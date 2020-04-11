<?php

class ControllerMultimerchCustomField extends ControllerMultimerchBase {

	public function index() {
		$this->validate(__FUNCTION__);

		$this->document->addScript('view/javascript/multimerch/custom-field.js');

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

		$this->data['heading'] = $this->language->get('ms_custom_field_heading');
		$this->document->setTitle($this->language->get('ms_custom_field_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_custom_field_breadcrumbs'),
				'href' => $this->url->link('multimerch/custom-field', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/custom-field', $this->data));
	}


	/* ====================================== CUSTOM FIELD GROUPS - CFG ============================================= */


	public function getCFGTableData() {
		$colMap = array(
			'id' => 'mscfg.custom_field_group_id',
			'name' => 'mscfgd.`name`',
			'status' => 'mscfg.status',
			'sort_order' => 'mscfg.sort_order'
		);

		$sorts = array('name', 'status', 'sort_order');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$statuses = array();
		$msCustomField = new ReflectionClass('MsCustomField');
		foreach ($msCustomField->getConstants() as $cname => $cval) {
			if (strpos($cname, 'STATUS_') !== FALSE) {
				$statuses[] = $cval;
			}
		}

		$results = $this->MsLoader->MsCustomField->getCustomFieldGroups(
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
			// status
			$status = "";
			if(isset($result['status'])) {
				$status .= "<p style='color: ";

				if($result['status'] == MsCustomField::STATUS_ACTIVE) $status .= "green";
				if($result['status'] == MsCustomField::STATUS_DISABLED) $status .= "red";

				$status .= "'>" . $this->language->get('ms_custom_field_status_' . $result['status']) . "</p>";
			}

			// actions
			$actions = "";
			$actions .= "<a class='btn btn-primary' href='" . $this->url->link('multimerch/custom-field/updateCFG', 'token=' . $this->session->data['token'] . '&custom_field_group_id=' . $result['custom_field_group_id'], 'SSL') . "' title='".$this->language->get('button_edit')."'><i class='fa fa-pencil''></i></a>";
			$actions .= "<button type='button' class='btn btn-danger ms-delete' title='".$this->language->get('button_delete')."' data-id='" . $result['custom_field_group_id'] . "' data-referrer='custom_field_group'><i class='fa fa-trash-o''></i></button>";

			$columns[] = array_merge(
				$result,
				array(
					'checkbox' => "<input type='checkbox' name='selected[]' value='{$result['custom_field_group_id']}' />",
					'name' => isset($result['languages'][$this->config->get('config_language_id')]['name']) ? $result['languages'][$this->config->get('config_language_id')]['name'] : '',
					'cf_count' => $result['cf_count'],
					'status' => $status,
					'sort_order' => $result['sort_order'],
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

	public function createCFG() {
		$this->document->setTitle($this->language->get('ms_custom_field_group_new_heading'));
		$this->_initCFGForm();

		$this->data['custom_field_group'] = FALSE;
		$this->data['heading'] = $this->language->get('ms_custom_field_group_new_heading');

		$this->response->setOutput($this->load->view('multiseller/custom-field-group-form', $this->data));
	}

	public function updateCFG() {
		$this->document->setTitle($this->language->get('ms_custom_field_group_edit_heading'));
		$this->_initCFGForm();

		$custom_field_group_id = isset($this->request->get['custom_field_group_id']) ? (int)$this->request->get['custom_field_group_id'] : 0;
		$custom_field_group = $this->MsLoader->MsCustomField->getCustomFieldGroups(array('custom_field_group_id' => $custom_field_group_id, 'single' => 1));

		$this->data['custom_field_group'] = $custom_field_group;
		$this->data['heading'] = $this->language->get('ms_custom_field_group_edit_heading');

		$this->response->setOutput($this->load->view('multiseller/custom-field-group-form', $this->data));
	}

	private function _initCFGForm() {
		$this->document->addScript('view/javascript/multimerch/custom-field-form.js');

		// languages
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		// stores
		$this->load->model('setting/store');
		$this->data['stores'] = $this->model_setting_store->getStores();

		// statuses
		$statuses = $locations = array();
		$msCustomField = new ReflectionClass('MsCustomField');
		foreach ($msCustomField->getConstants() as $cname => $cval) {
			if (strpos($cname, 'STATUS_') !== FALSE) {
				$statuses[] = $cval;
			}

			if (strpos($cname, 'LOCATION_') !== FALSE) {
				$locations[] = $cval;
			}
		}
		$this->data['statuses'] = $statuses;
		$this->data['locations'] = $locations;

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_custom_field_group_breadcrumbs'),
				'href' => $this->url->link('multimerch/custom-field', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['column_right'] = $this->load->controller('common/column_right');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
	}

	public function jxSaveCFG() {
		$validator = $this->MsLoader->MsValidator;

		$json = array();

		$data = array_merge($this->request->post, array('admin_id' => $this->user->getId()));

		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();
		$defaultLanguageId = $this->config->get('config_language_id');

		// validate primary language
		foreach ($languages as $language) {
			$language_id = $language['language_id'];
			$primary = true;
			if($language_id != $defaultLanguageId)
				$primary = false;

			// validate category name
			if(!$validator->validate(array(
				'name' => $this->language->get('ms_custom_field_name'),
				'value' => $data['cfg_description'][$language_id]['name']
			),
				array(
					!$primary ? array() : array('rule' => 'required'),
					array('rule' => 'max_len,100')
				)
			)) $json['errors']["cfg_description[$language_id][name]"] = $validator->get_errors();

			if(!$primary) {
				if (empty($data['cfg_description'][$language_id]['name']))
					$data['cfg_description'][$language_id]['name'] = $data['cfg_description'][$defaultLanguageId]['name'];
			}
		}

		if(empty($data['cfg_locations'])) {
			$json['errors']['cfg_locations'] = $this->language->get('ms_custom_field_group_error_locations');
		}

		if (!$validator->validate(array(
			'name' => $this->language->get('ms_sort_order'),
			'value' => $data['sort_order']
		),
			array(
				array('rule' => 'required'),
				array('rule' => 'numeric')
			)
		)
		) $json['errors']["sort_order"] = $validator->get_errors();

		if (!empty($json['errors'])) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}

		$this->MsLoader->MsCustomField->createOrUpdateCustomFieldGroup($data);

		$this->session->data['success'] = empty($data['custom_field_group_id']) ? $this->language->get('ms_custom_field_group_success_created') : $this->language->get('ms_custom_field_group_success_updated');

		$json['redirect'] = $this->url->link('multimerch/custom-field', 'token=' . $this->session->data['token'] . '#tab-cfg', true);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxDeleteCFG() {
		$json = array();

		if(!isset($this->request->get['custom_field_group_id']) && !isset($this->request->post['selected'])) {
			$json['error'] = $this->language->get('ms_custom_field_group_error_deleting');
		}

		if(!isset($json['error'])) {
			$custom_field_group_ids = isset($this->request->get['custom_field_group_id']) ?
				array($this->request->get['custom_field_group_id']) :
				(isset($this->request->post['selected']) ? $this->request->post['selected'] : array());

			foreach ($custom_field_group_ids as $custom_field_group_id) {
				$this->MsLoader->MsCustomField->deleteCustomFieldGroup($custom_field_group_id);
			}

			$this->session->data['success'] = $this->language->get('ms_custom_field_group_success_deleted');
			$json['redirect'] = $this->url->link('multimerch/custom-field', 'token=' . $this->session->data['token'] . '#tab-cfg', true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	/* ========================================= CUSTOM FIELDS - CF ================================================= */


	public function getCFTableData() {
		$colMap = array(
			'id' => 'mscf.custom_field_id',
			'name' => 'mscfd.`name`',
			'group_name' => 'mscfgd.`name`',
			'status' => 'mscf.status',
			'sort_order' => 'mscf.sort_order'
		);

		$sorts = array('name', 'group_name', 'status', 'sort_order');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$statuses = array();
		$msCustomField = new ReflectionClass('MsCustomField');
		foreach ($msCustomField->getConstants() as $cname => $cval) {
			if (strpos($cname, 'STATUS_') !== FALSE) {
				$statuses[] = $cval;
			}
		}

		$results = $this->MsLoader->MsCustomField->getCustomFields(
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
			// status
			$status = "";
			if(isset($result['status'])) {
				$status .= "<p style='color: ";

				if($result['status'] == MsCustomField::STATUS_ACTIVE) $status .= "green";
				if($result['status'] == MsCustomField::STATUS_DISABLED) $status .= "red";

				$status .= "'>" . $this->language->get('ms_custom_field_status_' . $result['status']) . "</p>";
			}

			// actions
			$actions = "";
			$actions .= "<a class='btn btn-primary' href='" . $this->url->link('multimerch/custom-field/updateCF', 'token=' . $this->session->data['token'] . '&custom_field_id=' . $result['custom_field_id'], 'SSL') . "' title='".$this->language->get('button_edit')."'><i class='fa fa-pencil''></i></a>";
			$actions .= "<button type='button' class='btn btn-danger ms-delete' title='".$this->language->get('button_delete')."' data-id='" . $result['custom_field_id'] . "' data-referrer='custom_field'><i class='fa fa-trash-o''></i></button>";

			$columns[] = array_merge(
				$result,
				array(
					'checkbox' => "<input type='checkbox' name='selected[]' value='{$result['custom_field_id']}' />",
					'name' => isset($result['languages'][$this->config->get('config_language_id')]['name']) ? $result['languages'][$this->config->get('config_language_id')]['name'] : '',
					'group_name' => $result['group_name'],
					'type' => ucfirst($result['type']),
					'status' => $status,
					'sort_order' => $result['sort_order'],
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

	public function createCF() {
		$this->document->setTitle($this->language->get('ms_custom_field_new_heading'));
		$this->_initCFForm();

		$this->data['custom_field'] = FALSE;
		$this->data['heading'] = $this->language->get('ms_custom_field_new_heading');

		$this->response->setOutput($this->load->view('multiseller/custom-field-form', $this->data));
	}

	public function updateCF() {
		$this->document->setTitle($this->language->get('ms_custom_field_edit_heading'));
		$this->_initCFForm();

		$custom_field_id = isset($this->request->get['custom_field_id']) ? (int)$this->request->get['custom_field_id'] : 0;
		$custom_field = $this->MsLoader->MsCustomField->getCustomFields(array('custom_field_id' => $custom_field_id, 'single' => 1));

		$this->data['custom_field'] = $custom_field;
		$this->data['heading'] = $this->language->get('ms_custom_field_edit_heading');

		$this->response->setOutput($this->load->view('multiseller/custom-field-form', $this->data));
	}

	private function _initCFForm() {
		$this->document->addScript('view/javascript/multimerch/custom-field-form.js');

		// languages
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		// stores
		$this->load->model('setting/store');
		$this->data['stores'] = $this->model_setting_store->getStores();

		// custom field groups
		$this->data['cf_groups'] = $this->MsLoader->MsCustomField->getCustomFieldGroups(array(
			'status' => MsCustomField::STATUS_ACTIVE
		));

		// statuses
		$statuses = array();
		$msCustomField = new ReflectionClass('MsCustomField');
		foreach ($msCustomField->getConstants() as $cname => $cval) {
			if (strpos($cname, 'STATUS_') !== FALSE) {
				$statuses[] = $cval;
			}
		}
		$this->data['statuses'] = $statuses;

		// types
		$this->data['types'] = array(
			'choose' => array('select', 'radio', 'checkbox'),
			'input' => array('text', 'textarea'),
			'file' => array('file'),
			'date' => array('date', 'time', 'datetime')
		);

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_custom_field_breadcrumbs'),
				'href' => $this->url->link('multimerch/custom-field', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['column_right'] = $this->load->controller('common/column_right');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
	}

	public function jxSaveCF() {
		$validator = $this->MsLoader->MsValidator;

		$json = array();

		$data = array_merge($this->request->post, array('admin_id' => $this->user->getId()));

		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();
		$defaultLanguageId = $this->config->get('config_language_id');

		// validate primary language
		foreach ($languages as $language) {
			$language_id = $language['language_id'];
			$primary = true;
			if($language_id != $defaultLanguageId)
				$primary = false;

			// validate custom field name
			if(!$validator->validate(array(
				'name' => $this->language->get('ms_custom_field_name'),
				'value' => $data['cf_description'][$language_id]['name']
			),
				array(
					!$primary ? array() : array('rule' => 'required'),
					array('rule' => 'max_len,100')
				)
			)) $json['errors']["cf_description[$language_id][name]"] = $validator->get_errors();

			if (isset($data['cf_value'])) {
				// Unset sample row
				unset($data['cf_value'][0]);

				if(in_array($data['type'], array('select', 'radio', 'checkbox')) && empty($data['cf_value']))
					$json['errors']["cf_values"] = $this->language->get('ms_custom_field_error_values');

				$i = 1;
				foreach ($data['cf_value'] as $cf_value) {
					if (!$validator->validate(array(
						'name' => $this->language->get('ms_custom_field_value'),
						'value' => $cf_value['description'][$language_id]['name']
					),
						array(
							!$primary ? array() : array('rule' => 'required'),
							array('rule' => 'max_len,100')
						)
					)
					) $json['errors']["cf_value[$i][description][$language_id][name]"] = $validator->get_errors();

					if (!$validator->validate(array(
						'name' => $this->language->get('ms_sort_order'),
						'value' => $cf_value['sort_order']
					),
						array(
							array('rule' => 'required'),
							array('rule' => 'numeric')
						)
					)
					) $json['errors']["cf_value[$i][sort_order]"] = $validator->get_errors();

					$i++;
				}
			}

			if(!$primary) {
				if (empty($data['cf_description'][$language_id]['name']))
					$data['cf_description'][$language_id]['name'] = $data['cf_description'][$defaultLanguageId]['name'];

				if (isset($data['cf_value'])) {
					foreach ($data['cf_value'] as $key => $cf_value) {
						if (empty($cf_value['description'][$language_id]['name'])) $data['cf_value'][$key]['description'][$language_id]['name'] = $cf_value['description'][$defaultLanguageId]['name'];
					}
				}
			}
		}

		if (!$validator->validate(array(
			'name' => $this->language->get('ms_sort_order'),
			'value' => $data['sort_order']
		),
			array(
				array('rule' => 'required'),
				array('rule' => 'numeric')
			)
		)
		) $json['errors']["sort_order"] = $validator->get_errors();

		if (!empty($json['errors'])) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}

		$this->MsLoader->MsCustomField->createOrUpdateCustomField($data);

		$this->session->data['success'] = empty($data['custom_field_id']) ? $this->language->get('ms_custom_field_success_created') : $this->language->get('ms_custom_field_success_updated');

		$json['redirect'] = $this->url->link('multimerch/custom-field', 'token=' . $this->session->data['token'] . '#tab-cf', true);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxDeleteCF() {
		$json = array();

		if(!isset($this->request->get['custom_field_id']) && !isset($this->request->post['selected'])) {
			$json['error'] = $this->language->get('ms_custom_field_error_deleting');
		}

		if(!isset($json['error'])) {
			$custom_field_ids = isset($this->request->get['custom_field_id']) ?
				array($this->request->get['custom_field_id']) :
				(isset($this->request->post['selected']) ? $this->request->post['selected'] : array());

			foreach ($custom_field_ids as $custom_field_id) {
				$this->MsLoader->MsCustomField->deleteCustomField($custom_field_id);
			}

			$this->session->data['success'] = $this->language->get('ms_custom_field_success_deleted');
			$json['redirect'] = $this->url->link('multimerch/custom-field', 'token=' . $this->session->data['token'] . '#tab-cf', true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	/* =============================================== COMMON ======================================================= */


	// @todo: unify method for uploading files for different multimerch systems (conversations etc.)
	public function jxUploadFile() {
		$this->load->language('catalog/download');

		$json = array();

		if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
			// Sanitize the filename
			$filename = basename(html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8'));

			// Validate the filename length
			if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 128)) {
				$json['error'] = $this->language->get('error_filename');
			}

			// Validate file extension
			$json['error'] = $this->MsLoader->MsFile->checkFile($this->request->files['file'], $this->config->get('msconf_msg_allowed_file_types'));

			// Check to see if any PHP files are trying to be uploaded
			$content = file_get_contents($this->request->files['file']['tmp_name']);

			if (preg_match('/\<\?php/i', $content)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Return any upload error
			if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
			}
		} else {
			$json['error'] = $this->language->get('error_upload');
		}

		if (empty($json['error'])) {
			unset($json['error']);

			// Hide the uploaded file name so people can not link to it directly.
			$file = $filename . '.' . token(32);

			move_uploaded_file($this->request->files['file']['tmp_name'], DIR_DOWNLOAD . $file);

			$download_data = array(
				'filename' => $file,
				'mask' => $filename
			);

			$this->load->model('localisation/language');
			$languages = $this->model_localisation_language->getLanguages();
			foreach ($languages as  $language) {
				$download_data['download_description'][$language['language_id']]['name'] = $filename;
			}

			$this->load->model('catalog/download');
			$json['download_id'] = $this->model_catalog_download->addDownload($download_data);

			$json['filename'] = $filename;
			$json['success'] = $this->language->get('ms_catalog_products_success_file_uploaded');
		}

		return $this->response->setOutput(json_encode($json));
	}

	public function jxRemoveUploadedFile() {
		$this->load->model('catalog/download');

		$json = array();

		if(isset($this->request->post['download_id'])) {
			$this->model_catalog_download->deleteDownload($this->request->post['download_id']);

			$json['success'] = $this->language->get('ms_catalog_products_success_upload_removed');
		} else {
			$json['error'] = $this->language->get('ms_catalog_products_error_upload_not_found_db');
		}

		return $this->response->setOutput(json_encode($json));
	}
}
?>