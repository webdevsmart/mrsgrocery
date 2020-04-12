<?php

class ControllerMultimerchDashboard extends ControllerMultimerchBase {
	public function index() {
		$this->document->addScript('view/javascript/multimerch/chartjs/Chart.min.js');
		$this->document->addScript('view/javascript/multimerch/chartjs/chartjs-plugin-datalabels.min.js');

		$this->document->addScript('view/javascript/multimerch/dashboard.js');
		$this->document->addStyle('view/stylesheet/multimerch/dashboard.css');

		$this->validate(__FUNCTION__);

		$this->data['token'] = $this->session->data['token'];

		$this->data['heading_title'] = $this->language->get('ms_dashboard_heading');
		$this->document->setTitle($this->language->get('ms_dashboard_title'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_dashboard_heading'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			)
		));

		$this->data['sales_count'] = $this->currency->format($this->MsLoader->MsReport->getSalesCount(), $this->config->get('config_currency'));
		$this->data['orders_count'] = $this->MsLoader->MsReport->getOrdersCount();
		$this->data['customers_count'] = $this->MsLoader->MsReport->getCustomersCount();
		$this->data['customers_online_count'] = $this->MsLoader->MsReport->getCustomersOnlineCount();
		$this->data['sellers_count'] = $this->MsLoader->MsReport->getSellersCount();
		$this->data['balances_count'] = $this->currency->format($this->MsLoader->MsReport->getSellersBalanceCount(), $this->config->get('config_currency'));
		$this->data['products_count'] = $this->MsLoader->MsReport->getProductsCount();
		$this->data['products_view_count'] = $this->MsLoader->MsReport->getProductsViewCount();

		$this->data['marketplace_activity'] = $results = $this->ms_event_manager->getEvents(
			array(),
			array(
				'order_by'  => 'date_created',
				'order_way' => 'DESC',
				'offset' => 0,
				'limit' => 5
			)
		);

		foreach ($this->data['marketplace_activity'] as &$result) {
			$result['event_description'] = rtrim($this->ms_event_manager->getEventDescription($result),'.');
			$result['date_created'] = date($this->language->get('date_format_short'), strtotime($result['date_created']));
		}

		$this->data['last_orders'] = $this->MsLoader->MsReport->getLastOrders();
		foreach ($this->data['last_orders'] as &$result) {
			$name = '<a target="blank" href="'.$this->url->link('customer/customer/edit', 'customer_id='. $result['customer_id'] .'&token=' . $this->session->data['token'], 'SSL').'" style="font-weight: bold; color: #42AFCA;" />';
			$name.= $result['customer_name'];
			$name.= '</a>';

			$result['name'] = $name;

			$order_state = $this->MsLoader->MsOrderData->getOrderStateByStatusId($result['order_status_id']);
			$status_class = 'ms-status-failed';
			if ((int)$order_state === (int)MsOrderData::STATE_PENDING || (int)$order_state === (int)MsOrderData::STATE_PROCESSING) {
				$status_class = 'ms-status-pending';
			} elseif ((int)$order_state === (int)MsOrderData::STATE_COMPLETED) {
				$status_class = 'ms-status-completed';
			}

			$result['order_status'] = '<span class="' . $status_class . '">' . $result['order_status'] . '</span>';

			$result['total'] = $this->currency->format($result['total'], $this->config->get('config_currency'));
			$result['date_added'] = date($this->language->get('date_format_short'), strtotime($result['date_added']));
		}

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multimerch/dashboard.tpl', $this->data));
	}

