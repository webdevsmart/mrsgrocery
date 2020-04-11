<?php

class ControllerSellerAccountReview extends ControllerSellerAccount {

	public function index() {
		$this->_validateCall();

		$this->document->addScript('catalog/view/javascript/multimerch/account-review.js');
		$this->document->setTitle($this->language->get('ms_account_review_breadcrumbs'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_review_breadcrumbs'),
				'href' => $this->url->link('seller/account-review', '', 'SSL'),
			)
		));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-review');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	private function _validateCall() {
		if(!$this->config->get('msconf_reviews_enable')) {
			return $this->response->redirect($this->url->link('account/account', '', 'SSL'));
		}
	}

	public function getTableData() {
		$this->_validateCall();

		$colMap = array(
			'product_name' => 'pd.name',
			'rating' => 'msr.rating',
			'comment' => 'msr.comment',
			'date_created' => 'msr.date_created',
			'status' => 'msr.status'
		);

		$sorts = array('product_name', 'rating', 'date_created', 'status');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsReview->getReviews(
			array(
				'seller_id' => $this->customer->getId()
			),
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

				$status .= "'>" . $this->language->get('ms_seller_review_status_' . $result['status']) . "</p>";
			}

			// actions
			$actions = "";
			$actions .= "<a class='icon-view' href='" . $this->url->link('seller/account-review/update', 'review_id=' . $result['review_id'], 'SSL') ."' title='" . $this->language->get('ms_edit') . "'><i class='fa fa-search'></i></a>";

			$columns[] = array_merge(
				$result,
				array(
					'product_name' => '<a href="' . $this->url->link('product/product', 'product_id=' . $result['product_id']) . '" target="_blank">' . (mb_strlen($result['product_name']) > 20 ? mb_substr($result['product_name'], 0, 20) . '...' : $result['product_name']) . '</a>',
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

		$this->document->addScript('catalog/view/javascript/multimerch/account-review.js');
		$this->MsLoader->MsHelper->addStyle('pagination');
		$this->document->addScript('catalog/view/javascript/pagination.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');

		$this->data['back'] = $this->url->link('seller/account-review', '', 'SSL');

		// Title and friends
		$this->document->setTitle($this->language->get('ms_account_review_breadcrumbs'));
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_dashboard_breadcrumbs'),
				'href' => $this->url->link('seller/account-dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_review_breadcrumbs'),
				'href' => $this->url->link('seller/account-review', '', 'SSL'),
			)
		));
	}

	public function update() {
		$this->_validateCall();

		$review_id = isset($this->request->get['review_id']) ? (int)$this->request->get['review_id'] : 0;
		$review = $this->MsLoader->MsReview->getReviews(array('review_id' => $review_id, 'single' => 1));

		if(!$this->MsLoader->MsProduct->productOwnedBySeller($review['product_id'], $this->customer->getId()))
			return $this->response->redirect($this->url->link('account/account', '', 'SSL'));

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

		$this->load->model('account/customer');
		$customer = $this->model_account_customer->getCustomer($review['author_id']);
		$this->data['review_customer'] = $customer['firstname'];

		$this->data['review'] = $review;

		$this->data['heading'] = $this->language->get('ms_account_editreview_heading');
		$this->document->setTitle($this->language->get('ms_account_editreview_heading'));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-review-form');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function delete() {
		$this->_validateCall();

		$json = array();
		$review_id = isset($this->request->get['review_id']) ? (int)$this->request->get['review_id'] : 0;

		if($review_id) {
			$this->MsLoader->MsReview->deleteReview($review_id);
			$this->session->data['success'] = $this->language->get('ms_success_review_deleted');
		} else {
			$json['error'] = $this->language->get('ms_error_review_id');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
