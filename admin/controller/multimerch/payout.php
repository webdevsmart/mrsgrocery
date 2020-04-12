<?php

class ControllerMultimerchPayout extends ControllerMultimerchBase {
	public function getSellerTableData() {
		$colMap = array(
			'seller' => '`nickname`',
		);

		$sorts = array('seller', 'balance', 'date_last_paid');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$dataParams = array();
		if(isset($this->request->get['date_filter'])) {
			$dataParams = array(
				'date_filter' => strtotime($this->request->get['date_filter']),
			);
		}

		$results = $this->MsLoader->MsPayout->getSellers(
			$dataParams,
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
			if($result['balance'] == 0 && $result['pending'] == 0 || $result['balance'] < 0) {
				$total--;
				continue;
			}

			$date_last_paid = (!$result['invoice_id'] || !$result['invoice_status']) ? '-' : '<a role="button" data-toggle="tooltip" data-html="true" title="<p>' . $this->language->get('ms_payout_invoice') . ' # ' . $result['invoice_id'] . '</p><p>' . $this->language->get('ms_status') . ': ' . $this->language->get('ms_pg_request_status_' . $result['invoice_status']) . '</p>">' . date($this->language->get('date_format_short'), strtotime($result['date_last_paid'])) . '</a>';

			$columns[] = array_merge(
				$result,
				array(
					'checkbox' => $result['balance'] > 0 ? "<input type='checkbox' name='selected[]' value='{$result['seller_id']}' />" : "",
					'seller' => $result['nickname'],
					'balance' => $this->currency->format($result['balance'], $this->config->get('config_currency')) . ($result['pending'] > 0 ? ' (' . $this->currency->format($result['pending'], $this->config->get('config_currency')) . ' ' . $this->language->get('ms_payout_seller_list_pending') .  ')' : ''),
					'date_last_paid' => $date_last_paid
				)
			);
		}

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}

