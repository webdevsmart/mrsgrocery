<?php

class ControllerSellerAccountDashboard extends ControllerSellerAccount {
	public function index() {
		$this->document->addScript('catalog/view/javascript/multimerch/chartjs/Chart.min.js');
		$this->document->addScript('catalog/view/javascript/multimerch/chartjs/chartjs-plugin-datalabels.min.js');

		$this->document->addScript('catalog/view/javascript/multimerch/account-dashboard.js');
		$this->document->addStyle('catalog/view/theme/default/stylesheet/multimerch/account-dashboard.css');

		$this->load->model('account/customer');

		// paypal listing payment confirmation
		if (isset($this->request->post['payment_status']) && strtolower($this->request->post['payment_status']) == 'completed') {
			$this->data['success'] = $this->language->get('ms_account_sellerinfo_saved');
		}

		$seller_id = $this->customer->getId();

		$this->data['current_balance'] = $this->currency->format($this->MsLoader->MsBalance->getSellerBalance($seller_id), $this->config->get('config_currency'));
		$this->data['total_earnings'] = $this->currency->format($this->MsLoader->MsSeller->getTotalEarnings($seller_id), $this->config->get('config_currency'));
		$this->data['total_orders'] = $this->MsLoader->MsReport->getOrdersCount(array('seller_id' => $seller_id));
		$this->data['total_products_views'] = $this->MsLoader->MsReport->getProductsViewCount(array('seller_id' => $seller_id));

		// Last orders
		$this->data['last_orders'] = $this->MsLoader->MsReport->getSellerLastOrders(array('seller_id' => $seller_id));
		foreach ($this->data['last_orders'] as &$order) {

			$order['date_added'] = '<a target="blank" href="' . $this->url->link('seller/account-order/viewOrder', 'order_id=' . $order['order_id'], 'SSL') . '">' . date($this->language->get('date_format_short'), strtotime($order['date_added'])) . '</a>';
			$order['customer_name'] = mb_strimwidth($order['customer_name'], 0, 20, "...");

			$order_state = $this->MsLoader->MsSuborderStatus->getSuborderStateByStatusId($order['order_status_id']);
			$status_class = 'ms-status-failed';
			if ((int)$order_state === (int)MsOrderData::STATE_PENDING || (int)$order_state === (int)MsOrderData::STATE_PROCESSING) {
				$status_class = 'ms-status-pending';
			} elseif ((int)$order_state === (int)MsOrderData::STATE_COMPLETED) {
				$status_class = 'ms-status-completed';
			}

			$order['order_status'] = '<span class="' . $status_class . '">' . $order['order_status'] . '</span>';

			$order_total = $this->MsLoader->MsSuborder->getSuborderTotal($order['order_id'], array('seller_id' => $seller_id));
			$shipping_total = $this->MsLoader->MsOrderData->getOrderShippingTotal($order['order_id'], array('seller_id' => $seller_id));
			$order['total'] = $this->currency->format($order_total['total'] + $shipping_total, $this->config->get('config_currency'));
		}

		// Last reviews
		$this->data['last_reviews'] = $this->MsLoader->MsReview->getReviews(
			array(
				'seller_id' => $seller_id
			),
			array(
				'order_by' => 'msr.date_created',
				'order_way' => 'DESC',
				'offset' => 0,
				'limit' => 5
			)
		);
		foreach ($this->data['last_reviews'] as &$review) {
			$customer = $this->model_account_customer->getCustomer($review['author_id']);
			$review['customer_name'] = isset($customer['firstname']) && isset($customer['lastname']) ? $customer['firstname'] . ' ' . $customer['lastname'] : $this->language->get('ms_conversation_customer_deleted');

			$review['rating_stars'] = '<div class="ms-ratings side">';
			$review['rating_stars'] .= '	<div class="ms-empty-stars"></div>';
			$review['rating_stars'] .= '	<div class="ms-full-stars" style="width:' . ($review['rating'] * 20) . '%"></div>';
			$review['rating_stars'] .= '</div>';

			$review['product_name'] = mb_strimwidth($review['product_name'], 0, 20, "...");
			$review['comment'] = mb_strimwidth($review['comment'], 0, 40, "...");
			$review['date_created'] = '<a target="blank" href="' . $this->url->link('seller/account-review/update', 'review_id=' . $review['review_id'], 'SSL') . '">' . date($this->language->get('date_format_short'), strtotime($review['date_created'])) . '</a>';
		}

		// Last messages
		$this->data['last_messages'] = $this->MsLoader->MsReport->getSellerLastMessages(array('seller_id' => $seller_id));
		foreach ($this->data['last_messages'] as &$message) {
			$message['title'] = mb_strimwidth($message['title'], 0, 25, "...");
			$message['message'] = mb_strimwidth($message['message'], 0, 50, "...");
			$message['date_created'] = '<a target="blank" href="' . $this->url->link('account/msmessage', 'conversation_id=' . $message['conversation_id'], 'SSL') . '">' . date($this->language->get('date_format_short'), strtotime($message['date_created'])) . '</a>';
		}

		// Last invoices
		$this->data['last_invoices'] = $this->MsLoader->MsPgRequest->getRequests(
			array(
				'seller_id' => $seller_id,
				'request_type' => array(MsPgRequest::TYPE_LISTING, MsPgRequest::TYPE_SIGNUP, MsPgRequest::TYPE_PAYOUT)
			),
			array(
				'order_by' => 'mpr.date_created',
				'order_way' => 'DESC',
				'offset' => 0,
				'limit' => 5
			)
		);
		foreach ($this->data['last_invoices'] as &$invoice) {
			$invoice['date_created'] = date($this->language->get('date_format_short'), strtotime($invoice['date_created']));
			$invoice['request_type'] = $this->language->get('ms_pg_request_type_' . $invoice['request_type']);
			$invoice['request_status'] = $this->language->get('ms_pg_request_status_' . $invoice['request_status']);
			$invoice['amount'] = $this->currency->format($invoice['amount'], $this->config->get('config_currency'));
		}

		$this->data['link_back'] = $this->url->link('account/account', '', 'SSL');

		$this->document->setTitle($this->language->get('ms_account_dashboard_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_dashboard_breadcrumbs'),
				'href' => $this->url->link('seller/account-dashboard', '', 'SSL'),
			)
		));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('account-dashboard');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
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
						'metric' => isset($this->request->get['metric']) ? $this->request->get['metric'] : false,
						'seller_id' => $this->customer->getId()
					));

					$type = 'line';

					$total_orders = $gross_sales = array();
					$tooltip_data_total_orders = $tooltip_data_gross_sales = array();
					$max_total_orders = $max_gross_sales = 0;

					foreach ($chart_data as $data) {
						if ($data['total'] > $max_total_orders) $max_total_orders = $data['total'];
						if ($data['gross_total'] > $max_gross_sales) $max_gross_sales = $data['gross_total'];

						$labels[] = $data['date'];
						$total_orders[] = $data['total'];
						$gross_sales[] = !is_null($data['gross_total']) ? $this->currency->format($data['gross_total'], $this->config->get('config_currency'), '', false) : NULL;

						$tooltip_data_total_orders[] = $this->language->get('ms_account_dashboard_total_orders') . ": " . end($total_orders);
						$tooltip_data_gross_sales[] = $this->language->get('ms_account_dashboard_gross_sales') . ": " . $this->currency->format(end($gross_sales), $this->config->get('config_currency'));
					}

					$step_gross_sales = pow(10, $this->MsLoader->MsHelper->getNumberOfSignificantDigits($max_gross_sales) - 1);
					$step_total_orders = pow(10, $this->MsLoader->MsHelper->getNumberOfSignificantDigits($max_total_orders) - 1);

					$max_gross_rescaled = $this->MsLoader->MsHelper->ceiling($max_gross_sales, $step_gross_sales);
					$max_total_rescaled = $this->MsLoader->MsHelper->ceiling($max_total_orders, $step_total_orders);

					$datasets = array(
						array(
							'fill' => false,
							'label' => $this->language->get('ms_account_dashboard_gross_sales'),
							'yAxisID' => 'gross',
							'borderColor' => '#77C2D8',
							'data' => $gross_sales,
							'tooltip_data' => $tooltip_data_gross_sales
						),
						array(
							'fill' => false,
							'label' => $this->language->get('ms_account_dashboard_total_orders'),
							'yAxisID' => 'total',
							'borderColor' => '#FFB15E',
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

					$no_results_error = $this->language->get('ms_account_dashboard_no_results_not_enough_data');

					break;

				case "top_products_views":
					$chart_data = $this->MsLoader->MsReport->getTopProductsByViewsAnalytics(array(
						'date_start' => isset($this->request->get['date_start']) ? $this->request->get['date_start'] : false,
						'date_end' => isset($this->request->get['date_end']) ? $this->request->get['date_end'] : false,
						'seller_id' => $this->customer->getId()
					));

					$type = 'horizontalBar';

					$total_views = $tooltip_data = array();
					$max_total_views = 0;

					if (!empty($chart_data)) {
						foreach ($chart_data as $data) {
							if ($data['total_views'] > $max_total_views) $max_total_views = $data['total_views'];

							$labels[] = $data['name'];
							$total_views[] = $data['total_views'];
							$tooltip_data[] = $this->language->get('ms_account_dashboard_column_total_views') . ": " . $data['total_views'];
						}
					}

					$step_total_views = pow(10, $this->MsLoader->MsHelper->getNumberOfSignificantDigits($max_total_views) - 1);
					$max = $this->MsLoader->MsHelper->ceiling($max_total_views, $step_total_views);

					$datasets = array(
						array(
							'label' => $this->language->get('ms_account_dashboard_column_total_views'),
							'backgroundColor' => '#F0D578',
							'data' => $total_views,
							'tooltip_data' => $tooltip_data
						)
					);

					$xAxes = array(
						array(
							'gridLines' => array(
								'display' => false
							),
							'ticks' => array(
								'min' => 0,
								'max' => $max,
								'stepSize' => $step_total_views
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

					$no_results_error = $this->language->get('ms_account_dashboard_no_results_no_data');

					break;

				case "top_products_sales":
					$chart_data = $this->MsLoader->MsReport->getTopProductsAnalytics(array(
						'date_start' => isset($this->request->get['date_start']) ? $this->request->get['date_start'] : false,
						'date_end' => isset($this->request->get['date_end']) ? $this->request->get['date_end'] : false,
						'seller_id' => $this->customer->getId()
					));

					$type = 'horizontalBar';

					$gross_sales = $tooltip_data = $data_formatted = array();
					$max_gross_sales = 0;

					if (!empty($chart_data)) {
						foreach ($chart_data as $data) {
							if ($data['gross_total'] > $max_gross_sales) $max_gross_sales = $data['gross_total'];

							$labels[] = $data['name'];
							$gross_sales[] = $this->currency->format($data['gross_total'], $this->config->get('config_currency'), '', false);
							$data_formatted[] = $this->currency->format($data['gross_total'], $this->config->get('config_currency'));
							$tooltip_data[] = $this->language->get('ms_account_dashboard_gross_sales') . ": " . $this->currency->format($data['gross_total'], $this->config->get('config_currency'));
						}
					}

					$step_gross_sales = pow(10, $this->MsLoader->MsHelper->getNumberOfSignificantDigits($max_gross_sales) - 1);
					$max = $this->MsLoader->MsHelper->ceiling($max_gross_sales, $step_gross_sales);

					$datasets = array(
						array(
							'label' => $this->language->get('ms_account_dashboard_column_gross'),
							'backgroundColor' => '#76BC5E',
							'data' => $gross_sales,
							'data_formatted' => $data_formatted,
							'tooltip_data' => $tooltip_data
						)
					);

					$xAxes = array(
						array(
							'gridLines' => array(
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

					$no_results_error = $this->language->get('ms_account_dashboard_no_results_no_data');

					break;
			}
		}

		$json['chart_data'] = array(
			'common' => array(
				'currency_symbol_left' => $this->currency->getSymbolLeft($this->config->get('config_currency')),
				'currency_symbol_right' => $this->currency->getSymbolRight($this->config->get('config_currency')),
				'no_results_error' => isset($no_results_error) ? $no_results_error : $this->language->get('ms_account_dashboard_no_results_no_data')
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

	private function _formatCurrencyTicks($min, $max, $step) {
		$ticks = array();
		for ($i = $max; $i >= $min; $i -= $step) {
			$ticks[] = $this->currency->format($i, $this->config->get('config_currency'));
		}

		return $ticks;
	}
}