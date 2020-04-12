<?php

class ControllerMultimerchReview extends ControllerMultimerchBase {

	public function index() {
		$this->_validateCall();

		$this->document->addScript('view/javascript/multimerch/review.js');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['token'] = $this->session->data['token'];

		$this->data['heading'] = $this->language->get('ms_review_heading');
		$this->document->setTitle($this->language->get('ms_review_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_review_breadcrumbs'),
				'href' => $this->url->link('multimerch/review', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['column_right'] = $this->load->controller('common/column_right');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('multiseller/review', $this->data));
	}

	private function _validateCall() {
		if(!$this->config->get('msconf_reviews_enable')) {
			return $this->response->redirect($this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
		}
	}

	/************************************************************/


	public function getTableData() {
		$this->_validateCall();

		$colMap = array(
			'product_name' => 'pd.name',
			'customer' => 'msr.author_id',
			'nickname' => 'mss.nickname',
			'order' => 'msr.order_id',
			'rating' => 'msr.rating',
			'comment' => 'msr.comment',
			'date_created' => 'msr.date_created',
			'status' => 'msr.status'
		);

		$sorts = array('product_name', 'customer', 'nickname', 'order', 'rating', 'date_created', 'status');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsReview->getReviews(
			array(),
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				'filters' => $filterParams,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength']
			)
		);

		$total = isset($results[0]) ? $results[0]['total_rows'] : 0;

		$columns = array();

		foreach ($results as $result) {
			// customer
			$this->load->model('customer/customer');
			$customer = $this->model_customer_customer->getCustomer($result['author_id']);
			$author = '<a href="' . $this->url->link('customer/customer/edit', 'customer_id=' . $result['author_id']) . '&token=' . $this->session->data['token'] . '" target="_blank">' . (!empty($customer) ? $customer['firstname'] . ' ' . $customer['lastname'] : $this->language->get('ms_questions_customer_deleted')) . '</a>';

			// rating
			$rating = "";
			if(isset($result['rating'])) {
				$rating .= '<div class="ms-ratings comments">';
				$rating .= '	<div class="ms-empty-stars"></div>';
				$rating .= '	<div class="ms-full-stars" style="width: ' . $result['rating'] * 20 . '%;"></div>';
				$rating .= '</div>';
			}

			// status
			$status = "";
			if(isset($result['status'])) {
				$status .= "<p style='color: ";

				if($result['status'] == MsReview::STATUS_ACTIVE) $status .= "green";
				if($result['status'] == MsReview::STATUS_INACTIVE) $status .= "red";

				$status .= "'>" . $this->language->get('ms_review_status_' . $result['status']) . "</p>";
			}

			// actions
			$actions = "";
			$actions .= "<a class='btn btn-info' href='" . $this->url->link('multimerch/review/update', 'token=' . $this->session->data['token'] . '&review_id=' . $result['review_id'], 'SSL') . "' title='".$this->language->get('ms_view')."'><i class='fa fa-search''></i></a>";
			$actions .= "<a class='btn btn-danger ms-delete' title='".$this->language->get('ms_delete')."'  data-id='" . $result['review_id'] . "' data-referrer='review'><i class='fa fa-trash-o''></i></a>";

			$columns[] = array_merge(
				$result,
				array(
					'product_name' => '<input type="hidden" value="' . $result['review_id'] . '" /><a href="' . $this->url->link('catalog/product/edit', 'product_id=' . $result['product_id']) . '&token=' . $this->session->data['token'] . '" target="_blank">' . (mb_strlen($result['product_name']) > 20 ? mb_substr($result['product_name'], 0, 20) . '...' : $result['product_name']) . '</a>',
					'customer' => $author,
					'nickname' => '<a href="' . $this->url->link('multimerch/seller/update', 'seller_id=' . $result['seller_id']) . '&token=' . $this->session->data['token'] . '" target="_blank"> #' . $result['nickname'] . '</a>',
					'order' => '<a href="' . $this->url->link('sale/order/info', 'order_id=' . $result['order_id']) . '&token=' . $this->session->data['token'] . '" target="_blank"> #' . $result['order_id'] . '</a>',
					'rating' => $rating,
					'comment' => (mb_strlen($result['comment']) > 40 ? mb_substr($result['comment'], 0, 40) . '...' : $result['comment']),
					'date_created' => date($this->language->get('date_format_short'), strtotime($result['date_created'])),
					'status' => $status,
					'actions' => $actions
				)
			);
		}

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}

	private function _initReviewForm() {
		$this->_validateCall();

		$this->document->addScript('view/javascript/multimerch/review.js');

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_review_breadcrumbs'),
				'href' => $this->url->link('multimerch/review', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['column_right'] = $this->load->controller('common/column_right');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
	}

	public function update() {
		$this->_validateCall();

		$review_id = isset($this->request->get['review_id']) ? (int)$this->request->get['review_id'] : 0;
		$review = $this->MsLoader->MsReview->getReviews(array('review_id' => $review_id, 'single' => 1));

		$this->document->setTitle($this->language->get('ms_review_edit_heading'));

		$this->_initReviewForm();

		$this->load->model('tool/image');
		foreach ($review['attachments'] as &$attachment) {
			$attachment['fullsize'] = $this->model_tool_image->resize($attachment['attachment'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'));
			$attachment['thumb'] = $this->model_tool_image->resize($attachment['attachment'], 70, 70);
		}

		$suborder = $this->MsLoader->MsSuborder->getSuborders(array(
			'order_id' => $review['order_id'],
			'seller_id' => $this->customer->getId(),
			'single' => 1
		));

		$this->data['full_order_id'] = $review['order_id'] . (isset($suborder['suborder_id']) ? '-' . $suborder['suborder_id'] : '');

		$review['comments'] = $this->MsLoader->MsReview->getReviewComments($review_id);

		if(!empty($review['comments'])) {
			$this->load->model('customer/customer');
			foreach ($review['comments'] as &$comment) {
				$customer = $this->model_customer_customer->getCustomer($comment['author_id']);
				$comment['author'] = !empty($customer) ? $customer['firstname'] . ' ' . $customer['lastname'] : $this->language->get('ms_questions_customer_deleted');
			}
		}

		$this->load->model('customer/customer');
		$customer = $this->model_customer_customer->getCustomer($review['author_id']);
		$this->data['customer'] = !empty($customer) ? $customer['firstname'] . ' ' . $customer['lastname'] : $this->language->get('ms_questions_customer_deleted');

		$this->data['review'] = $review;
		$this->data['heading'] = $this->language->get('ms_review_edit_heading');

		$this->response->setOutput($this->load->view('multiseller/review-form', $this->data));
	}

	public function delete() {
		$this->_validateCall();

		$json = array();

		if(!isset($this->request->get['question_id']) && !isset($this->request->post['selected'])) {
			$json['errors'][] = $this->language->get('ms_error_review_deleting');
		}

		if(!isset($json['errors'])) {
			$review_ids = isset($this->request->get['review_id']) ?
				array($this->request->get['review_id']) :
				(isset($this->request->post['selected']) ? $this->request->post['selected'] : array());

			foreach ($review_ids as $review_id) {
				$this->MsLoader->MsReview->deleteReview($review_id);
			}

			$this->session->data['success'] =  $this->language->get('ms_success_review_deleted');
			$json['redirect'] = $this->url->link('multimerch/review', 'token=' . $this->session->data['token'], true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxDownloadAttachment() {
		if(!isset($this->request->get['filename'])) return;

		$file = DIR_IMAGE . $this->request->get['filename'];

		if (!headers_sent()) {
			if (is_file($file)) {
				header('Content-Type: application/octet-stream');
				header('Content-Description: File Transfer');
				header('Content-Disposition: attachment; filename="' . basename($file) . '"');
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));

				readfile($file, 'rb');
				exit;
			} else {
				exit('Error: Could not find file ' . $file . '!');
			}
		} else {
			exit('Error: Headers already sent out!');
		}
	}

	public function jxDeleteAttachment() {
		$this->_validateCall();

		$json = array();

		if(!isset($this->request->get['review_attachment_id']))
			$json['errors'][] = $this->language->get('ms_error_review_id');

		if (!empty($json['errors'])) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}

		$review_attachments = $this->MsLoader->MsReview->getReviewAttachments(array('review_attachment_id' => $this->request->get['review_attachment_id']));

		foreach ($review_attachments as $attachment) {
			if((int)$attachment['review_attachment_id'] == (int)$this->request->get['review_attachment_id'] && file_exists(DIR_IMAGE. $attachment['attachment'])) {
				unlink(DIR_IMAGE. $attachment['attachment']);
			}
		}

		$this->MsLoader->MsReview->deleteReviewAttachment($this->request->get['review_attachment_id']);
		$json['success'] = 'Success!';

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxDeleteComment() {
		$this->_validateCall();

		$json = array();

		if(!isset($this->request->get['comment_id']))
			$json['errors'][] = $this->language->get('ms_error_review_id');

		if (!empty($json['errors'])) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}

		$this->MsLoader->MsReview->deleteReviewComment($this->request->get['comment_id']);
		$json['success'] = 'Success!';

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
