<?php

class ControllerSellerAccountQuestion extends ControllerSellerAccount {

	public function index() {
		$this->_validateCall();

		$this->document->addScript('catalog/view/javascript/multimerch/account-question.js');
		$this->document->setTitle($this->language->get('ms_account_question_breadcrumbs'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_question_breadcrumbs'),
				'href' => $this->url->link('seller/account-question', '', 'SSL'),
			)
		));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-question');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	private function _validateCall() {
		if(!$this->config->get('msconf_allow_questions')) {
			return $this->response->redirect($this->url->link('account/account', '', 'SSL'));
		}
	}

	public function getTableData() {
		$this->_validateCall();

		$colMap = array(
			'product_name' => 'pd.name',
			'customer' => 'msq.author_id',
			'answer' => 'msq.answer',
			'date_created' => 'msq.date_created'
		);

		$sorts = array('product_name', 'customer', 'answer', 'date_created');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsQuestion->getQuestions(
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
			// answers - show last answer
			$answer = !empty($result['answers']) ? $result['answers'][0]['text'] : $this->language->get('ms_account_question_no_answers');

			// actions
			$actions = "";
			$actions .= "<a class='icon-view' href='" . $this->url->link('seller/account-question/update', 'question_id=' . $result['question_id'], 'SSL') ."' title='" . $this->language->get('ms_view') . "'><i class='fa fa-search'></i></a>";

			$columns[] = array_merge(
				$result,
				array(
					'product_name' => '<a href="' . $this->url->link('product/product', 'product_id=' . $result['product_id']) . '" target="_blank">' . (mb_strlen($result['product_name']) > 20 ? mb_substr($result['product_name'], 0, 20) . '...' : $result['product_name']) . '</a>',
					'customer' => $result['author_name'],
					'answer' => (mb_strlen($answer) > 50 ? mb_substr($answer, 0, 50) . '...' : $answer),
					'date_created' => date($this->language->get('date_format_short'), strtotime($result['date_created'])),
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

	private function _initQuestionForm() {
		$this->_validateCall();

		$this->document->addScript('catalog/view/javascript/multimerch/account-question.js');
		$this->MsLoader->MsHelper->addStyle('pagination');
		$this->document->addScript('catalog/view/javascript/pagination.min.js');

		$this->data['back'] = $this->url->link('seller/account-question', '', 'SSL');

		// Title and friends
		$this->document->setTitle($this->language->get('ms_account_question_breadcrumbs'));
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
				'text' => $this->language->get('ms_account_question_breadcrumbs'),
				'href' => $this->url->link('seller/account-question', '', 'SSL'),
			)
		));
	}

	public function update() {
		$this->_validateCall();

		$question_id = isset($this->request->get['question_id']) ? (int)$this->request->get['question_id'] : 0;
		$question = $this->MsLoader->MsQuestion->getQuestions(array('question_id' => $question_id, 'single' => 1));

		if(!$this->MsLoader->MsProduct->productOwnedBySeller($question['product_id'], $this->customer->getId()))
			return $this->response->redirect($this->url->link('account/account', '', 'SSL'));

		$this->_initQuestionForm();

		$this->data['question'] = $question;

		$this->data['heading'] = $this->language->get('ms_account_editquestion_heading');
		$this->document->setTitle($this->language->get('ms_account_editquestion_heading'));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-question-form');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function delete() {
		$this->_validateCall();

		$json = array();
		$question_id = isset($this->request->get['question_id']) ? (int)$this->request->get['question_id'] : 0;

		if($question_id) {
			$this->MsLoader->MsQuestion->deleteQuestion($question_id);
			$this->session->data['success'] = $this->language->get('ms_success_question_deleted');
		} else {
			$json['error'] = $this->language->get('ms_error_question_id');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
