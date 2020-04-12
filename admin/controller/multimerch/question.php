<?php

class ControllerMultimerchQuestion extends ControllerMultimerchBase {

	public function index() {
		$this->_validateCall();

		$this->document->addScript('view/javascript/multimerch/question.js');

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['token'] = $this->session->data['token'];

		$this->data['heading'] = $this->language->get('ms_question_heading');
		$this->document->setTitle($this->language->get('ms_question_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_question_breadcrumbs'),
				'href' => $this->url->link('multimerch/question', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['column_right'] = $this->load->controller('common/column_right');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('multiseller/question', $this->data));
	}

	private function _validateCall() {
		if(!$this->config->get('msconf_allow_questions')) {
			return $this->response->redirect($this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
		}
	}

	/************************************************************/


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

			// answers - show last answer
			$answer = !empty($result['answers']) ? $result['answers'][0]['text'] : $this->language->get('ms_question_no_answers');

			// actions
			$actions = "";
			$actions .= "<a class='btn btn-info' href='" . $this->url->link('multimerch/question/update', 'token=' . $this->session->data['token'] . '&question_id=' . $result['question_id'], 'SSL') . "' title='".$this->language->get('ms_view')."'><i class='fa fa-search''></i></a>";
			$actions .= "<a class='btn btn-danger ms-delete' title='".$this->language->get('ms_delete')."' data-id='" . $result['question_id'] . "' data-referrer='question'><i class='fa fa-trash-o''></i></a>";

			$columns[] = array_merge(
				$result,
				array(
					'product_name' => '<input type="hidden" value="' . $result['question_id'] . '" /><a href="' . $this->url->link('catalog/product/edit', 'product_id=' . $result['product_id']) . '&token=' . $this->session->data['token'] . '" target="_blank">' . (mb_strlen($result['product_name']) > 20 ? mb_substr($result['product_name'], 0, 20) . '...' : $result['product_name']) . '</a>',
					'customer' => $author,
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

		$this->document->addScript('view/javascript/multimerch/question.js');

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_question_breadcrumbs'),
				'href' => $this->url->link('multimerch/question', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['column_right'] = $this->load->controller('common/column_right');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
	}

	public function update() {
		$this->_validateCall();

		$question_id = isset($this->request->get['question_id']) ? (int)$this->request->get['question_id'] : 0;
		$question = $this->MsLoader->MsQuestion->getQuestions(array('question_id' => $question_id, 'single' => 1));

		$this->document->setTitle($this->language->get('ms_question_edit_heading'));

		$this->_initQuestionForm();

		$this->data['question'] = $question;
		$this->data['heading'] = $this->language->get('ms_question_edit_heading');

		$this->response->setOutput($this->load->view('multiseller/question-form', $this->data));
	}

	public function delete() {
		$this->_validateCall();

		$json = array();

		if(!isset($this->request->get['question_id']) && !isset($this->request->post['selected'])) {
			$json['errors'][] = $this->language->get('ms_error_question_deleting');
		}

		if(!isset($json['errors'])) {
			$question_ids = isset($this->request->get['question_id']) ?
				array($this->request->get['question_id']) :
				(isset($this->request->post['selected']) ? $this->request->post['selected'] : array());

			foreach ($question_ids as $question_id) {
				$this->MsLoader->MsQuestion->deleteQuestion($question_id);
			}

			$this->session->data['success'] =  $this->language->get('ms_success_question_deleted');
			$json['redirect'] = $this->url->link('multimerch/question', 'token=' . $this->session->data['token'], true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxDeleteAnswer() {
		$this->_validateCall();

		$json = array();

		if(!isset($this->request->get['answer_id']))
			$json['errors'][] = $this->language->get('ms_error_answer_id');

		if (!empty($json['errors'])) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}

		$this->MsLoader->MsQuestion->deleteAnswer($this->request->get['answer_id']);
		$json['success'] = 'Success!';

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
