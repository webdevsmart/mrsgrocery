<?php
class ControllerMultimerchProductReview extends Controller {
	private $data;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->load->model('account/customer');
		$this->load->model('tool/image');

		$this->data = array_merge(!empty($this->data) ? $this->data : array(), $this->load->language('multiseller/multiseller'));
	}

	public function index() {
		$this->data['reviews'] = $rating_stats = array();
		$this->data['total_reviews'] = $this->data['rating_stats'] = $this->data['avg_rating'] = 0;

		for($i = 1; $i <= 5; $i++) {
			$rating_stats[$i] = array('votes' => 0, 'percentage' => 0);
		}

		$sum_rating = 0;

		$reviews = $this->MsLoader->MsReview->getReviews(array('product_id' => $this->request->get['product_id']));
		$total_reviews = isset($reviews[0]) ? $reviews[0]['total_rows'] : 0;

		foreach ($reviews as &$review) {
			$sum_rating += $review['rating'];

			$rating_stats[$review['rating']]['votes'] += 1;

			$review['author'] = $this->model_account_customer->getCustomer($review['author_id']);
			$review['date_created'] = date($this->language->get('date_format_short'), strtotime($review['date_created']));

			foreach ($review['attachments'] as &$attachment) {
				$attachment['fullsize'] = $this->model_tool_image->resize($attachment['attachment'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'));
				$attachment['thumb'] = $this->model_tool_image->resize($attachment['attachment'], 70, 70);
			}

			$comments = $this->MsLoader->MsReview->getReviewComments($review['review_id']);
			$review['total_comments'] = isset($comments[0]) ? $comments[0]['total_rows'] : 0;
		}

		foreach ($rating_stats as &$rating) {
			$rating['percentage'] = $total_reviews > 0 ? round($rating['votes'] / $total_reviews * 100, 1) : 0;
		}
		krsort($rating_stats);

		$this->data['reviews'] = $reviews;
		$this->data['total_reviews'] = $total_reviews;
		$this->data['rating_stats'] = $rating_stats;
		$this->data['avg_rating'] = $total_reviews > 0 ? round($sum_rating / $total_reviews, 1) : 0;

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('product/mm_review');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function jxGetReviewComments() {
		$json = array();

		if(!isset($this->request->get['review_id']))
			$json['errors'][] = $this->language->get('mm_review_signin');

		if(!empty($json['errors'])) {
			$this->response->setOutput(json_encode($json));
			return;
		}

		$this->data['is_logged'] = $this->customer->isLogged();
		$this->data['review'] = $this->MsLoader->MsReview->getReviews(array('review_id' => $this->request->get['review_id'], 'single' => 1));
		$this->data['comments'] = $this->MsLoader->MsReview->getReviewComments($this->request->get['review_id']);

		if(!empty($this->data['comments'])) {
			$this->load->model('account/customer');
			foreach ($this->data['comments'] as &$comment) {
				$customer = $this->model_account_customer->getCustomer($comment['author_id']);
				$comment['author'] = !empty($customer) ? $customer['firstname'] . ' ' . $customer['lastname'] : $this->language->get('ms_questions_customer_deleted');
			}
		}

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('product/mm_review_comment');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function jxAddReviewComment() {
		$json = array();

		if(!isset($this->request->post['review_id']) || !isset($this->request->post['text']))
			$json['errors'][] = $this->language->get('mm_review_comments_error_review_id');

		if(!$this->request->post['text'])
			$json['errors'][] = $this->language->get('mm_review_comments_error_notext');

		if(!empty($json['errors'])) {
			$this->response->setOutput(json_encode($json));
			return;
		}

		$review = $this->MsLoader->MsReview->getReviews(
			array(
				'review_id' => $this->request->post['review_id'],
				'single' => 1
			)
		);

		//check product owner and count comments
		if ($this->MsLoader->MsProduct->productOwnedBySeller($review['product_id'], $this->customer->getId()) && !$this->MsLoader->MsReview->getReviewComments($this->request->post['review_id'])) {
			$data = array_merge($this->request->post, array('author_id' => $this->customer->getId()));

			$this->MsLoader->MsReview->addReviewComment($data);

			$json['success'] = $this->language->get('mm_review_comments_success_added');
		}

		$this->response->setOutput(json_encode($json));
	}
}