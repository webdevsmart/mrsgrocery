<?php

class ControllerMultimerchPaymentRequest extends ControllerMultimerchBase {
	public function getTableData() {
		$colMap = array(
			'seller' => 'ms.nickname',
			'type' => 'request_type',
			'description' => 'mpr.description',
			'date_created' => 'mpr.date_created',
			'date_paid' => 'mpr.date_modified'
		);

		$sorts = array('request_type', 'seller', 'amount', 'description', 'request_status', 'date_created', 'date_modified');
		$filters = array_diff($sorts, array('request_status'));

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsPgRequest->getRequests(
			array(
				'request_type' => array(MsPgRequest::TYPE_SIGNUP, MsPgRequest::TYPE_LISTING)
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
				'checkbox' => ($result['request_status'] == MsPgRequest::STATUS_PAID || (int)$payment_status !== (int)MsPgPayment::STATUS_COMPLETE ? "" : "<input type='checkbox' name='selected[]' value='{$result['request_id']}' />"),
				'request_id' => $result['request_id'],
				'request_type' => $this->language->get('ms_pg_request_type_' . $result['request_type']),
				'seller' => "<a href='".$this->url->link('multimerch/seller/update', 'token=' . $this->session->data['token'] . '&seller_id=' . $result['seller_id'], 'SSL')."'>{$result['nickname']}</a>",
				'amount' => $this->currency->format(abs($result['amount']), $result['currency_code']),
				'description' => $result['description'],
				'date_created' => date($this->language->get('date_format_short'), strtotime($result['date_created'])),
				'request_status' => $this->language->get('ms_pg_request_status_' . $result['request_status']),
				'payment_id' => $result['payment_id'] ? $result['payment_id'] : '',
				'date_modified' => $result['date_modified'] ? date($this->language->get('date_format_short'), strtotime($result['date_modified'])) : '',
			);
		}

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}

	public function jxCreate() {
		$json = array();

		$data = $this->request->post;

		if(empty($data['sellers'])) {
			$json['errors'][] = $this->language->get('ms_pg_request_error_seller_notselected');
		}

		if(empty($data['date_payout_period'])) {
			$json['errors'][] = $this->language->get('ms_pg_request_error_date_period');
		}

		if(empty($json['errors'])) {
			$sellers = isset($this->request->post['sellers']) ? $this->request->post['sellers'] : array();
			$invoice_type = isset($this->request->post['type']) ? $this->request->post['type'] : MsPgRequest::TYPE_PAYOUT;

			$created_invoices_ids = array();

			if($invoice_type) {
				switch($invoice_type) {
					case MsPgRequest::TYPE_PAYOUT:
						foreach ($sellers as $seller_id => $amount) {
							$seller_name = $this->MsLoader->MsSeller->getSellerNickname($seller_id) ?: $this->MsLoader->MsSeller->getSellerFullName($seller_id);

							$request_id = $this->MsLoader->MsPgRequest->createRequest(
								array(
									'seller_id' => $seller_id,
									'request_type' => MsPgRequest::TYPE_PAYOUT,
									'request_status' => MsPgRequest::STATUS_UNPAID,
									'description' => sprintf($this->language->get('ms_pg_request_desc_payout'), $seller_name),
									'amount' => $amount,
									'currency_id' => $this->currency->getId($this->config->get('config_currency')),
									'currency_code' => $this->config->get('config_currency')
								)
							);

							if($request_id) {
								array_push($created_invoices_ids, $request_id);
							} else {
								$json['errors'][] = sprintf($this->language->get('ms_pg_request_error_not_created'), $seller_name);
							}
						}

						if(!empty($created_invoices_ids)) {
							$payout_id = $this->MsLoader->MsPayout->createPayout(array(
								'date_payout_period' => date("Y-m-d", strtotime($data['date_payout_period'])) . ' ' . date("H:i:s"),
								'invoice_ids' => $created_invoices_ids
							));

							$this->session->data['success'] = $json['success'] = sprintf($this->language->get('ms_payout_success_payout_created'), $payout_id);
						}

						break;

					default:
						$json['errors'][] = $this->language->get('ms_pg_request_error_type');
						break;
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function jxDelete() {
		$json = array();

		if(!isset($this->request->post['selected'])) {
			$json['errors'][] = $this->language->get('ms_pg_request_error_empty');
		}

		if(!isset($json['errors'])) {
			$request_ids = $this->request->post['selected'];

			foreach ($request_ids as $request_id) {
				$this->MsLoader->MsPgRequest->deleteRequest($request_id);
			}

			$this->session->data['success'] = $this->language->get('ms_pg_request_success_deleted');
			$json['redirect'] = $this->url->link('multimerch/payment-request', 'token=' . $this->session->data['token'], true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

	public function index() {
		$this->document->addScript('view/javascript/multimerch/payment-request.js');

		if (isset($this->session->data['error_warning'])) {
			$this->data['error_warning'] = $this->session->data['error_warning'];
			unset($this->session->data['error_warning']);
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['action'] = $this->url->link('multimerch/payment/create', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['token'] = $this->session->data['token'];
		$this->data['heading'] = $this->language->get('ms_pg_request');
		$this->document->setTitle($this->language->get('ms_pg_request'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_pg_request'),
				'href' => $this->url->link('multimerch/payment-request', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('multiseller/payment-request.tpl', $this->data));
	}
}