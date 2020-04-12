<?php

class ControllerMultimerchAttribute extends ControllerMultimerchBase {

	public function index() {
		$this->validate(__FUNCTION__);

		$this->document->addScript('//code.jquery.com/ui/1.11.2/jquery-ui.min.js');
		$this->document->addScript('view/javascript/multimerch/attribute.js');

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
		
		$this->data['heading'] = $this->language->get('ms_attribute_heading');
		$this->document->setTitle($this->language->get('ms_attribute_heading'));
		
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_attribute_breadcrumbs'),
				'href' => $this->url->link('multimerch/attribute', '', 'SSL'),
			)
		));
		
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/attribute', $this->data));
	}

	public function getMsAttributeTableData() {
		$colMap = array(
			'id' => 'msa.attribute_id',
			'name' => 'ad.name',
			'group_name' => 'agd.name',
			'seller' => 'mss.nickname',
			'status' => 'msa.attribute_status'
		);

		$sorts = array('name', 'group_name', 'seller', 'status');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$statuses = array();
		$msAttribute = new ReflectionClass('MsAttribute');
		foreach ($msAttribute->getConstants() as $cname => $cval) {
			if (strpos($cname, 'STATUS_') !== FALSE) {
				$statuses[] = $cval;
			}
		}

		$results = $this->MsLoader->MsAttribute->getAttributes(
			array(
				'with_seller' => true
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

			$status = "";
			if(isset($result['attribute_status'])) {
				$status .= "<p style='color: ";

				if($result['attribute_status'] == MsAttribute::STATUS_APPROVED) $status .= "blue";
				if($result['attribute_status'] == MsAttribute::STATUS_ACTIVE) $status .= "green";
				if($result['attribute_status'] == MsAttribute::STATUS_INACTIVE || $result['attribute_status'] == MsAttribute::STATUS_DISABLED) $status .= "red";

				$status .= "'>" . $this->language->get('ms_seller_attribute_status_' . $result['attribute_status']) . "</p>";
			}

			// actions
			$actions = "";
			$actions .= "<a class='btn btn-primary' href='" . $this->url->link('catalog/attribute/edit', 'token=' . $this->session->data['token'] . '&attribute_id=' . $result['attribute_id'], 'SSL') . "' title='".$this->language->get('button_edit')."'><i class='fa fa-pencil''></i></a>";
			$actions .= "<button type='button' class='btn btn-danger ms-delete' title='".$this->language->get('button_delete')."' data-id='" . $result['attribute_id'] . "' data-referrer='attribute'><i class='fa fa-trash-o''></i></button>";

			$columns[] = array_merge(
				$result,
				array(
					'checkbox' => "<input type='checkbox' name='selected[]' value='{$result['attribute_id']}' />",
					'name' => $result['name'],
					'group_name' => $result['ag_name'],
					'seller' => $this->MsLoader->MsSeller->getSellerNickname($result['seller_id']),
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

	public function getOcAttributeTableData() {
		$colMap = array(
			'id' => 'msa.attribute_id',
			'name' => 'ad.name',
			'group_name' => 'agd.name',
			'status' => 'msa.attribute_status'
		);

		$sorts = array('name', 'group_name', 'status');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$statuses = array();
		$msAttribute = new ReflectionClass('MsAttribute');
		foreach ($msAttribute->getConstants() as $cname => $cval) {
			if (strpos($cname, 'STATUS_') !== FALSE) {
				$statuses[] = $cval;
			}
		}

		$results = $this->MsLoader->MsAttribute->getAttributes(
			array(
				'seller_ids' => '0'
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

			$status = "";
			if(isset($result['attribute_status'])) {
				$status .= "<p style='color: ";

				if($result['attribute_status'] == MsAttribute::STATUS_APPROVED) $status .= "blue";
				if($result['attribute_status'] == MsAttribute::STATUS_ACTIVE) $status .= "green";
				if($result['attribute_status'] == MsAttribute::STATUS_INACTIVE || $result['attribute_status'] == MsAttribute::STATUS_DISABLED) $status .= "red";

				$status .= "'>" . $this->language->get('ms_seller_attribute_status_' . $result['attribute_status']) . "</p>";
			}

			// actions
			$actions = "";
			$actions .= "<a class='btn btn-primary' href='" . $this->url->link('catalog/attribute/edit', 'token=' . $this->session->data['token'] . '&attribute_id=' . $result['attribute_id'], 'SSL') . "' title='".$this->language->get('button_edit')."'><i class='fa fa-pencil''></i></a>";
			$actions .= "<button type='button' class='btn btn-danger ms-delete' title='".$this->language->get('button_delete')."' data-id='" . $result['attribute_id'] . "' data-referrer='attribute'><i class='fa fa-trash-o''></i></button>";

			$columns[] = array_merge(
				$result,
				array(
					'name' => $result['name'],
					'group_name' => $result['ag_name'],
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

	public function jxUpdateAttribute() {
		$serviceLocator = $this->MsLoader->load('\MultiMerch\Module\MultiMerch')->getServiceLocator();
		$mailTransport = $serviceLocator->get('MailTransport');
		$mails = new \MultiMerch\Mail\Message\MessageCollection();

		$json = array();

		if(!isset($this->request->get['attribute_id']) && !isset($this->request->post['selected_attributes']) && (!isset($this->request->get['attribute_status']) || !isset($this->request->get['seller_id']))) {
			$json['error'] = $this->language->get('ms_seller_attribute_error_updating');
		}

		if(!isset($json['error'])) {
			$attribute_ids = isset($this->request->get['attribute_id']) ?
				array($this->request->get['attribute_id']) :
				(isset($this->request->post['selected_attributes']) ? $this->request->post['selected_attributes'] : array());

			foreach ($attribute_ids as $attribute_id) {
				$params = array();

				$attribute_info = $this->MsLoader->MsAttribute->getAttributes(array('attribute_id' => $attribute_id, 'single' => 1));
				$seller = isset($attribute_info['seller_id']) ? $this->MsLoader->MsSeller->getSeller($attribute_info['seller_id']) : FALSE;

				if(isset($this->request->get['attribute_status'])) {
					$params['attribute_status'] = $this->request->get['attribute_status'];

					$status = "<p style='color: ";

					if($params['attribute_status'] == MsAttribute::STATUS_APPROVED) $status .= "blue";
					if($params['attribute_status'] == MsAttribute::STATUS_ACTIVE) $status .= "green";
					if($params['attribute_status'] == MsAttribute::STATUS_INACTIVE || $params['attribute_status'] == MsAttribute::STATUS_DISABLED) $status .= "red";

					$status .= "'>" . $this->language->get('ms_seller_attribute_status_' . $params['attribute_status']) . "</p>";

					$json['attribute_status'][$attribute_id] = $status;

					if($seller) {
						$MailAttributeStatusChanged = $serviceLocator->get('MailAttributeStatusChanged', false)
							->setTo($seller['c.email'])
							->setData(array(
								'addressee' => $seller['ms.nickname'],
								'attr_name' => $attribute_info['name'],
								'attr_status' => $this->language->get('ms_seller_attribute_status_' . $this->request->get['attribute_status'])
							));
						$mails->add($MailAttributeStatusChanged);
					}
				}

				if(isset($this->request->get['seller_id'])) {
					$params['seller_id'] = $this->request->get['seller_id'];

					/*if($this->request->get['seller_id']) {
						if($seller) {
							// Mail old seller to let him know his attribute has been detached from him

							$MailAttributeSellerChanged = $serviceLocator->get('MailAttributeSellerChanged', false)
								->setTo($seller['c.email'])
								->setData(array(
									'addressee' => $seller['ms.nickname'],
									'attr_name' => $attribute_info['name'],
									'action' => 1
								));
							$mails->add($MailAttributeSellerChanged);
						}

						$new_seller = $this->MsLoader->MsSeller->getSeller($this->request->get['seller_id']);

						if($new_seller) {
							// Mail new seller

							$MailAttributeSellerChanged = $serviceLocator->get('MailAttributeSellerChanged', false)
								->setTo($new_seller['c.email'])
								->setData(array(
									'addressee' => $new_seller['ms.nickname'],
									'attr_name' => $attribute_info['name'],
									'action' => 2
								));
							$mails->add($MailAttributeSellerChanged);
						}
					} else {
						$MailAttributeConvertedToGlobal = $serviceLocator->get('MailAttributeConvertedToGlobal', false)
							->setTo($seller['c.email'])
							->setData(array(
								'addressee' => $seller['ms.nickname'],
								'attr_name' => $attribute_info['name']
							));
						$mails->add($MailAttributeConvertedToGlobal);
					}*/
				}

				$this->MsLoader->MsAttribute->createOrUpdateMsAttribute($attribute_id, $params);
			}

			if ($mails->count()) {
				$mailTransport->sendMails($mails);
			}

			$this->session->data['success'] = $this->language->get('ms_seller_attribute_updated');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxDeleteAttribute() {
		$json = array();

		if(!isset($this->request->get['attribute_id']) && !isset($this->request->post['selected'])) {
			$json['errors'][] = $this->language->get('ms_seller_attribute_error_deleting');
		}

		if(!isset($json['errors'])) {
			$attribute_ids = isset($this->request->get['attribute_id']) ?
				array($this->request->get['attribute_id']) :
				(isset($this->request->post['selected']) ? $this->request->post['selected'] : array());

			foreach ($attribute_ids as $attribute_id) {
				$this->MsLoader->MsAttribute->deleteAttribute($attribute_id);

				$this->session->data['success'] = $this->language->get('ms_seller_attribute_deleted');
				$json['redirect'] = $this->url->link('multimerch/attribute', 'token=' . $this->session->data['token'] . '#tab-attribute', true);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	/************************************************************/

	public function jxUpdateAttributeGroup() {
		$serviceLocator = $this->MsLoader->load('\MultiMerch\Module\MultiMerch')->getServiceLocator();
		$mailTransport = $serviceLocator->get('MailTransport');
		$mails = new \MultiMerch\Mail\Message\MessageCollection();

		$json = array();

		if(!isset($this->request->get['attribute_group_id']) && !isset($this->request->post['selected_attribute_groups']) && (!isset($this->request->get['attribute_group_status']) || !isset($this->request->get['seller_id']))) {
			$json['error'] = $this->language->get('ms_seller_attribute_group_error_updating');
		}

		if(!isset($json['error'])) {
			$attribute_group_ids = isset($this->request->get['attribute_group_id']) ?
				array($this->request->get['attribute_group_id']) :
				(isset($this->request->post['selected_attribute_groups']) ? $this->request->post['selected_attribute_groups'] : array());

			foreach ($attribute_group_ids as $attribute_group_id) {
				$params = array();

				$attribute_group_info = $this->MsLoader->MsAttribute->getAttributeGroups(array('attribute_group_id' => $attribute_group_id, 'single' => 1));
				$seller = isset($attribute_group_info['seller_id']) ? $this->MsLoader->MsSeller->getSeller($attribute_group_info['seller_id']) : FALSE;

				if(isset($this->request->get['attribute_group_status'])) {
					$params['attribute_group_status'] = $this->request->get['attribute_group_status'];

					$status = "<p style='color: ";

					if($params['attribute_group_status'] == MsAttribute::STATUS_APPROVED) $status .= "blue";
					if($params['attribute_group_status'] == MsAttribute::STATUS_ACTIVE) $status .= "green";
					if($params['attribute_group_status'] == MsAttribute::STATUS_INACTIVE || $params['attribute_group_status'] == MsAttribute::STATUS_DISABLED) $status .= "red";

					$status .= "'>" . $this->language->get('ms_seller_attribute_status_' . $params['attribute_group_status']) . "</p>";

					$json['attribute_group_status'][$attribute_group_id] = $status;

					if($seller) {
						$MailAttributeGroupStatusChanged = $serviceLocator->get('MailAttributeGroupStatusChanged', false)
							->setTo($seller['c.email'])
							->setData(array(
								'addressee' => $seller['ms.nickname'],
								'attr_gr_name' => $attribute_group_info['name'],
								'attr_gr_status' => $this->language->get('ms_seller_attribute_status_' . $this->request->get['attribute_group_status'])
							));
						$mails->add($MailAttributeGroupStatusChanged);
					}
				}

				if(isset($this->request->get['seller_id'])) {
					$params['seller_id'] = $this->request->get['seller_id'];
				}

				$this->MsLoader->MsAttribute->createOrUpdateMsAttributeGroup($attribute_group_id, $params);
			}

			if ($mails->count()) {
				$mailTransport->sendMails($mails);
			}

			$this->session->data['success'] =  $this->language->get('ms_seller_attribute_group_updated');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxDeleteAttributeGroup() {
		$json = array();

		if(!isset($this->request->get['attribute_group_id']) && !isset($this->request->post['selected'])) {
			$json['errors'][] = $this->language->get('ms_seller_attribute_group_error_deleting');
		}

		if(!isset($json['errors'])) {
			$attribute_group_ids = isset($this->request->get['attribute_group_id']) ?
				array($this->request->get['attribute_group_id']) :
				(isset($this->request->post['selected']) ? $this->request->post['selected'] : array());

			foreach ($attribute_group_ids as $attribute_group_id) {
				$this->MsLoader->MsAttribute->deleteAttributeGroup($attribute_group_id);

				$this->session->data['success'] =  $this->language->get('ms_seller_attribute_group_deleted');
				$json['redirect'] = $this->url->link('multimerch/attribute', 'token=' . $this->session->data['token'] . '#tab-attribute-group', true);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
?>