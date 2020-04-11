<?php
class MsTransaction extends Model {
	private $ms_logger;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->load->language('multiseller/multiseller');

		$this->ms_logger = new \MultiMerch\Logger\Logger();
	}

	public function __call($method, $arguments) {
		if(method_exists($this, $method)) {
			$this->ms_logger->debug('Function called: `' . $method . '(' . print_r($arguments, true) . ')`');

			return call_user_func_array(array($this, $method), $arguments);
		}
	}

	/**
	 * Gets order products by given order_id
	 *
	 * @param $order_id
	 * @return mixed
	 */
	private function _getOrderProducts($order_id) {
		$this->ms_logger->debug('Function called: `_getOrderProducts(' . $order_id . ')`');

		$sql = "SELECT
					op.*,
					COALESCE(mss.seller_id, 0) as `seller_id`
				FROM `" . DB_PREFIX . "order_product` op
				LEFT JOIN (SELECT product_id, seller_id FROM `" . DB_PREFIX . "ms_product`) mss
					ON (mss.product_id = op.product_id)
				WHERE op.order_id = " . (int)$order_id;

		$res = $this->db->query($sql);

		if($res->num_rows) {
			foreach ($res->rows as &$row) {
				$product_options_res = $this->db->query("
					SELECT
						GROUP_CONCAT(CONCAT_WS('_', `product_option_id`, `product_option_value_id`) SEPARATOR ',') as `product_options`
					FROM `" . DB_PREFIX . "order_option`
					WHERE order_id = '" . (int)$row['order_id'] . "'
						AND order_product_id = '" . (int)$row['order_product_id'] . "'
					GROUP BY order_product_id
				");

				$row['product_options'] = $product_options_res->num_rows ? $product_options_res->row['product_options'] : NULL;
			}
		}

		$this->ms_logger->debug('Leaving function: `_getOrderProducts`');

		return $res->rows;
	}

	private function _getTotalSellerProductsInCart($order_products = array()) {
		$tsp = array();
		foreach ($order_products as $order_product) {
			$s_id = (int)$order_product['seller_id'];
			$tsp[$s_id] = !isset($tsp[$s_id]) ? 1 : $tsp[$s_id] + 1;
		}

		return $tsp;
	}

	/**
	 * Processes operations with suborders of an order
	 *
	 * @param $order_id
	 * @param $seller_id
	 * @return mixed
	 */
	protected function processSuborder($order_id, $seller_id) {
		$this->ms_logger->debug('Function called: `processSuborder(' . $order_id . ', ' . $seller_id . ')`');

		// fetch suborder if it exists already
		$suborder = $this->MsLoader->MsSuborder->getSuborders(array(
			'order_id' => $order_id,
			'seller_id' => $seller_id,
			'include_abandoned' => 1,
			'single' => 1
		));

		$this->ms_logger->debug('Suborder: ' . print_r($suborder, true));

		if($suborder) {
			$suborder_id = $suborder['suborder_id'];

			$this->ms_logger->info('Suborder #' . $suborder_id . ' exists... ');
			$this->ms_logger->debug('Suborder #' . $suborder_id . ' exists... ');
		} else {
			$this->ms_logger->info('Creating suborder...');
			$this->ms_logger->debug('Suborder not exist...');

			$this->MsLoader->MsSuborder->createSuborder(array(
				'order_id' => $order_id,
				'seller_id' => $seller_id,
				'order_status_id' => $this->config->get('msconf_suborder_default_status')
			));

			$suborder_id = $this->db->getLastId();

			$this->ms_logger->info('Suborder #' . $suborder_id . ' created.');
			$this->ms_logger->debug('Suborder created: ' . $suborder_id);

			$this->MsLoader->MsSuborder->addSuborderHistory(array(
				'suborder_id' => $suborder_id,
				'order_status_id' => $this->config->get('msconf_suborder_default_status'),
				'comment' => $this->language->get('ms_transaction_order_created')
			));

			$this->ms_logger->debug('Suborder history created.');
		}

		$this->ms_logger->debug('Leaving function: `processSuborder`');

		return $suborder_id;
	}

	/**
	 * Calculates commissions for each order product
	 *
	 * @param $seller_id
	 * @param $order_product
	 * @param bool|array $coupon
	 * @return array
	 */
	protected function processCommission($seller_id, $order_product, $coupon = false) {
		$this->ms_logger->debug('Function called: `processCommission(' . $seller_id . ', ' . print_r($order_product, true) . ', ' . print_r($coupon, true) . ')`');

		$flat = $pct = $slr_net_amt = 0;

		$this->ms_logger->debug('Initial values... Flat: ' . $flat . '; Pct: ' . $pct . '; Slr_net_amt: ' . $slr_net_amt);

		if ($order_product['total'] > 0) {
			$commission_rates = array();

			// Catalog fee priority
			if($this->config->get('msconf_fee_priority') == 1) {
				$this->ms_logger->info('Catalog fee priority is set...');
				$this->ms_logger->debug('Catalog fee priority is set...');

				$msp_commission_id = $this->MsLoader->MsProduct->getProductCommissionId($order_product['product_id']);

				// Product fee
				if($msp_commission_id) {
					$commission_rates = $this->MsLoader->MsCommission->getCommissionRates($msp_commission_id);

					$this->ms_logger->info('Product commission rates are applied');
					$this->ms_logger->debug('Product commission rates are applied: ' . print_r($commission_rates, true));

					// Category fee
				} else {
					$msp_categories = $this->MsLoader->MsProduct->getProductOcCategories($order_product['product_id']);
					$commission_rates = $this->MsLoader->MsCategory->getOcCategoryCommission($msp_categories, MsCommission::RATE_SALE, array('price' => $order_product['total']));

					$this->ms_logger->info('Category commission rates are applied');
					$this->ms_logger->debug('Category commission rates are applied: ' . print_r($commission_rates, true));
				}
			}

			// Vendor fee priority
			if($this->config->get('msconf_fee_priority') == 2) {
				$this->ms_logger->info('Vendor fee priority is set...');
				$this->ms_logger->debug('Vendor fee priority is set...');

				$commission_rates = $this->MsLoader->MsCommission->calculateCommission(array('seller_id' => $seller_id));

				$this->ms_logger->info('Vendor commission rates are applied');
				$this->ms_logger->debug('Vendor commission rates are applied: ' . print_r($commission_rates, true));
			}

			if(!empty($commission_rates) && isset($commission_rates[MsCommission::RATE_SALE])) {
				$flat = $commission_rates[MsCommission::RATE_SALE]['flat'];
				$pct = $order_product['total'] * $commission_rates[MsCommission::RATE_SALE]['percent'] / 100;
			}

			$slr_net_amt = $order_product['total'] + $order_product['tax'] - ($flat + $pct);

			$this->ms_logger->debug('Updated values... Flat: ' . $flat . '; Pct: ' . $pct . '; Slr_net_amt: ' . $slr_net_amt);
		} else {
			$this->ms_logger->error('Order total is less or equals zero.');
		}

		// Calculate coupon discount if exists
		if ($coupon && $coupon['type'] == 'P'){
			$this->ms_logger->info('Coupon is applied');

			$flat -= $flat / 100 * $coupon['discount'];
			$pct -= $pct / 100 * $coupon['discount'];
			$slr_net_amt -= $slr_net_amt / 100 * $coupon['discount'];

			$this->ms_logger->debug('Coupon applied values... Flat: ' . $flat . '; Pct: ' . $pct . '; Slr_net_amt: ' . $slr_net_amt);
		}

		$this->ms_logger->debug('Leaving function: `processCommission`');

		return array(
			'flat' => $flat,
			'pct' => $pct,
			'slr_net_amt' => $slr_net_amt
		);
	}

	/**
	 * Processes shipping information for each order product
	 *
	 * @param $product_id
	 * @param $cart_id
	 * @return array
	 */
	protected function processShipping($product_id, $cart_id) {
		$this->ms_logger->debug('Function called: `processShipping(' . $product_id . ', ' . $cart_id . ')`');

		$fixed_method_id = $combined_method_id = NULL;
		$shipping_cost = 0;

		$fixed_rules = isset($this->session->data['ms_cart_product_shipping']['fixed']) ? $this->session->data['ms_cart_product_shipping']['fixed'] : false;
		$combined_rules = isset($this->session->data['ms_cart_product_shipping']['combined']) ? $this->session->data['ms_cart_product_shipping']['combined'] : false;

		$this->ms_logger->debug('Session shipping values... $fixed_rules: ' . print_r($fixed_rules, true) . '; $combined_rules: ' . print_r($combined_rules, true));

		// Fixed shipping rules
		if(isset($fixed_rules[$product_id][$cart_id]['shipping_method_id'])) {
			$fixed_method_id = $fixed_rules[$product_id][$cart_id]['shipping_method_id'];
		} else if(isset($this->session->data['ms_cart_product_shipping']['free'][$product_id])) {
			$fixed_method_id = 0;
		}

		// Combined shipping rules
		if(isset($combined_rules[$product_id]['shipping_method_id'])) {
			$combined_method_id = $combined_rules[$product_id]['shipping_method_id'];
		}

		// Shipping cost
		if(isset($fixed_rules[$product_id][$cart_id]['cost'])) {
			$shipping_cost = $fixed_rules[$product_id][$cart_id]['cost'];
		} else if(isset($combined_rules[$product_id]['cost'])) {
			$shipping_cost = $combined_rules[$product_id]['cost'];
		}

		$this->ms_logger->debug('Shipping values... $fixed_method_id: ' . $fixed_method_id . '; $combined_method_id: ' . $combined_method_id . '; $shipping_cost: ' . $shipping_cost);

		$this->ms_logger->debug('Leaving function: `processShipping`');

		return array(
			'fixed_method_id' => $fixed_method_id,
			'combined_method_id' => $combined_method_id,
			'shipping_cost' => $shipping_cost
		);
	}

	/**
	 * Processes MultiMerch coupon discount if applied.
	 *
	 * @param	int		$order_id						Order id.
	 * @param	int		$seller_id						Seller id.
	 * @param	array	$order_product					Seller order product.
	 * @param	int		$total_seller_products_in_order	Total count of seller's products in an order.
	 * @return	float									Absolute value of discount amount. If order product is passed
	 * 													returns its particular discount, else returns total discount of
	 * 													order.
	 */
	protected function processMsCoupon($order_id, $seller_id, $order_product = array(), $total_seller_products_in_order = 0) {
		$this->load->model('account/order');
		$order_totals = $this->model_account_order->getOrderTotals($order_id);
		$seller_nickname = $this->MsLoader->MsSeller->getSellerNickname($seller_id);
		$ms_coupon_discount = 0;

		foreach ($order_totals as $order_total) {
			if ($order_total['code'] == 'ms_coupon') {
				$coupon_code = '';

				$start = strpos($order_total['title'], '(') + 1;
				$end = strrpos($order_total['title'], ')');

				if ($start && $end) {
					$coupon_code = substr($order_total['title'], $start, $end - $start);
				}

				if ($coupon_code && (string)$order_total['title'] === (string)sprintf($this->language->get('ms_total_coupon_title'), $seller_nickname, $coupon_code)) {
					if (!empty($order_product)) {
						$coupon_info = $this->MsLoader->MsCoupon->getCoupons(array('seller_id' => $seller_id, 'code' => $coupon_code));

						if (!empty($coupon_info)) {
							if ((int)$coupon_info['type'] === (int)MsCoupon::TYPE_DISCOUNT_PERCENT) {
								$ms_coupon_discount = $order_product['total'] * $coupon_info['value'] / 100;
							} elseif ((int)$coupon_info['type'] === (int)MsCoupon::TYPE_DISCOUNT_FIXED) {
								$ms_coupon_discount = $total_seller_products_in_order > 0 ? round($coupon_info['value'] / (int)$total_seller_products_in_order, 2) : 0;
							}
						}
					} else {
						$ms_coupon_discount = $order_total['value'];
					}
				}
			}
		}

		$this->ms_logger->debug("Coupon discount for order_id ($order_id), seller_id ($seller_id): $ms_coupon_discount");

		return abs((float)$ms_coupon_discount);
	}

	/**
	 * Creates MsOrder data for each order product
	 *
	 * @param array $data
	 */
	protected function processMsOrderData($data = array()) {
		$this->ms_logger->debug('Function called: `processMsOrderData(' . print_r($data, true) . ')`');

		$seller_id = isset($data['seller_id']) ? (int)$data['seller_id'] : false;
		$suborder_id = isset($data['suborder_id']) ? (int)$data['suborder_id'] : false;
		$order_product = isset($data['order_product']) ? $data['order_product'] : false;
		$commissions = isset($data['commissions']) ? $data['commissions'] : false;
		$shipping = isset($data['shipping']) ? $data['shipping'] : false;
		$ms_coupon_discount = isset($data['ms_coupon_discount']) ? $data['ms_coupon_discount'] : 0;

		$this->ms_logger->debug('MsOrder data values... $seller_id: ' . $seller_id . '; $suborder_id: ' . $suborder_id . '; $order_product: ' . print_r($order_product, true) . '; $commissions: ' . print_r($commissions, true) . '; $shipping: ' . print_r($shipping, true));

		$order_data = $this->MsLoader->MsOrderData->getOrderData(array(
			'product_id' => $order_product['product_id'],
			'order_id' => $order_product['order_id'],
			'order_product_id' => $order_product['order_product_id']
		));

		$this->ms_logger->debug('Order data: ' . print_r($order_data, true));

		if (!$order_data) {
			$this->ms_logger->info('Creating order product data...');
			$this->ms_logger->debug('Creating order product data...');

			$this->MsLoader->MsOrderData->addOrderProductData(
				$order_product['order_id'],
				$order_product['product_id'],
				array(
					'order_product_id' => $order_product['order_product_id'],
					'seller_id' => $seller_id,
					'suborder_id' => $suborder_id,
					'store_commission_flat' => $commissions['flat'],
					'store_commission_pct' => $commissions['pct'],
					'seller_net_amt' => $commissions['slr_net_amt'] - (float)$ms_coupon_discount,
					'order_status_id' => isset($data['order_status_id']) ? (int)$data['order_status_id'] : 0
				)
			);

			$this->ms_logger->debug('Product order data created.');

			$this->ms_logger->debug('Creating order product shipping data...');

			$this->MsLoader->MsOrderData->addOrderProductShippingData(
				$order_product['order_id'],
				$order_product['product_id'],
				array(
					'order_product_id' => $order_product['order_product_id'],
					'fixed_shipping_method_id' => $shipping['fixed_method_id'],
					'combined_shipping_method_id' => $shipping['combined_method_id'],
					'shipping_cost' => $shipping['shipping_cost']
				)
			);

			$this->ms_logger->info('Order product data created.');
			$this->ms_logger->debug('Product shipping data created.');
		} else {
			$this->ms_logger->error('Order data exists.');
		}

		$this->ms_logger->debug('Leaving function: `processMsOrderData`');
	}

	/**
	 * Processes operations with coupon if it was used
	 *
	 * @return array|bool
	 */
	protected function getOrderCoupon() {
		$this->ms_logger->debug('Function called: `getOrderCoupon()`');

		if (isset($this->session->data['coupon'])){
			$coupon_query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "coupon` WHERE `code` = '" . $this->db->escape($this->session->data['coupon']) . "'");
			$coupon = $coupon_query->row;
		} else {
			$coupon = false;
		}

		$this->ms_logger->debug('Leaving function: `getOrderCoupon`');

		return $coupon;
	}

	/**
	 * Processes operations with seller balance.
	 *
	 * @param	int			$type		Transaction (balance) type.
	 * @param	int|bool	$subtype	If transaction type is Refund, then this is a type of transaction to be refunded.
	 * @param	array		$data		Conditions.
	 * @return	int|bool				Transaction id if new transaction was created, false if not.
	 */
	protected function processBalance($type, $subtype = false, $data = array()) {
		$this->ms_logger->info('Start processing balance for sellers...');
		$this->ms_logger->debug('Function called: `processBalance(' . $type . ', ' . $subtype . ', ' . print_r($data, true) . ')`');

		$seller_id = isset($data['seller_id']) ? (int)$data['seller_id'] : false;
		$order_id = isset($data['order_id']) ? (int)$data['order_id'] : false;
		$order_product = isset($data['order_product']) ? $data['order_product'] : false;
		$tsp = isset($data['tsp']) ? $data['tsp'] : false;

		if(!$seller_id || !$order_id) {
			$this->ms_logger->error("Unable to create balance record. Some of the required conditions are not specified: seller_id - " . (int)$seller_id . "; order_id - " . (int)$order_id . "; order_product - " . print_r($order_product, true));
			return;
		}

		$this->ms_logger->debug('Checking if any balance entry exists with defined conditions...');

		$balance_entry = $this->MsLoader->MsBalance->getBalanceEntry(array(
			'seller_id' => $seller_id,
			'product_id' => $order_product['product_id'] ?: NULL,
			'order_id' => $order_id,
			'order_product_id' => $order_product['order_product_id'] ?: NULL,
			'balance_type' => (int)$type
		));

		if (!$balance_entry) {
			$this->ms_logger->debug('Balance entry not exists. Creating a new one...');

			$order_data = $this->MsLoader->MsOrderData->getOrderData(array(
				'product_id' => $order_product['product_id'] ?: NULL,
				'order_id' => $order_id,
				'order_product_id' => $order_product['order_product_id'] ?: NULL,
				'single' => 1
			));

			$amount = 0;
			$description = '';

			if ((int)$type !== (int)MsBalance::MS_BALANCE_TYPE_MSCOUPON && (!isset($order_data['seller_net_amt']) || !isset($order_data['store_commission_flat']) || !isset($order_data['store_commission_pct']))) {
				$this->ms_logger->error("Unable to calculate seller's and store's commissions. Order info: " . print_r($order_data, true));
			}

			$commission = isset($order_data['store_commission_flat']) && isset($order_data['store_commission_pct']) ? $order_data['store_commission_flat'] + $order_data['store_commission_pct'] : 0;

			switch($type) {
				case MsBalance::MS_BALANCE_TYPE_SALE:
					$this->ms_logger->debug('Balance type: Sale');

					// check ms_coupon value just in case
					$coupon_discount = $this->processMsCoupon($order_id, $seller_id, $order_product, isset($tsp[$seller_id]) ? $tsp[$seller_id] : 0);

					$amount = isset($order_data['seller_net_amt']) ? $order_data['seller_net_amt'] + $coupon_discount : 0;
					$description .= sprintf($this->language->get('ms_transaction_sale'), ($order_product['quantity'] > 1 ? $order_product['quantity'] . ' x ' : '')  . $order_product['name'], $this->currency->format($commission, $this->config->get('config_currency')));
					break;

				case MsBalance::MS_BALANCE_TYPE_SHIPPING:
					$this->ms_logger->debug('Balance type: Shipping');

					$amount = $order_data['shipping_cost'];
					$description .= sprintf($this->language->get('ms_transaction_shipping'), ($order_product['quantity'] > 1 ? $order_product['quantity'] . ' x ' : '')  . $order_product['name'], $this->currency->format($commission, $this->config->get('config_currency')));
					break;

				case MsBalance::MS_BALANCE_TYPE_MSCOUPON:
					$this->ms_logger->debug('Balance type: MsCoupon');

					$coupon_ids = array();
					$seller_coupons = $this->MsLoader->MsCoupon->getCoupons(array('seller_id' => $seller_id));
					foreach ($seller_coupons as $seller_coupon) {
						$coupon_ids[] = $seller_coupon['coupon_id'];
					}

					$coupon_history = $this->MsLoader->MsCoupon->getCouponHistory(array(
						'coupon_ids' => $coupon_ids,
						'order_id' => $order_id,
						'single' => 1
					));

					if (!empty($coupon_history)) {
						$amount = $coupon_history['amount'];
					}
					$description .= sprintf($this->language->get('ms_transaction_coupon'));

					break;

				case MsBalance::MS_BALANCE_TYPE_REFUND:
					$this->ms_logger->debug('Balance type: Refunding');

					if($subtype) {
						$subtype_balance_entry = $this->MsLoader->MsBalance->getBalanceEntry(array(
							'seller_id' => $seller_id,
							'product_id' => $order_product['product_id'] ?: NULL,
							'order_id' => $order_id,
							'order_product_id' => $order_product['order_product_id'] ?: NULL,
							'balance_type' => (int)$subtype
						));

						if($subtype_balance_entry) {
							// check ms_coupon value just in case
							$amount = -1 * $subtype_balance_entry['amount'];

							switch ($subtype) {
								case MsBalance::MS_BALANCE_TYPE_SALE:
									$this->ms_logger->debug('Balance type: Refunding Sale');

									$coupon_discount = $this->processMsCoupon($order_id, $seller_id, $order_product, isset($tsp[$seller_id]) ? $tsp[$seller_id] : 0);
									$amount += $coupon_discount;

									$description .= sprintf($this->language->get('ms_transaction_refund'), ($order_product['quantity'] > 1 ? $order_product['quantity'] . ' x ' : '')  . $order_product['name']);
									break;

								case MsBalance::MS_BALANCE_TYPE_SHIPPING:
									$this->ms_logger->debug('Balance type: Refunding Shipping');

									$description .= sprintf($this->language->get('ms_transaction_shipping_refund'), ($order_product['quantity'] > 1 ? $order_product['quantity'] . ' x ' : '')  . $order_product['name']);
									break;

								case MsBalance::MS_BALANCE_TYPE_MSCOUPON:
									$this->ms_logger->debug('Balance type: Refunding Coupon');

									$description .= sprintf($this->language->get('ms_transaction_coupon_refund'));
									break;

								default:
									$this->ms_logger->error("Unable to create balance record. Wrong subtype for refund has been passed: " . (int)$subtype);
									break;
							}
						}
					}
					break;

				default:
					$this->ms_logger->error("Unable to create balance record. Wrong type has been passed: " . (int)$type);
					break;
			}

			$this->ms_logger->info('Creating balance entry...');
			$this->ms_logger->debug('Creating balance entry... Values: ' . print_r(array(
				'order_id' => $order_id,
				'product_id' => $order_product['product_id'] ?: NULL,
				'order_product_id' => $order_product['order_product_id'] ?: NULL,
				'balance_type' => (int)$type,
				'amount' => $amount,
				'description' => $description
			), true));

			if ((int)$type === (int)MsBalance::MS_BALANCE_TYPE_MSCOUPON && (float)$amount === (float)0) {
				$transaction_created = false;
			} else {
				$transaction_created = $this->MsLoader->MsBalance->addBalanceEntry($seller_id, array(
					'order_id' => $order_id,
					'product_id' => $order_product['product_id'] ?: NULL,
					'order_product_id' => $order_product['order_product_id'] ?: NULL,
					'balance_type' => (int)$type,
					'amount' => $amount,
					'description' => $description
				));
			}

			$this->ms_logger->info('Balance entry created.');
			$this->ms_logger->debug('Balance entry created.');
		} else {
			$this->ms_logger->debug('Balance entry exists.');

			$transaction_created = false;
		}

		$this->ms_logger->info('Processing balance for sellers is finished.');
		$this->ms_logger->debug('Leaving function: `processBalance`');

		return $transaction_created;
	}

	/**
	 * Represents main flow for MsOrder creation
	 *
	 * @param $order_id
	 * @return array
	 */
	protected function createMsOrderDataEntries($order_id) {
		$this->ms_logger->info('Start creating MsOrder data entries...');
		$this->ms_logger->debug('Function called: `createMsOrderDataEntries(' . $order_id . ')`');

		$cart_products = $this->cart->getProducts();
		$ms_order_products = $this->_getOrderProducts($order_id);

		foreach ($ms_order_products as &$ms_order_product) {
			foreach ($cart_products as $k => $cart_product) {
				if ((int)$ms_order_product['product_id'] !== (int)$cart_product['product_id'])
					continue;

				if($ms_order_product['product_options']) {
					// Get order product options from `oc_order_option` table
					$oo_order_product_options = array();
					$tmp_1 = explode(',', $ms_order_product['product_options']);
					foreach ($tmp_1 as $v) {
						$tmp_2 = explode('_', $v);
						$oo_order_product_options[$tmp_2[0]] = $tmp_2[1];
					}

					foreach ($cart_product['option'] as $option) {
						if(isset($oo_order_product_options[$option['product_option_id']]) && (int)$oo_order_product_options[$option['product_option_id']] === (int)$option['product_option_value_id']) {
							$ms_order_product['cart_id'] = $cart_product['cart_id'];
							unset($cart_products[$k]);
						}
					}
				} else {
					$ms_order_product['cart_id'] = $cart_product['cart_id'];
					unset($cart_products[$k]);
				}
			}
		}

		$this->ms_logger->debug('MsOrderProducts: ' . print_r($ms_order_products, true));

		// fetch order sellers
		$sellers = array();
		$result = array(
			'sellers_net_amt_total' => 0,
			'commission_total' => 0,
			'sellers_shipping_cost_total' => 0
		);

		// OpenCart coupon
		$coupon = $this->getOrderCoupon();

		// Get total seller products in cart
		$tsp = $this->_getTotalSellerProductsInCart($ms_order_products);

		foreach ($ms_order_products as $key => $order_product) {
			$this->ms_logger->debug('In $ms_order_products loop. Product: ' . print_r($order_product, true));

			$seller_id = (int)$order_product['seller_id'];

			if (!$seller_id) {
				$this->ms_logger->debug("Product " . $order_product['product_id'] . "doesn't belong to any seller - move to the next product");
				continue;
			}

			$this->ms_logger->info('Start processing suborders for sellers...');
			$suborder_id = $this->processSuborder($order_id, $seller_id);
			$this->ms_logger->info('Processing suborders for sellers is finished.');

			$this->ms_logger->info('Start processing commissions...');
			$commissions = $this->processCommission($seller_id, $order_product, $coupon);
			$this->ms_logger->info('Processing commissions is finished.');

			$this->ms_logger->info('Start processing shipping...');
			$shipping = $this->processShipping($order_product['product_id'], $order_product['cart_id']);
			$this->ms_logger->info('Processing shipping is finished.');

			$this->ms_logger->info('Start processing MultiMerch coupons...');
			$ms_coupon_discount = $this->processMsCoupon($order_id, $seller_id, $order_product, isset($tsp[$seller_id]) ? $tsp[$seller_id] : 0);
			$this->ms_logger->info('Processing MultiMerch coupons is finished.');

			$this->ms_logger->debug('Product before processMsOrderData: ' . print_r($order_product, true));

			$this->ms_logger->info('Start processing MsOrder data for each order product...');
			$this->processMsOrderData(array(
				'seller_id' => $seller_id,
				'suborder_id' => $suborder_id,
				'order_product' => $order_product,
				'commissions' => $commissions,
				'shipping' => $shipping,
				'ms_coupon_discount' => $ms_coupon_discount
			));
			$this->ms_logger->info('Processing MsOrder data for each order product is finished.');

			// For ms_pp_adaptive
			if(!isset($sellers[$seller_id])) {
				$sellers[$seller_id] = array(
					'seller_net_amt_total' => 0,
					'seller_commission_total' => 0,
					'shipping_cost_total' => 0,
					'ms_coupon_discount' => 0,
					'products' => array()
				);
			}

			$sellers[$seller_id]['seller_net_amt_total'] += $commissions['slr_net_amt'];
			$sellers[$seller_id]['seller_commission_total'] += ($commissions['flat'] + $commissions['pct']);
			$sellers[$seller_id]['shipping_cost_total'] += $shipping['shipping_cost'];
			$sellers[$seller_id]['ms_coupon_discount'] += $ms_coupon_discount;
			$sellers[$seller_id]['products'][] = array(
				'store_commission_flat' => $commissions['flat'],
				'store_commission_pct' => $commissions['pct'],
				'seller_net_amt' => $commissions['slr_net_amt'],
				'fixed_shipping_method_id' => $shipping['fixed_method_id'],
				'combined_shipping_method_id' => $shipping['combined_method_id'],
				'shipping_cost' => $shipping['shipping_cost']
			);

			$result['sellers_net_amt_total'] += $commissions['slr_net_amt'];
			$result['commission_total'] += ($commissions['flat'] + $commissions['pct']);
			$result['sellers_shipping_cost_total'] += $shipping['shipping_cost'];
		}

		$this->ms_logger->debug('End $ms_order_products loop... ');

		$result['sellers'] = $sellers;

		$this->ms_logger->info('Finish creating MsOrder data entries.');
		$this->ms_logger->debug('Leaving function: `processSuborder`');

		return $result;
	}

	/**
	 * Represents flow for MsOrder update from admin-side
	 *
	 * @param $order_id
	 * @return bool
	 */
	protected function adminUpdateMsOrderDataEntries($order_id) {
		$this->ms_logger->info('Start updating MsOrder data entries from admin side...');
		$this->ms_logger->debug('Function called: `adminUpdateMsOrderDataEntries(' . $order_id . ')`');

		$ms_order_products = $this->_getOrderProducts($order_id);

		$this->ms_logger->debug('MsOrderProducts: ' . print_r($ms_order_products, true));

		foreach ($ms_order_products as $order_product) {
			$this->ms_logger->debug('In $ms_order_products loop. Product: ' . print_r($order_product, true));

			$seller_id = (int)$order_product['seller_id'];

			if (!$seller_id) {
				$this->ms_logger->debug("Product " . $order_product['product_id'] . "doesn't belong to any seller - move to the next product");
				continue;
			}

			$suborder = $this->MsLoader->MsSuborder->getSuborders(array('order_id' => $order_id, 'seller_id' => $seller_id, 'include_abandoned' => 1, 'single' => 1));

			if (isset($suborder['suborder_id']) && $suborder['suborder_id']){
				$this->ms_logger->debug('Updating MsOrder data entries...');

				$this->db->query("UPDATE " . DB_PREFIX . "ms_order_product_data
					SET order_product_id = " . (int)$order_product['order_product_id'] . "
					WHERE order_id = " . (int)$order_product['order_id'] . "
						AND product_id = " . (int)$order_product['product_id']
				);

				$this->ms_logger->debug('Updating MsOrder shipping data entries...');

				$this->db->query("UPDATE " . DB_PREFIX . "ms_order_product_shipping_data SET
					order_product_id = " . (int)$order_product['order_product_id'] . " WHERE
					order_id = " . (int)$order_product['order_id'] . " AND
					product_id = " . (int)$order_product['product_id']
				);
			}
		}

		$this->ms_logger->info('Finish updating MsOrder data entries from admin side.');
		$this->ms_logger->debug('Leaving function: `adminUpdateMsOrderDataEntries`');

		return true;
	}

	/**
	 * Represents flow for MsOrder seller balance entries creation for each order product.
	 *
	 *
	 * @param	int			$order_id				Main order id.
	 * @param	int			$order_status_id		Main order status id.
	 * @param	int|bool	$suborder_id			Suborder id.
	 * @param	int|bool	$suborder_status_id		Suborder status id.
	 * @return	array								Array with either transaction ids or false values, standing for transactions:
	 * 												sale_transaction_created, shipping_transaction_created,
	 * 												sale_transaction_refunded and shipping_transaction_refunded.
	 */
	public function createMsOrderBalanceEntries($order_id, $order_status_id, $suborder_id = false, $suborder_status_id = false) {
		$sale_transaction_created = $shipping_transaction_created = $sale_transaction_refunded = $shipping_transaction_refunded = false;
		$ms_coupon_transaction_created = $ms_coupon_transaction_refunded = false;

		$this->ms_logger->info('Start creating MsOrder balance entries...');
		$this->ms_logger->debug('Function called: `createMsOrderBalanceEntries(' . $order_id . ', ' . $order_status_id . ')`');

		$ms_order_products = $this->_getOrderProducts($order_id);

		$this->ms_logger->debug('MsOrderProducts: ' . print_r($ms_order_products, true));

		// Credit order statuses
		$msconf_credit_order_statuses = $this->config->get('msconf_credit_order_statuses');

		// Debit order statuses
		$msconf_debit_order_statuses = $this->config->get('msconf_debit_order_statuses');

		// Get total seller products in cart
		$tsp = $this->_getTotalSellerProductsInCart($ms_order_products);

		foreach ($ms_order_products as $order_product) {
			$seller_id = (int)$order_product['seller_id'];

			if (!$seller_id) {
				$this->ms_logger->debug("Product " . $order_product['product_id'] . "doesn't belong to any seller - move to the next product");
				continue;
			}

			// For balance entry creation
			$conditions = array(
				'order_id' => $order_id,
				'seller_id' => $seller_id,
				'order_product' => $order_product,
				'tsp' => $tsp
			);

			$this->ms_logger->debug('Conditions for balance entry creation: ' . print_r($conditions, true));

			if ((!$suborder_status_id && in_array($order_status_id, !empty($msconf_credit_order_statuses['oc']) ? $msconf_credit_order_statuses['oc'] : array(-1))) || ($suborder_status_id && in_array($suborder_status_id, !empty($msconf_credit_order_statuses['ms']) ? $msconf_credit_order_statuses['ms'] : array(-1)))) {
				// check adaptive payments
				$request = $this->MsLoader->MsPgRequest->getRequests(array(
					'seller_id' => $seller_id,
					'order_id' => $order_id,
					'request_type' => array(MsPgRequest::TYPE_SALE),
					'request_status' => array(MsPgRequest::STATUS_PAID),
					'single' => 1
				));

				if ($request)
					continue;

				$sale_transaction_created = $this->processBalance(MsBalance::MS_BALANCE_TYPE_SALE, false, $conditions);

				if ($this->config->get('msconf_allow_seller_coupons')) {
					$ms_coupon_transaction_created = $this->processBalance(MsBalance::MS_BALANCE_TYPE_MSCOUPON, false, array(
						'order_id' => $order_id,
						'seller_id' => $seller_id
					));
				}

				if((int)$this->config->get('msconf_shipping_type') == 2) {
					$shipping_transaction_created = $this->processBalance(MsBalance::MS_BALANCE_TYPE_SHIPPING, false, $conditions);
				}
			} else if ((!$suborder_status_id && in_array($order_status_id, !empty($msconf_debit_order_statuses['oc']) ? $msconf_debit_order_statuses['oc'] : array(-1))) || ($suborder_status_id && in_array($suborder_status_id, !empty($msconf_debit_order_statuses['ms']) ? $msconf_debit_order_statuses['ms'] : array(-1)))) {
				$sale_transaction_refunded = $this->processBalance(MsBalance::MS_BALANCE_TYPE_REFUND, MsBalance::MS_BALANCE_TYPE_SALE, $conditions);

				if ($this->config->get('msconf_allow_seller_coupons')) {
					$ms_coupon_transaction_refunded = $this->processBalance(MsBalance::MS_BALANCE_TYPE_REFUND, MsBalance::MS_BALANCE_TYPE_MSCOUPON, array(
						'order_id' => $order_id,
						'seller_id' => $seller_id
					));
				}

				if((int)$this->config->get('msconf_shipping_type') == 2) {
					$shipping_transaction_refunded = $this->processBalance(MsBalance::MS_BALANCE_TYPE_REFUND, MsBalance::MS_BALANCE_TYPE_SHIPPING, $conditions);
				}
			}
		}

		$this->ms_logger->info('Finish creating MsOrder balance entries.');
		$this->ms_logger->debug('Leaving function: `createMsOrderBalanceEntries`');

		// @todo: Check why is always false returned
		return array($sale_transaction_created, $shipping_transaction_created, $sale_transaction_refunded, $shipping_transaction_refunded);
	}
}