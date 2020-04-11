<?php

class ControllerMultimerchSuborderStatus extends ControllerMultimerchBase {
	private $error = array();

	public function __construct($registry) {
		parent::__construct($registry);
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
	}

	public function getTableData() {
		$colMap = array(
			'id' => 's.ms_suborder_status_id',
			'name' => 'sd.name'
		);

		$sorts = array('name');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsSuborderStatus->getMsSuborderStatuses(
			array(
				'language_id' => $this->config->get('config_language_id')
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
			// actions
			$actions = "";
			$actions .= "<a class='btn btn-primary' href='" . $this->url->link('multimerch/suborder-status/edit', 'token=' . $this->session->data['token'] . '&ms_suborder_status_id=' . $result['ms_suborder_status_id'], 'SSL') . "' title='".$this->language->get('button_edit')."'><i class='fa fa-pencil''></i></a>";

			if($this->MsLoader->MsSuborderStatus->getSuborderStateByStatusId($result['ms_suborder_status_id']) || !empty($this->MsLoader->MsSuborder->getSuborders(array('order_status_id' => $result['ms_suborder_status_id'])))) {
				$actions .= "<a class='btn btn-danger' title='".$this->language->get('ms_suborder_status_info_disabled_delete')."' disabled='disabled'><i class='fa fa-trash-o'></i></a>";
			} else {
				$actions .= "<a class='btn btn-danger' href='" . $this->url->link('multimerch/suborder-status/delete', 'token=' . $this->session->data['token'] . '&ms_suborder_status_id=' . $result['ms_suborder_status_id'], 'SSL') . "' title='".$this->language->get('button_delete')."'><i class='fa fa-trash-o'></i></a>";
			}

			$columns[] = array_merge(
				$result,
				array(
					'name' => $result['name'],
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

	public function jxSaveSuborderStatus() {
		$data = $this->request->post['suborder_status'];
		$json = array();
		foreach ($data['description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 32)) {
				$json['errors']['suborder_status[description][' . $language_id . '][name]'] = $this->language->get('ms_suborder_status_name_error');
			}
		}

		if (empty($json['errors'])) {
			if (!$data['ms_suborder_status_id']) {
				$this->MsLoader->MsSuborderStatus->addMsSuborderStatus($data);
				$this->session->data['success'] = $this->data['ms_suborder_status_add_success'];
			} else {
				$this->MsLoader->MsSuborderStatus->editMsSuborderStatus($data);
				$this->session->data['success'] = $this->data['ms_suborder_status_edit_success'];
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function index() {

		$this->document->addScript('//code.jquery.com/ui/1.11.2/jquery-ui.min.js');
		$this->validate(__FUNCTION__);
		$this->data['add'] = $this->url->link('multimerch/suborder-status/add', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['delete'] = $this->url->link('multimerch/suborder-status/add', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->session->data['error'])) {
			$this->data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
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
		$this->data['heading'] = $this->language->get('ms_suborder_status_heading');
		$this->document->setTitle($this->language->get('ms_suborder_status_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_suborder_status_breadcrumbs'),
				'href' => $this->url->link('multimerch/suborder-status', '', 'SSL'),
			)
		));
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/suborder-status.tpl', $this->data));
	}

	public function add() {
		$this->_initForm();
	}

	public function edit() {
		$this->_initForm();
	}

	public function delete() {
		$ms_suborder_status_ids = array();
		if(isset($this->request->post['selected'])){
			$ms_suborder_status_ids = $this->request->post['selected'];
		} else if($this->request->get['ms_suborder_status_id']) {
			$ms_suborder_status_ids = array($this->request->get['ms_suborder_status_id']);
		}
		foreach($ms_suborder_status_ids as $ms_suborder_status_id) {
			$this->MsLoader->MsSuborderStatus->deleteMsSuborderStatus($ms_suborder_status_id);
		}

		$this->session->data['success'] = $this->data['ms_suborder_status_delete_success'];
		$this->response->redirect($this->url->link('multimerch/suborder-status', 'token=' . $this->session->data['token'], 'SSL'));
	}

	private function _initForm() {
		$this->data['token'] = $this->session->data['token'];
		$this->data['cancel'] = $this->url->link('multimerch/suborder-status', 'token=' . $this->session->data['token'], 'SSL');

		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		if(isset($this->request->get['ms_suborder_status_id'])) {
			$this->data['heading'] = $this->language->get('ms_suborder_status_edit_heading');
			$this->document->setTitle($this->language->get('ms_suborder_status_edit_heading'));

			$this->data['suborder_status'] = $this->MsLoader->MsSuborderStatus->getMsSuborderStatuses(
				array('ms_suborder_status_id' => $this->request->get['ms_suborder_status_id'])
			);
		} else {
			$this->data['heading'] = $this->language->get('ms_suborder_status_add_heading');
			$this->document->setTitle($this->language->get('ms_suborder_status_add_heading'));
		}

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_suborder_status_breadcrumbs'),
				'href' => $this->url->link('multimerch/suborder-status', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/suborder-status-form.tpl', $this->data));
	}

	public function jxAutocompleteOrderStatus() {
		$json = array();

		if (isset($this->request->get['type'])) {
			$term = isset($this->request->get['term']) ? $this->request->get['term'] : '';

			if(isset($this->request->get['limit'])) {
				$filter_data_default = array(
					'offset' => 0,
					'limit' => $this->request->get['limit']
				);
			}

			if((string)$this->request->get['type'] === 'oc' || (string)$this->request->get['type'] === 'oc_ms') {
				$data = array();

				if(isset($this->request->get['selected_oc'])) {
					$data['status_id_exclude'] = $this->request->get['selected_oc'];
				}

				$filter_data_oc = array(
					'filters' => array(
						'os.name' => $term
					),
					'order_by' => 'os.name',
					'order_way' => 'ASC'
				);

				$filter_data = !empty($filter_data_default) ? array_merge($filter_data_default, $filter_data_oc) : $filter_data_oc;

				$results = $this->MsLoader->MsSuborderStatus->getOcOrderStatuses($data, $filter_data);

				foreach ($results as $result) {
					$json[] = array(
						'status_id' => $result['order_status_id'],
						'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
						'status_type' => $this->language->get('ms_config_order_statuses'),
						'status_type_code' => 'oc'
					);
				}

				$sort_order = array();

				foreach ($json as $key => $value) {
					$sort_order[$key] = $value['name'];
				}

				array_multisort($sort_order, SORT_ASC, $json);
			}

			if((string)$this->request->get['type'] === 'ms' || (string)$this->request->get['type'] === 'oc_ms') {
				$data = array('language_id' => $this->config->get('config_language_id'));

				if(isset($this->request->get['selected_ms'])) {
					$data['status_id_exclude'] = $this->request->get['selected_ms'];
				}

				$filter_data_ms = array(
					'filters' => array(
						'sd.name' => $term
					),
					'order_by' => 'sd.name',
					'order_way' => 'ASC'
				);

				$filter_data = !empty($filter_data_default) ? array_merge($filter_data_default, $filter_data_ms) : $filter_data_ms;

				$results = $this->MsLoader->MsSuborderStatus->getMsSuborderStatuses($data, $filter_data);

				foreach ($results as $result) {
					$json[] = array(
						'status_id' => $result['ms_suborder_status_id'],
						'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
						'status_type' => $this->language->get('ms_config_suborder_statuses'),
						'status_type_code' => 'ms'
					);
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
?>