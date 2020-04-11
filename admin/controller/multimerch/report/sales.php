<?php

class ControllerMultimerchReportSales extends ControllerMultimerchReportbase {

	public function index() {
		$this->document->addScript('view/javascript/multimerch/report/sales.js');

		$this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['token'] = $this->session->data['token'];

		$this->data['heading'] = $this->language->get('ms_menu_reports_sales_list');
		$this->document->setTitle($this->language->get('ms_menu_reports_sales_list'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL')
			),
			array(
				'text' => $this->language->get('ms_menu_reports') . ' - ' . $this->language->get('ms_menu_reports_sales') . ' - ' . $this->language->get('ms_menu_reports_sales_list'),
				'href' => $this->url->link('multimerch/report/sales', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('multimerch/report/sales', $this->data));
	}

	public function getTableData() {
		$dataParams = array();
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
			$dataParams = array(
				'date_start' => strtotime($this->request->get['date_start']),
				'date_end' => strtotime($this->request->get['date_end'])
			);
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
					'order_id' => '<a href="' . $this->url->link('sale/order/info', 'order_id=' . $result['order_id']) . '&token=' . $this->session->data['token'] . '" target="_blank"> #' . $result['order_id'] . '</a>',
					'product' => $result['product_name'],
					'seller' => $result['seller_name'],
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