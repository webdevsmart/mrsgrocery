<?php
class ControllerCustomerReview extends Controller {
	public $data;

	public function __construct($registry) {
		parent::__construct($registry);

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', 'SSL');
			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}

		$this->data = array_merge($this->load->language('multiseller/multiseller'), $this->load->language('account/account'));

		if (!isset($this->session->data['multiseller']['files']))
			$this->session->data['multiseller']['files'] = array();
	}

	private function _initForm() {
		// Script and style for star ratings
		$this->MsLoader->MsHelper->addStyle('star-rating');
		$this->document->addScript('catalog/view/javascript/star-rating.js');
		$this->document->addScript('catalog/view/javascript/multimerch/account-customer-product-review.js');
		$this->document->addScript('catalog/view/javascript/plupload/plupload.js');
		$this->document->addScript('catalog/view/javascript/plupload/plupload.html5.js');
		$this->document->addScript('catalog/view/javascript/ms-common.js');

		$order_id = isset($this->request->get['order_id']) ? $this->request->get['order_id'] : 0;
		$product_id = isset($this->request->get['product_id']) ? $this->request->get['product_id'] : 0;
		$order_product_id = isset($this->request->get['order_product_id']) ? $this->request->get['order_product_id'] : 0;

		if(!$this->config->get('msconf_reviews_enable') || !$this->MsLoader->MsOrderData->isOrderCreatedByCustomer($order_id, $this->customer->getId())) {
			$this->response->redirect($this->url->link('account/order', '', 'SSL'));
			return;
		}

		$this->document->setTitle($this->language->get('ms_customer_product_rate_heading'));

		$this->data['breadcrumbs'] = array(
			array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			),
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL')
			),
			array(
				'text' => $this->language->get('ms_account_order_history'),
				'href' => $this->url->link('account/order', '', 'SSL')
			),
			array(
				'text' => $this->language->get('ms_order_feedback'),
				'href' => $this->url->link('customer/review/create', 'product_id=' . $product_id . '&order_id=' . $order_id . '&order_product_id=' . $order_product_id, 'SSL')
			)
		);

		$this->load->model('account/order');
		$this->load->model('tool/image');
		$this->load->model('catalog/product');

		$this->data['order_id'] = $order_id;
		$this->data['product_id'] = $product_id;
		$this->data['order_product_id'] = $order_product_id;

		$products = $this->model_account_order->getOrderProducts($order_id);
		$order_products_ids = array();

		foreach($products as &$product) {
			$order_products_ids[] = $product['product_id'];

			$product['options'] = $this->model_account_order->getOrderOptions($product['order_id'], $product['order_product_id']);
			$product['seller'] = $this->MsLoader->MsSeller->getSeller($this->MsLoader->MsProduct->getSellerId($product['product_id']));
			$product['price'] = $this->currency->format($this->tax->calculate($product['price'], $this->config->get('config_tax')));
			$product['product'] = $this->model_catalog_product->getProduct($product['product_id']);
			$product['product']['image'] = $this->model_tool_image->resize($product['product']['image'] ?: 'no_image.png', $this->config->get('msconf_seller_avatar_seller_profile_image_width'), $this->config->get('msconf_seller_avatar_seller_profile_image_height'));
			$product['review'] = $this->MsLoader->MsReview->getReviews(array('order_product_id' => $product['order_product_id'], 'author_id' => $this->customer->getId()));
		}

		if(!in_array($product_id, $order_products_ids)) {
			$this->response->redirect($this->url->link('account/order', '', 'SSL'));
			return;
		}

		if ($this->MsLoader->MsReview->getReviews(array('product_id' => $product_id, 'author_id' => $this->customer->getId()))){
			$this->response->redirect($this->url->link('account/order', '', 'SSL'));
			return;
		}

		$this->data['products'] = $products;
	}

	public function create() {
		$this->_initForm();

		$this->data['review'] = false;

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('customer/review');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function update() {
		$this->_initForm();

		$review_id = $this->request->get('review_id');
		$this->data['review'] = $this->MsLoader->MsReview->getReviews(array('review_id' => $review_id));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('customer/review');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function jxSubmitReview() {
		$this->_validatePost();

		$serviceLocator = $this->MsLoader->load('\MultiMerch\Module\MultiMerch')->getServiceLocator();
		$mailTransport = $serviceLocator->get('MailTransport');
		$mails = new \MultiMerch\Mail\Message\MessageCollection();

		$review_data = array(
			'author_id' => $this->customer->getId(),
			'product_id' => $this->request->post['product_id'],
			'order_product_id' => $this->request->post['order_product_id'],
			'order_id' => $this->request->post['order_id'],
			'rating' => $this->request->post['rating'],
			'title' => '',
			'comment' => $this->request->post['rating_comment']
		);

		$review_id = isset($this->request->post['review_id']) && $this->request->post['review_id'] ? $this->request->post['review_id'] : false;
		if($review_id) {
			// Update existing review
			$review_data = array_merge($review_data, array('review_id' => $review_id));
		} else {
			// Create new review and send mails
			$product = $this->MsLoader->MsProduct->getProduct($review_data['product_id']);
			$product['href'] = $this->url->link('product/product', 'product_id=' . $review_data['product_id']);

			$MailProductReviewedAdmin = $serviceLocator->get('MailProductReviewed', false)
				->setTo($this->config->get('config_email'))
				->setData(array(
					'product_name' => $product['languages'][$this->config->get('config_language_id')]['name'],
					'product_href' => $product['href']
				));
			$mails->add($MailProductReviewedAdmin);

			if($product['seller_id']) {
				$seller = $this->MsLoader->MsSeller->getSeller($product['seller_id']);
				$review_data['seller_id'] = $product['seller_id'];
				$MailProductReviewedSeller = clone $MailProductReviewedAdmin;
				$MailProductReviewedSeller->setTo($seller['c.email']);
				$mails->add($MailProductReviewedSeller);
			}
		}

		if(!empty($this->request->post['images'])) {
			$review_data = array_merge($review_data, array('attachments' => $this->request->post['images']));
		}

		$this->MsLoader->MsReview->createOrUpdateReview($review_data);

		if ($mails->count()) {
			$mailTransport->sendMails($mails);
		}

		$this->session->data['success'] = $this->language->get('mm_review_submit_success');
		$this->response->redirect($this->url->link('account/order', '', 'SSL'));
	}

	public function jxAddUpload() {
		$json = array();
		$json['errors'] = $this->MsLoader->MsFile->checkPostMax($_POST, $_FILES);

		if ($json['errors']) {
			return $this->response->setOutput(json_encode($json));
		}

		// allow a maximum of N images
		$msconf_images_limits = $this->config->get('msconf_images_limits');
		foreach ($_FILES as $file) {
			if ($msconf_images_limits[1] > 0 && $this->request->post['imageCount'] > $msconf_images_limits[1]) {
				$json['errors'][] = sprintf($this->language->get('ms_error_product_image_maximum'),$msconf_images_limits[1]);
				$json['cancel'] = 1;
				return $this->response->setOutput(json_encode($json));
			} else {
				$errors = $this->MsLoader->MsFile->checkImage($file);

				if ($errors) {
					$json['errors'] = array_merge($json['errors'], $errors);
				} else {
					$fileName = $this->MsLoader->MsFile->uploadImage($file);

					$thumbUrl = $this->MsLoader->MsFile->resizeImage($this->config->get('msconf_temp_image_path') . $fileName, $this->config->get('msconf_preview_product_image_width'), $this->config->get('msconf_preview_product_image_height'));
					$json['files'][] = array(
						'name' => $fileName,
						'thumb' => $thumbUrl
					);
				}
			}
		}

		return $this->response->setOutput(json_encode($json));
	}

	protected function _validatePost() {
		if(empty($this->request->post)) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', 'SSL');
			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		}
	}
}