<?php

class ControllerSellerReportFinancesPayout extends ControllerSellerReport {

	public function index() {
		$this->document->addScript('catalog/view/javascript/multimerch/report/finances-payout.js');

		$this->document->setTitle($this->language->get('ms_report_finances_payout'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL')
			),
			array(
				'text' => $this->language->get('ms_report_finances') . " - " . $this->language->get('ms_report_finances_payout'),
				'href' => $this->url->link('seller/report/finances-payout', '', 'SSL')
			)
		));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('multiseller/report/finances-payout');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function getTableData() {
		$dataParams = array(
			'seller_id' => $this->customer->getId()
		);

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
			$date_range = array(
				'date_start' => strtotime($this->request->get['date_start']),
				'date_end' => strtotime($this->request->get['date_end'])
			);

			$dataParams = !empty($dataParams) ? array_merge($dataParams, $date_range) : $date_range;
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
					'request_id' => '#' . $result['request_id'],
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
