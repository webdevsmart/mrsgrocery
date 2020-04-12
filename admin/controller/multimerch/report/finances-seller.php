<?php

class ControllerMultimerchReportFinancesSeller extends ControllerMultimerchReportbase {

	public function index() {
		$this->document->addScript('view/javascript/multimerch/report/finances-seller.js');

		$this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['token'] = $this->session->data['token'];

		$this->data['heading'] = $this->language->get('ms_menu_reports_finances_seller');
		$this->document->setTitle($this->language->get('ms_menu_reports_finances_seller'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL')
			),
			array(
				'text' => $this->language->get('ms_menu_reports') . ' - ' . $this->language->get('ms_menu_reports_finances') . ' - ' . $this->language->get('ms_menu_reports_finances_seller'),
				'href' => $this->url->link('multimerch/report/finances-seller', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('multimerch/report/finances-seller', $this->data));
	}

	public function getTableData() {
		$dataParams = array();
		$colMap = array(
			'seller' => 'mss.nickname'
		);

		$sorts = array('seller', 'balance_in', 'balance_out', 'marketplace_earnings', 'seller_earnings', 'payments_received', 'payouts_paid', 'current_balance');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		if(isset($this->request->get['date_start']) && isset($this->request->get['date_end'])) {
			$dataParams = array(
				'date_start' => strtotime($this->request->get['date_start']),
				'date_end' => strtotime($this->request->get['date_end'])
			);
		}

		$results = $this->MsLoader->MsReport->getSellerFinancesData(
			$dataParams,
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				'filters' => $filterParams,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength']
			)
		);

		$total = isset($results[0]['total_rows']) ? $results[0]['total_rows'] : 0;

		$columns = array();
		foreach ($results as $result) {
			$columns[] = array_merge(
				$result,
				array(
					'seller' => $result['seller_name'],
					'balance_in' => $this->currency->format($result['balance_in'], $this->config->get('config_currency')),
					'balance_out' => $this->currency->format($result['balance_out'], $this->config->get('config_currency')),
					'marketplace_earnings' => $this->currency->format(abs($result['marketplace_earnings']), $this->config->get('config_currency')),
					'seller_earnings' => $this->currency->format(abs($result['seller_earnings']), $this->config->get('config_currency')),
					'payments_received' => $this->currency->format(abs($result['payments_received']), $this->config->get('config_currency')),
					'payouts_paid' => $this->currency->format(abs($result['payouts_paid']), $this->config->get('config_currency')),
					'current_balance' => $this->currency->format($result['current_balance'], $this->config->get('config_currency'))
				)
			);
		}

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}
}