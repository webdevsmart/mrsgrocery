<?php

class ControllerMultimerchImport extends ControllerMultimerchBase {
	private $error = array();

	public function __construct($registry) {
		parent::__construct($registry);
	}
	
	public function getTableData() {
		$colMap = array(
			'seller' => 'ms.nickname',
			'date' => 'date_added'
		);
		$sorts = array('date','name','seller','type');
		$filters = $sorts;
		
		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsImportExportData->getImportHistory(
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
			// actions
			$actions = "";
			$actions .= "<a class='btn btn-danger ms-delete-import' href='" . $this->url->link('multimerch/import/delete', 'token=' . $this->session->data['token'] . '&import_id=' . $result['import_id'], 'SSL') . "' title='".$this->language->get('button_delete')."'><i class='fa fa-trash-o''></i></a>";
			
			$columns[] = array_merge(
				$result,
				array(
					'checkbox'          => "<input type='checkbox' name='selected[]' value='{$result['import_id']}' />",
					'name'              => $result['name'],
					'seller'              => $result['nickname'],
					'date'              => $result['date_added'],
					'type'              => $result['type'],
					'processed'              => $result['processed'],
					'added'              => $result['added'],
					'updated'              => $result['updated'],
					'errors'              => $result['errors'],
					'actions' 				=> $actions
				)
			);
		}

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}
	
	// List all the seller imports
	public function index() {
		$this->validate(__FUNCTION__);

		if (!$this->config->get('msconf_import_enable')) {
			$this->response->redirect($this->url->link('module/multimerch', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_catalog_imports_breadcrumbs'),
				'href' => $this->url->link('multimerch/import', '', 'SSL'),
			)
		));

		$this->data['delete'] = $this->url->link('multimerch/import/delete', 'token=' . $this->session->data['token'], 'SSL');
	
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
		$this->data['heading'] = $this->language->get('ms_catalog_imports_heading');
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		
		$this->document->setTitle($this->language->get('ms_catalog_imports_heading'));
		
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/import-list.tpl', $this->data));
	}
	
	// Bulk delete of import history
	public function delete() {
		if (!$this->config->get('msconf_import_enable')) {
			$this->response->redirect($this->url->link('module/multimerch', 'token=' . $this->session->data['token'], 'SSL'));
		}
		if (isset($this->request->get['import_id'])) $this->request->post['selected'] = array($this->request->get['import_id']);
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $import_id) {
				$this->MsLoader->MsImportExportData->deleteImport($import_id);
			}
			$this->session->data['success'] = $this->language->get('ms_success');
		}
		$this->response->redirect($this->url->link('multimerch/import', 'token=' . $this->session->data['token'], 'SSL'));
	}
	
	// Validate delete of the import history
	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'multimerch/import')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
      	
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>
