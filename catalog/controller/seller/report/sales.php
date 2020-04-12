<?php

class ControllerSellerReportSales extends ControllerSellerReport {

	public function index() {
		$this->document->addScript('catalog/view/javascript/multimerch/report/sales.js');

		$this->document->setTitle($this->language->get('ms_report_sales_list'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL')
			),
			array(
				'text' => $this->language->get('ms_report_sales') . " - " . $this->language->get('ms_report_sales_list'),
				'href' => $this->url->link('seller/report/sales', '', 'SSL')
			)
		));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('multiseller/report/sales');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function getTableData() {
		$dataParams = array(
			'seller_id' => $this->customer->getId()
		);

		$colMap = array(
			'order_id' => 'o.order_id',
			'product' => 'op.`name`',
			'seller' => 'mss.nickname',
			'date' => 'o.`date_added`'
		);

		$sorts = array('order_id', 'product', 'seller', 'gross', 'net_marketplace', 'net_seller', 'tax', 'shipping', 'total', 'date');
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

		$results = $this->MsLoader->MsReport->getSalesData(
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
					'date' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'order_id' => '<a href="' . $this->url->link('seller/account-order/viewOrder', 'order_id=' . $result['order_id']) . '" target="_blank"> #' . $result['order_id'] . '</a>',
					'product' => $result['product_name'],
					'gross' => $this->currency->format(abs($result['gross']), $this->config->get('config_currency')),
					'net_marketplace' => $this->currency->format(abs($result['net_marketplace']), $this->config->get('config_currency')),
					'net_seller' => $this->currency->format(abs($result['net_seller']), $this->config->get('config_currency')),
					'tax' => $this->currency->format(abs($result['tax']), $this->config->get('config_currency')),
					'shipping' => $this->currency->format(abs($result['shipping']), $this->config->get('config_currency')),
					'total' => $this->currency->format(abs($result['total']), $this->config->get('config_currency'))
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
