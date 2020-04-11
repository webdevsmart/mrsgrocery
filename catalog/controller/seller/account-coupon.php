<?php

class ControllerSellerAccountCoupon extends ControllerSellerAccount {

	// General

	public function index() {
		$this->_validateCall();

		$this->document->addScript('catalog/view/javascript/multimerch/account-coupon.js');
		$this->document->setTitle($this->language->get('ms_seller_account_coupon_breadcrumbs'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_seller_account_coupon_breadcrumbs'),
				'href' => $this->url->link('seller/account-coupon', '', 'SSL'),
			)
		));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-coupon');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	private function _validateCall() {
		if(!$this->config->get('msconf_allow_seller_coupons')) {
			return $this->response->redirect($this->url->link('seller/account-dashboard', '', 'SSL'));
		}
	}


	/************************************************************/


	// Coupons

	public function getTableData() {
		$this->_validateCall();

		$colMap = array(
			'code' => 'msc.`code`',
			'type' => 'msc.`type`',
			'value' => 'msc.`value`',
			'total_uses' => 'msc.`total_uses`',
			'max_uses' => 'msc.`max_uses`',
			'date_start' => 'msc.`date_start`',
			'date_end' => 'msc.`date_end`',
			'status' => 'msc.`status`'
		);

		$sorts = array('code', 'value', 'total_uses', 'date_start', 'date_end', 'status');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsCoupon->getCoupons(
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
			// status
			$status = "";
			if(isset($result['status'])) {
				$status .= "<p style='color: ";

				if($result['status'] == MsCoupon::STATUS_ACTIVE) $status .= "green";
				if($result['status'] == MsCoupon::STATUS_DISABLED) $status .= "red";

				$status .= "'>" . $this->language->get('ms_seller_account_coupon_status_' . $result['status']) . "</p>";
			}

			// actions
			$actions = "";
			$actions .= "<a class='icon-edit' href='" . $this->url->link('seller/account-coupon/update', 'coupon_id=' . $result['coupon_id'], 'SSL') ."' title='" . $this->language->get('ms_edit') . "'><i class='fa fa-pencil'></i></a>";
			$actions .= "<a class='icon-remove ms_remove_coupon' href='" . $this->url->link('seller/account-coupon/delete', 'coupon_id=' . $result['coupon_id'], 'SSL') ."' title='" . $this->language->get('ms_delete') . "'><i class='fa fa-times'></i></a>";

			$columns[] = array_merge(
				$result,
				array(
					'code' => $result['code'],
					'value' => (int)$result['type'] === (int)MsCoupon::TYPE_DISCOUNT_PERCENT ? round($result['value'], 2) . "%" : $this->currency->format($result['value'], $this->config->get('config_currency')),
					'total_uses' => $result['total_uses'] . "/" . ($result['max_uses'] ?: '-'),
					'date_start' => $result['date_start'] ? date($this->language->get('date_format_short'), strtotime($result['date_start'])) : '-',
					'date_end' => $result['date_end'] ? date($this->language->get('date_format_short'), strtotime($result['date_end'])) : '-',
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

	public function jxSaveCoupon() {
		$this->_validateCall();

		$data = $this->request->post['coupon'];

		// Validate category is owned by seller
		if (isset($data['category_id']) && !empty($data['category_id'])) {
			if (!$this->MsLoader->MsCoupon->checkCouponBelongsToSeller($data['coupon_id'], $this->customer->getId())) {
				return;
			}
		}

		/**
		 * Validation
		 */
		$validator = $this->MsLoader->MsValidator;

		// coupon name
		if (!$validator->validate(array(
			'name' => $this->language->get('ms_seller_account_coupon_name'),
			'value' => $data['name']
		),
			array(
				array('rule' => 'required'),
				array('rule' => 'max_len,100')
			)
		)
		) $json['errors']["coupon[name]"] = $validator->get_errors();

		// coupon code
		// @todo 8.12: make strict alphanumeric
		if (!$validator->validate(array(
			'name' => $this->language->get('ms_seller_account_coupon_code'),
			'value' => $data['code']
		),
			array(
				array('rule' => 'required'),
				array('rule' => 'max_len,12')
			)
		)
		) $json['errors']["coupon[code]"] = $validator->get_errors();

		// coupon value
		if (!$validator->validate(array(
			'name' => $this->language->get('ms_seller_account_coupon_value'),
			'value' => $data['value']
		),
			array(
				array('rule' => 'required'),
				array('rule' => 'numeric')
			)
		)
		) $json['errors']["coupon[value]"] = $validator->get_errors();

		// coupon max uses
		if (!$validator->validate(array(
			'name' => $this->language->get('ms_seller_account_coupon_max_uses'),
			'value' => $data['max_uses']
		),
			array(
				array('rule' => 'numeric')
			)
		)
		) $json['errors']["coupon[max_uses]"] = $validator->get_errors();

		// min order total
		if (!$validator->validate(array(
			'name' => $this->language->get('ms_seller_account_coupon_min_order_total'),
			'value' => $data['min_order_total']
		),
			array(
				array('rule' => 'numeric')
			)
		)
		) $json['errors']["coupon[min_order_total]"] = $validator->get_errors();

		if(empty($data['coupon_id'])) {
			$coupon_code_exists = $this->MsLoader->MsCoupon->getCoupons(array('seller_id' => $this->customer->getId(), 'code' => $data['code']));
			if(!empty($coupon_code_exists)) {
				$json['errors']["coupon[code]"] = $this->language->get('ms_seller_account_coupon_code_error_exists');
			}
		}

		// return if errors
		if (!empty($json['errors'])) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}

		// Force uppercase for coupon code
		$data['code'] = mb_strtoupper($data['code']);

		// coupon discount type
		if(empty($data['type'])) $data['type'] = MsCoupon::TYPE_DISCOUNT_PERCENT;

		// coupon date end
		if(empty($data['date_end'])) unset($data['date_end']);

		// coupon status
		if(empty($data['status'])) $data['status'] = MsCoupon::STATUS_DISABLED;

		// seller
		$data['seller_id'] = $this->customer->getId();

		// Finish
		$this->MsLoader->MsCoupon->createOrUpdateCoupon($data);
		$this->session->data['success'] = $this->language->get(!empty($data['coupon_id']) ? 'ms_seller_account_coupon_updated' : 'ms_seller_account_coupon_created');

		$json['redirect'] = $this->url->link('seller/account-coupon', '', 'SSL');
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function _initCouponForm() {
		$this->_validateCall();

		$this->document->addScript('catalog/view/javascript/multimerch/account-coupon-form.js');
		$this->document->addScript('catalog/view/javascript/multimerch/selectize/selectize.min.js');
		$this->document->addStyle('catalog/view/javascript/multimerch/selectize/selectize.bootstrap3.css');

		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		$this->data['back'] = $this->url->link('seller/account-coupon', '', 'SSL');

		$this->data['seller_products'] = $this->MsLoader->MsProduct->getProducts(array('seller_id' => $this->customer->getId(), 'oc_status' => 1, 'product_status' => array(MsProduct::STATUS_ACTIVE)));
		if ($this->config->get('msconf_allow_seller_categories')) {
			$this->data['seller_categories'] = $this->MsLoader->MsCategory->getCategories(array('seller_ids' => $this->customer->getId(), 'category_status' => MsCategory::STATUS_ACTIVE));
		} else {
			$this->data['marketplace_categories'] = $this->MsLoader->MsCategory->getOcCategories();
		}

		// Title and friends
		$this->document->setTitle($this->language->get('ms_seller_account_coupon'));
		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_seller_account_coupon_breadcrumbs'),
				'href' => $this->url->link('seller/account-coupon', '', 'SSL'),
			)
		));
	}

	public function create() {
		$this->_validateCall();

		$this->_initCouponForm();

		$this->data['coupon'] = FALSE;

		$this->data['heading'] = $this->language->get('ms_seller_account_newcoupon_heading');
		$this->document->setTitle($this->language->get('ms_seller_account_newcoupon_heading'));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-coupon-form');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function update() {
		$this->_validateCall();

		$coupon_id = isset($this->request->get['coupon_id']) ? (int)$this->request->get['coupon_id'] : 0;
		$coupon = $this->MsLoader->MsCoupon->getCoupons(array('coupon_id' => $coupon_id));

		//check coupon owner
		if ($coupon['seller_id'] != $this->customer->getId()){
			return $this->response->redirect($this->url->link('seller/account-coupon', '', 'SSL'));
		}

		$this->_initCouponForm();

		// Validate included and excluded items not exist together at the same time
		foreach (array('products', 'customers', 'oc_categories', 'ms_categories') as $key) {
			if(!empty($coupon[$key])) {
				if(!empty($coupon[$key]['include']) && !empty($coupon[$key]['exclude'])) unset($coupon[$key]['exclude']);
				if(!empty($coupon[$key]['exclude']) && !empty($coupon[$key]['include'])) unset($coupon[$key]['include']);
			}
		}

		$this->load->model('localisation/currency');
		$currencies = $this->model_localisation_currency->getCurrencies();
		$decimal_place = isset($currencies[$this->config->get('config_currency')]['decimal_place']) ? $currencies[$this->config->get('config_currency')]['decimal_place'] : 2;

		if (!empty($coupon['value'])) $coupon['value'] = round($coupon['value'], (int)$decimal_place);
		if (!empty($coupon['min_order_total'])) $coupon['min_order_total'] = round($coupon['min_order_total'], (int)$decimal_place);

		// @todo: unify date format everywhere (mb move to lang file)
		if(!empty($coupon['date_start'])) $coupon['date_start'] = date('Y-m-d', strtotime($coupon['date_start']));
		if(!empty($coupon['date_end'])) $coupon['date_end'] = date('Y-m-d', strtotime($coupon['date_end']));

		$this->data['coupon'] = $coupon;

		$this->data['heading'] = $this->language->get('ms_seller_account_editcoupon_heading');
		$this->document->setTitle($this->language->get('ms_seller_account_editcoupon_heading'));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-coupon-form');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function delete() {
		$this->_validateCall();

		$json = array();

		$coupon_id = isset($this->request->get['coupon_id']) ? (int)$this->request->get['coupon_id'] : 0;

		if($coupon_id) {
			//check coupon owner
			$coupon = $this->MsLoader->MsCoupon->getCoupons(array('coupon_id' => $coupon_id));
			if ($coupon['seller_id'] != $this->customer->getId()){
				return $this->response->redirect($this->url->link('seller/account-coupon', '', 'SSL'));
			}

			$this->MsLoader->MsCoupon->deleteCoupon($coupon_id);
			$this->session->data['success'] = $this->language->get('ms_seller_account_coupon_success_deleted');
		} else {
			$json['error'] = $this->language->get('ms_seller_account_coupon_error_id');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	/************************************************************/


	// Helpers

	public function jxGetProducts() {
		$this->_validateCall();

		$json = array();

		$params = array();
		if(!empty($this->request->get['name'])) {
			$params['pd.name'] = $this->request->get['name'];
		}

		$json['products'] = $this->MsLoader->MsProduct->getProducts(array_merge($params, array('seller_id' => $this->customer->getId(), 'oc_status' => 1, 'product_status' => array(MsProduct::STATUS_ACTIVE))));

		foreach ($json['products'] as &$product) {
			$product['name'] = $product['pd.name'];
			unset($product['pd.name']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxGetCustomers() {
		$this->_validateCall();

		$json = array();

		$params = array();
		if(!empty($this->request->get['name'])) {
			$params['name'] = $this->request->get['name'];
		}

		$json['customers'] = $this->MsLoader->MsSeller->getSellerCustomers($this->customer->getId(), $params);

		foreach ($json['customers'] as &$customer) {
			$customer['name'] = $customer['customer_name'];
			unset($customer['customer_name']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxGetOcCategories() {
		$this->_validateCall();

		$json = array();

		$filter_data = !empty($this->request->get['name']) ? array(
			'filters' => array(
				'cd.name' => $this->request->get['name']
			)
		) : array();

		$json['oc_categories'] = $this->MsLoader->MsCategory->getOcCategories(array(), $filter_data);

		foreach ($json['oc_categories'] as &$oc_category) {
			$path = '';
			$path_arr = explode(',', $this->MsLoader->MsCategory->getOcCategoryPath($oc_category['category_id']));
			foreach ($path_arr as $category_id) {
				$path .= $this->MsLoader->MsCategory->getOcCategoryName($category_id, $this->config->get('config_language_id')) . ($category_id !== end($path_arr) ? ' > ' : '');
			}

			$oc_category['name'] = html_entity_decode(!empty($path) ? $path : $oc_category['name']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxGetMsCategories() {
		$this->_validateCall();

		$json = array();

		$filter_data = !empty($this->request->get['name']) ? array(
			'filters' => array(
				'mscd.name' => $this->request->get['name']
			)
		) : array();

		$json['ms_categories'] = $this->MsLoader->MsCategory->getCategories(array('seller_ids' => $this->customer->getId(), 'category_status' => MsCategory::STATUS_ACTIVE), $filter_data);

		foreach ($json['ms_categories'] as &$ms_category) {
			$ms_category['name'] = html_entity_decode(!empty($ms_category['path']) ? $ms_category['path'] . ' > ' . $ms_category['name'] : $ms_category['name']);
			unset($ms_category['path']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