	/**
	 * Prepares data for chart initialization.
	 *
	 * Accepts 4 parameters in GET request:
	 * - 'type': chart type. Possible types: 'sales_analytics', 'top_countries', 'top_sellers', 'top_customers',
	 * 										 'top_products'.
	 * - 'date_start': start date of a time period selected by user.
	 * - 'date_end': end date of a time period selected by user.
	 * - 'metric': metric in which user wants to get results (e.g., 'week', 'month' etc.)
	 *
	 * @return	string			JSON with data needed for chart initialization.
	 */
	public function getChartData() {
		$json = array();

		$labels = $datasets = $xAxes = $yAxes = array();

		if (isset($this->request->get['type'])) {
			switch ((string)$this->request->get['type']) {
				case "sales_analytics":
					$chart_data = $this->MsLoader->MsReport->getSalesAnalytics(array(
						'date_start' => isset($this->request->get['date_start']) ? $this->request->get['date_start'] : false,
						'date_end' => isset($this->request->get['date_end']) ? $this->request->get['date_end'] : false,
						'metric' => isset($this->request->get['metric']) ? $this->request->get['metric'] : false
					));

					$total_orders = $gross_sales = array();
					$tooltip_data_total_orders = $tooltip_data_gross_sales = array();
					$max_total_orders = $max_gross_sales = 0;

					foreach ($chart_data as $data) {
						if ($data['total'] > $max_total_orders) $max_total_orders = $data['total'];
						if ($data['gross_total'] > $max_gross_sales) $max_gross_sales = $data['gross_total'];

						$labels[] = $data['date'];
						$total_orders[] = $data['total'];
						$gross_sales[] = !is_null($data['gross_total']) ? $this->currency->format($data['gross_total'], $this->config->get('config_currency'), '', false) : NULL;

						$tooltip_data_total_orders[] = $this->language->get('ms_dashboard_total_orders') . ": " . end($total_orders);
						$tooltip_data_gross_sales[] = $this->language->get('ms_dashboard_gross_sales') . ": " . $this->currency->format(end($gross_sales), $this->config->get('config_currency'));
					}

					$step_gross_sales = pow(10, $this->MsLoader->MsHelper->getNumberOfSignificantDigits($max_gross_sales) - 1);
					$step_total_orders = pow(10, $this->MsLoader->MsHelper->getNumberOfSignificantDigits($max_total_orders) - 1);

					$max_gross_rescaled = $this->MsLoader->MsHelper->ceiling($max_gross_sales, $step_gross_sales);
					$max_total_rescaled = $this->MsLoader->MsHelper->ceiling($max_total_orders, $step_total_orders);

					$datasets = array(
						array(
							'fill' => false,
							'label' => $this->language->get('ms_dashboard_gross_sales'),
							'yAxisID' => 'gross',
							'borderColor' => '#77c2d8',
							'data' => $gross_sales,
							'tooltip_data' => $tooltip_data_gross_sales
						),
						array(
							'fill' => false,
							'label' => $this->language->get('ms_dashboard_total_orders'),
							'yAxisID' => 'total',
							'borderColor' => '#ffb15e',
							'data' => $total_orders,
							'tooltip_data' => $tooltip_data_total_orders
						)
					);

					$yAxes = array(
						array(
							'id' => 'gross',
							'type' => 'linear',
							'position' => 'left',
							'ticks' => array(
								'min' => 0,
								'max' => $max_gross_rescaled,
								'stepSize' => $step_gross_sales,
								'data_formatted' => $this->_formatCurrencyTicks(0, $max_gross_rescaled, $step_gross_sales)
							)
						),
						array(
							'id' => 'total',
							'type' => 'linear',
							'position' => 'right',
							'gridLines' => array(
								'display' => false
							),
							'ticks' => array(
								'min' => 0,
								'max' => $max_total_rescaled,
								'stepSize' => $step_total_orders
							)
						)
					);

					$xAxes = array(
						array(
							'gridLines' => array(
								'display' => false
							)
						)
					);

					$type = 'line';

					$no_results_error = $this->language->get('ms_dashboard_sales_analytics_no_results');

					break;

				case "top_countries":
					$chart_data = $this->MsLoader->MsReport->getTopCountriesAnalytics(array(
						'date_start' => isset($this->request->get['date_start']) ? $this->request->get['date_start'] : false,
						'date_end' => isset($this->request->get['date_end']) ? $this->request->get['date_end'] : false,
					));

					list($type, $labels, $datasets, $xAxes, $yAxes, $no_results_error) = $this->prepareHorizontalBarChartData($this->request->get['type'], $chart_data);

					break;

				case "top_sellers":
					$chart_data = $this->MsLoader->MsReport->getTopSellersAnalytics(array(
						'date_start' => isset($this->request->get['date_start']) ? $this->request->get['date_start'] : false,
						'date_end' => isset($this->request->get['date_end']) ? $this->request->get['date_end'] : false,
					));

					list($type, $labels, $datasets, $xAxes, $yAxes, $no_results_error) = $this->prepareHorizontalBarChartData($this->request->get['type'], $chart_data);

					break;

				case "top_customers":
					$chart_data = $this->MsLoader->MsReport->getTopCustomersAnalytics(array(
						'date_start' => isset($this->request->get['date_start']) ? $this->request->get['date_start'] : false,
						'date_end' => isset($this->request->get['date_end']) ? $this->request->get['date_end'] : false,
					));

					list($type, $labels, $datasets, $xAxes, $yAxes, $no_results_error) = $this->prepareHorizontalBarChartData($this->request->get['type'], $chart_data);

					break;

				case "top_products":
					$chart_data = $this->MsLoader->MsReport->getTopProductsAnalytics(array(
						'date_start' => isset($this->request->get['date_start']) ? $this->request->get['date_start'] : false,
						'date_end' => isset($this->request->get['date_end']) ? $this->request->get['date_end'] : false,
					));

					list($type, $labels, $datasets, $xAxes, $yAxes, $no_results_error) = $this->prepareHorizontalBarChartData($this->request->get['type'], $chart_data);

					break;
			}
		}

		$json['chart_data'] = array(
			'common' => array(
				'currency_symbol_left' => $this->currency->getSymbolLeft($this->config->get('config_currency')),
				'currency_symbol_right' => $this->currency->getSymbolRight($this->config->get('config_currency')),
				'no_results_error' => isset($no_results_error) ? $no_results_error : $this->language->get('text_no_results')
			),

			'type' => $type,
			'labels' => $labels,
			'datasets' => $datasets,
			'xAxes' => $xAxes,
			'yAxes' => $yAxes
		);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Helper. Prepares data for chart initialization of type 'horizontalBar'.
	 *
	 * @param	string	$chart_type		Possible types: 'top_countries', 'top_sellers', 'top_customers', 'top_products'.
	 * @param	array	$chart_data		Unprepared data to be displayed at the chart.
	 * @return	array					Prepared data for chart initialization.
	 */
	protected function prepareHorizontalBarChartData($chart_type, $chart_data) {
		$bar_colors = array(
			'top_countries' => '#F0D578',
			'top_sellers' => '#42AFCA',
			'top_customers' => '#92739E',
			'top_products' => '#76BC5E'
		);

		$no_results_errors = array(
			'top_countries' => $this->language->get('ms_dashboard_top_countries_no_results'),
			'top_sellers' => $this->language->get('ms_dashboard_top_sellers_no_results'),
			'top_customers' => $this->language->get('ms_dashboard_top_customers_no_results'),
			'top_products' => $this->language->get('ms_dashboard_top_products_no_results')
		);

		$type = 'horizontalBar';

		$labels = $gross_sales = $tooltip_data = $data_formatted = array();
		$max_gross_sales = 0;

		if (!empty($chart_data)) {
			foreach ($chart_data as $data) {
				if ($data['gross_total'] > $max_gross_sales) $max_gross_sales = $data['gross_total'];

				$labels[] = $data['name'];
				$gross_sales[] = $this->currency->format($data['gross_total'], $this->config->get('config_currency'), '', false);
				$data_formatted[] = $this->currency->format($data['gross_total'], $this->config->get('config_currency'));
				$tooltip_data[] = $this->language->get('ms_dashboard_gross_sales') . ": " . $this->currency->format($data['gross_total'], $this->config->get('config_currency')) . "; " . $this->language->get('ms_dashboard_total_orders') . ": " . $data['total'];
			}
		}

		$step_gross_sales = pow(10, $this->MsLoader->MsHelper->getNumberOfSignificantDigits($max_gross_sales) - 1);
		$max = $this->MsLoader->MsHelper->ceiling($max_gross_sales, $step_gross_sales);

		$datasets = array(
			array(
				'label' => $this->language->get('ms_report_column_gross'),
				'backgroundColor' => $bar_colors[$chart_type],
				'data' => $gross_sales,
				'data_formatted' => $data_formatted,
				'tooltip_data' => $tooltip_data
			)
		);

		$xAxes = array(
			array(
				'gridLines' => array(
					'drawBorder' => false,
					'display' => false
				),
				'ticks' => array(
					'min' => 0,
					'max' => $max,
					'stepSize' => $step_gross_sales
				),
				'display' => false
			)
		);

		$yAxes = array(
			array(
				'gridLines' => array(
					'drawBorder' => false,
					'display' => false
				),
				'afterFit' => array(
					'label_width' => 110
				)
			)
		);

		$no_results_error = isset($no_results_errors[$chart_type]) ? $no_results_errors[$chart_type] : $this->language->get('text_no_results');

		return array($type, $labels, $datasets, $xAxes, $yAxes, $no_results_error);
	}

	private function _formatCurrencyTicks($min, $max, $step) {
		$ticks = array();
		for ($i = $max; $i >= $min; $i -= $step) {
			$ticks[] = $this->currency->format($i, $this->config->get('config_currency'));
		}

		return $ticks;
	}
}
