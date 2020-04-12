<?php

class ControllerMultimerchProductQuestion extends Controller {
	private $data;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->load->model('account/customer');

		$this->data = array_merge(!empty($this->data) ? $this->data : array(), $this->load->language('multiseller/multiseller'));
	}

	public function index() {
		$product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
		$this->data['product_id'] = $product_id;

		$questions = $this->MsLoader->MsQuestion->getQuestions(array('product_id' => $product_id));

		$this->data['questions'] = $questions;

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('product/mm_question');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}
	
	public function jxAddQuestion() {
		$json = array();
		$data = $this->request->post;

		$data['author_id'] = $this->customer->getId();

		$validator = $this->MsLoader->MsValidator;

		if (!$validator->validate(array(
			'name' => 'question',
			'value' => $data['question']
		),
			array(
				array('rule' => 'required'),
				array('rule' => 'min_len,10'),
				array('rule' => 'max_len,200')
			)
		)) $json['errors'] = $validator->get_errors();
		
		if(empty($json['errors'])) {
			$this->MsLoader->MsQuestion->addQuestion($data);
			$json['success'] = $this->language->get('ms_success_question_submitted');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxGetQuestions() {
		$json = array();

		if(!isset($this->request->get['question_id']))
			$json['errors'][] = $this->language->get('mm_question_signin');

		if(!empty($json['errors'])) {
			$this->response->setOutput(json_encode($json));
			return;
		}

		$this->data['is_logged'] = $this->customer->isLogged();
		$this->data['questions'] = $this->MsLoader->MsQuestion->getQuestions(array('product_id' => $this->request->get['product_id']));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('product/mm_question_answer');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function jxAddAnswer() {
		$json = array();

		if(!isset($this->request->post['question_id']) || !isset($this->request->post['text']))
			$json['errors'][] = $this->language->get('ms_error_question_id');

		if(!$this->request->post['text'])
			$json['errors'][] = $this->language->get('ms_error_question_text');

		if(!empty($json['errors'])) {
			$this->response->setOutput(json_encode($json));
			return;
		}

		$question = $this->MsLoader->MsQuestion->getQuestions(
			array(
				'question_id' => $this->request->post['question_id'],
				'single' => 1
			)
		);

		//check product owner and count answers
		if ($this->MsLoader->MsProduct->productOwnedBySeller($question['product_id'], $this->customer->getId()) && !$question['answers']) {
			$data = array_merge($this->request->post, array('author_id' => $this->customer->getId()));

			$this->MsLoader->MsQuestion->addAnswer($data);

			$json['success'] = $this->language->get('ms_success_question_answered');
		}

		$this->response->setOutput(json_encode($json));
	}

	public function jxGetAnswers() {
		$json = array();

		if(!isset($this->request->get['question_id']))
			$json['errors'][] = $this->language->get('mm_question_signin');

		if(!empty($json['errors'])) {
			$this->response->setOutput(json_encode($json));
			return;
		}

		$this->data['is_logged'] = $this->customer->isLogged();
		$this->data['question'] = $this->MsLoader->MsQuestion->getQuestions(array('question_id' => $this->request->get['question_id'], 'single' => 1));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('product/mm_question_answer');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}
}