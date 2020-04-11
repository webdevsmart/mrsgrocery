<?php

class ControllerMultimerchReportFinancesPayout extends ControllerMultimerchReportbase {

	public function index() {
		$this->document->addScript('view/javascript/multimerch/report/finances-payout.js');

		$this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['token'] = $this->session->data['token'];

		$this->data['heading'] = $this->language->get('ms_menu_reports_finances_payouts');
		$this->document->setTitle($this->language->get('ms_menu_reports_finances_payouts'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL')
			),
			array(
				'text' => $this->language->get('ms_menu_reports') . ' - ' . $this->language->get('ms_menu_reports_finances') . ' - ' . $this->language->get('ms_menu_reports_finances_payouts'),
				'href' => $this->url->link('multimerch/report/finances-payout', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('multimerch/report/finances-payout', $this->data));
	}

	public function getTableData() {
		$dataParams = array();
		$colMap = array(
			'request_id' => 'mspgr.request_id',
			'seller' => 'mss.nickname',
			'method' => 'mspgp.payment_code',
			'date' => 'mspgr.`date_created`'
		);

		$sorts = array('date', 'request_id', 'seller', 'method', 'description', 'gross');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		if(isset($this->request->get['date_start']) && isset($this->request->get['date_end'])) {
			$dataParams = array(
				'date_start' => strtotime($this->request->get['date_start']),
				'date_end' => strtotime($this->request->get['date_end'])
			);
		}

		$results = $this->MsLoader->MsReport->getPayoutsData(
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
					'date' => date($this->language->get('date_format_short'), strtotime($result['date_created'])),
					'request_id' => $result['request_id'],
					'seller' => $result['seller_name'],
					'method' => $result['method'],
					'description' => $result['description'],
					'gross' => $this->currency->format(abs($result['gross']), $this->config->get('config_currency'))
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