<?php

class ControllerSellerAccountPaymentRequest extends ControllerSellerAccount {
	public function getTableData() {
		$colMap = array(
			'type' => 'request_type',
			'description' => 'description',
			'date_created' => 'date_created',
			'date_paid' => 'date_modified'
		);

		$seller_id = $this->customer->getId();

		$sorts = array('request_id', 'request_type', 'payment_id', 'request_status', 'date_created', 'amount');
		$filters = array_diff(array_merge($sorts, array('description')), array('request_status', 'type'));

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsPgRequest->getRequests(
			array(
				'seller_id' => $seller_id,
				'request_type' => array(MsPgRequest::TYPE_LISTING, MsPgRequest::TYPE_SIGNUP, MsPgRequest::TYPE_PAYOUT)
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
			$payment_info = '-';

			// If there is no payment, show invoice status
			$status = $this->language->get('ms_pg_request_status_' . $result['request_status']);

			// If invoice is paid
			$payment = array();
			if($result['payment_id']) {
				$payment = $this->MsLoader->MsPgPayment->getPayments(array('payment_id' => $result['payment_id'], 'single' => 1));

				$pg_name = str_replace('ms_pg_', '', $payment['payment_code']);
				$this->load->language('multimerch/payment/' . $pg_name);

				// Show payment details
				$html  = '<p>Method: ' . ($payment['payment_code'] == 'ms_pp_adaptive' ? 'PayPal Adaptive' : $this->language->get('text_title')) . '</p>';
				$html .= '<p>Date paid: ' . date($this->language->get('date_format_short'), strtotime($payment['date_created'])) . '</p>';
				$payment_info  = '<a class="paymentInfoControl" href="#" data-toggle="tooltip" data-html="true" title="' . $html . '">' . $result['payment_id'] . '</a>';

				// Show payment status
				$status = $this->language->get('ms_pg_payment_status_' . $payment['payment_status']);
			}

			$columns[] = array(
				'checkbox' => ($result['payment_id'] && (isset($payment['payment_status']) && $payment['payment_status'] == MsPgPayment::STATUS_COMPLETE)) || $result['request_type'] == MsPgRequest::TYPE_PAYOUT ? "" : "<input type='checkbox' name='payment_requests[]' value='{$result['request_id']}' style='margin: 0;'/>",
				'request_id' => $result['request_id'],
				'request_type' => $this->language->get('ms_pg_request_type_' . $result['request_type']),
				'description' => $result['description'],
				'payment' => $payment_info,
				'request_status' => $status,
				'date_created' => date($this->language->get('date_format_short'), strtotime($result['date_created'])),
				'amount' => $this->currency->format(abs($result['amount']), $result['currency_code'])
			);
		}

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}

	public function index() {
		$seller_id = $this->customer->getId();

		$seller_balance = $this->MsLoader->MsBalance->getSellerBalance($seller_id);
		$pending_funds = $this->MsLoader->MsBalance->getReservedSellerFunds($seller_id);
		$waiting_funds = $this->MsLoader->MsBalance->getWaitingSellerFunds($seller_id, 14);
		$balance_formatted = $this->currency->format($seller_balance,$this->config->get('config_currency'));

		$balance_reserved_formatted = $pending_funds > 0 ? sprintf($this->language->get('ms_account_balance_reserved_formatted'), $this->currency->format($pending_funds)) . ', ' : '';
		$balance_reserved_formatted .= $waiting_funds > 0 ? sprintf($this->language->get('ms_account_balance_waiting_formatted'), $this->currency->format($waiting_funds)) . ', ' : '';
		$balance_reserved_formatted = ($balance_reserved_formatted == '' ? '' : '(' . substr($balance_reserved_formatted, 0, -2) . ')');

		$this->data['ms_balance_formatted'] = $balance_formatted;
		$this->data['ms_reserved_formatted'] = $balance_reserved_formatted;

		$earnings = $this->MsLoader->MsSeller->getTotalEarnings($seller_id);

		$this->data['earnings'] = $this->currency->format($earnings, $this->config->get('config_currency'));
		$this->data['link_back'] = $this->url->link('account/account', '', 'SSL');

		$this->data['action'] = 'index.php?route=seller/account-payment/create';

		$this->document->setTitle($this->language->get('ms_payment_payments_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_transactions_breadcrumbs'),
				'href' => $this->url->link('seller/account-payment-request', '', 'SSL'),
			)
		));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-payment-request');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}
}

?>