	public function getPayoutTableData() {
		$colMap = array(
			'payout_id' => 'payout_id'
		);

		$sorts = array('payout_id');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsPayout->getPayouts(
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
			$actions = "";
			$actions .= "<a class='btn btn-primary' href='" . $this->url->link('multimerch/payout/view', 'token=' . $this->session->data['token'] . '&payout_id=' . $result['payout_id'], 'SSL') . "' title='".$this->language->get('ms_view')."'><i class='fa fa-eye''></i></a>";

			$columns[] = array_merge(
				$result,
				array(
					'name' => $result['name'],
					'date_created' => date($this->language->get('date_format_short'), strtotime($result['date_created'])),
					'date_payout_period' => date($this->language->get('date_format_short'), strtotime($result['date_payout_period'])),
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

	public function getInvoiceTableData() {
		$payout_id = isset($this->request->get['payout_id']) ? $this->request->get['payout_id'] : 0;

		$colMap = array(
			'seller' => 'ms.nickname',
			'date_paid' => 'mpr.date_modified'
		);

		$sorts = array('request_id', 'seller', 'amount', 'request_status');
		$filters = array_diff($sorts, array('amount', 'request_status'));

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsPgRequest->getRequests(
			array(
				'payout_id' => $payout_id
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
			$payment_status = $this->MsLoader->MsPgPayment->getPaymentStatus($result['payment_id']);
			$columns[] = array(
				'checkbox' => ($result['request_status'] == MsPgRequest::STATUS_PAID || ($result['payment_id'] && (int)$payment_status !== (int)MsPgPayment::STATUS_COMPLETE) ? "" : "<input type='checkbox' name='selected[]' value='{$result['request_id']}' />"),
				'request_id' => $result['request_id'],
				'seller' => "<a href='".$this->url->link('multimerch/seller/update', 'token=' . $this->session->data['token'] . '&seller_id=' . $result['seller_id'], 'SSL')."'>{$result['nickname']}</a>",
				'amount' => $this->currency->format(abs($result['amount']), $result['currency_code']),
				'date_created' => date($this->language->get('date_format_short'), strtotime($result['date_created'])),
				'request_status' => $this->language->get('ms_pg_request_status_' . $result['request_status'])
			);
		}

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}

	/**
	 * Renders a list of payouts and sellers available for payout.
	 *
	 * First tab in a view contains all payouts existing, second contains sellers list.
	 *
	 * @return	view	multiseller/payout.tpl
	 */
	public function index() {
		$this->document->addScript('view/javascript/multimerch/payout.js');
		$this->document->addScript('view/javascript/multimerch/moment.min.js');
		$this->document->addScript('view/javascript/multimerch/daterangepicker/daterangepicker.js');
		$this->document->addStyle('view/javascript/multimerch/daterangepicker/daterangepicker.css');

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

		$this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

		$this->data['generate_action'] = $this->url->link('multimerch/payout/confirm', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_payout_heading');
		$this->document->setTitle($this->language->get('ms_payout_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_payout_heading'),
				'href' => $this->url->link('multimerch/payout', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('multiseller/payout.tpl', $this->data));
	}

	/**
	 * Renders an interface for payout invoices creation.
	 *
	 * @return	view	multiseller/payout-confirm.tpl
	 */
	public function confirm() {
		$this->document->addScript('view/javascript/multimerch/payout-confirm.js');

		$selected_seller_ids = isset($this->request->post['selected']) ? $this->request->post['selected'] : array();
		$total_amount = 0;

		$date_payout_period = isset($this->request->post['date_filter']) ? $this->request->post['date_filter'] : '-';

		foreach ($selected_seller_ids as $seller_id) {
			// Get seller's amount available for payout to date
			$seller = $this->MsLoader->MsPayout->getSellers(array(
				'seller_id' => $seller_id,
				'date_filter' => strtotime($date_payout_period)
			));

			$available = isset($seller[0]) ? $seller[0]['balance'] : 0;

			$total_amount += $available;

			$this->data['sellers'][] = array(
				'seller_id' => $seller_id,
				'seller_name' => $this->MsLoader->MsSeller->getSellerNickname($seller_id) ?: $this->MsLoader->MsSeller->getSellerFullName($seller_id),
				'amount' => $available,
				'amount_formatted' => $this->currency->format($available, $this->config->get('config_currency'))
			);
		}

		$this->data['total_amount_formatted'] = $this->currency->format($total_amount, $this->config->get('config_currency'));
		$this->data['date_payout_period'] = $date_payout_period;

		$this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_payout_confirm');
		$this->document->setTitle($this->language->get('ms_payout_confirm'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_payout_confirm'),
				'href' => $this->url->link('multimerch/payout/confirm', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('multiseller/payout-confirm.tpl', $this->data));
	}

	/**
	 * Renders a list of invoices for selected payout.
	 *
	 * GET parameter 'payout_id' must be passed, otherwise user will be redirected to payouts list.
	 *
	 * @return	view	multiseller/payout-view.tpl
	 */
	public function view() {
		$this->document->addScript('view/javascript/multimerch/payout-view.js');

		$payout_id = isset($this->request->get['payout_id']) ? $this->request->get['payout_id'] : 0;

		// Validate payout id
		if(!$payout_id) {
			$this->response->redirect($this->url->link('multimerch/payout', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['payout_id'] = $payout_id;

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

		$this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

		$this->data['token'] = $this->session->data['token'];
		$this->data['back_action'] = $this->url->link('multimerch/payout', 'token=' . $this->session->data['token'], 'SSL');

		$heading = sprintf($this->language->get('ms_payout_view_heading'), $payout_id);
		$this->data['heading'] = $heading;
		$this->document->setTitle($heading);

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_payout_heading'),
				'href' => $this->url->link('multimerch/payout', '', 'SSL'),
			),
			array(
				'text' => $heading,
				'href' => $this->url->link('multimerch/payout/view', 'payout_id=' . $payout_id, 'SSL'),
			),
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('multiseller/payout-view.tpl', $this->data));
	}
}

