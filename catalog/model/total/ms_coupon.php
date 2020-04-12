<?php
class ModelTotalMsCoupon extends Model {
	public function getTotal($total) {
		if (isset($this->session->data['ms_coupons'])) {
			$this->load->language('multiseller/multiseller');

			$cart_data = $this->getCartData();

			foreach ($this->session->data['ms_coupons'] as $seller_id => $code) {
				$coupon_info = $this->MsLoader->MsCoupon->getCoupons(array('seller_id' => $seller_id, 'code' => $code));

				if ($coupon_info && isset($cart_data[$seller_id])) {
					$coupon_validation = $this->validateCouponUsage($coupon_info, $cart_data[$seller_id]);

					if ($coupon_validation['is_available']) {
						$discount_total = 0;

						// @todo 8.12: think of taxes and shipping
						switch ($coupon_info['type']) {
							case MsCoupon::TYPE_DISCOUNT_PERCENT:
								$discount_total += ($coupon_validation['suborder_total'] * (float)$coupon_info['value'] / 100);
								break;

							case MsCoupon::TYPE_DISCOUNT_FIXED:
								$discount_total += (float)$coupon_info['value'];
								break;

							default:
								$discount_total += 0;
								break;
						}

						// If discount greater than total
						if ($discount_total > $total['total']) {
							$discount_total = $total['total'];
						}

						$total['totals'][] = array(
							'code'       => 'ms_coupon',
							'title'      => sprintf($this->language->get('ms_total_coupon_title'), $this->MsLoader->MsSeller->getSellerNickname($seller_id), $this->session->data['ms_coupons'][$seller_id]),
							'value'      => -$discount_total,
							'sort_order' => $this->config->get('ms_coupon_sort_order')
						);

						$total['total'] -= $discount_total;
					}
				}
			}
		}
	}

	public function confirm($order_info, $order_total) {
		$this->load->language('multiseller/multiseller');
		$coupon_code = '';

		$start = strpos($order_total['title'], '(') + 1;
		$end = strrpos($order_total['title'], ')');

		if ($start && $end) {
			$coupon_code = substr($order_total['title'], $start, $end - $start);
		}

		if ($coupon_code) {
			// Get all sellers from session with this coupon code
			foreach ($this->session->data['ms_coupons'] as $seller_id => $code) {
				if ((string)$code === (string)$coupon_code && (string)$order_total['title'] === (string)sprintf($this->language->get('ms_total_coupon_title'), $this->MsLoader->MsSeller->getSellerNickname($seller_id), $coupon_code)) {
					$coupon_info = $this->MsLoader->MsCoupon->getCoupons(array('seller_id' => $seller_id, 'code' => $coupon_code));

					if ($coupon_info) {
						// @todo 8.12: fix getSuborders method
						$suborder_id_query = $this->db->query("SELECT suborder_id FROM `" . DB_PREFIX . "ms_suborder` WHERE seller_id = " . (int)$seller_id . " AND order_id = " . (int)$order_info['order_id']);
						$suborder_id = isset($suborder_id_query->row['suborder_id']) ? $suborder_id_query->row['suborder_id'] : 0;

						$this->MsLoader->MsCoupon->createCouponHistory($coupon_info['coupon_id'], array(
							'order_id' => $order_info['order_id'],
							'suborder_id' => $suborder_id,
							'customer_id' => $order_info['customer_id'],
							'amount' => $order_total['value']
						));

						$this->MsLoader->MsCoupon->incrementTotalUses($coupon_info['coupon_id']);

						unset($this->session->data['ms_coupons'][$seller_id]);
					} else {
						return $this->config->get('config_fraud_status_id');
					}
				}
			}
		}
	}

	public function unconfirm($order_id) {
		$this->MsLoader->MsCoupon->deleteCouponHistory(array('order_id' => $order_id));
	}

	public function getCartData() {
		$cart_data = array();

		$cart_products = $this->cart->getProducts();
		foreach ($cart_products as $cart_product) {
			$seller_id = $this->MsLoader->MsProduct->getSellerId($cart_product['product_id']);

			$product_oc_categories = explode(',', $this->MsLoader->MsProduct->getProductOcCategories($cart_product['product_id']));
			foreach ($product_oc_categories as $oc_category_id) {
				$cart_data[$seller_id][$cart_product['product_id']]['oc_category_ids'][] = $oc_category_id;
			}

			if ($this->config->get('msconf_allow_seller_categories')) {
				$product_ms_categories = explode(',', $this->MsLoader->MsProduct->getProductMsCategories($cart_product['product_id']));
				foreach ($product_ms_categories as $ms_category_id) {
					$cart_data[$seller_id][$cart_product['product_id']]['ms_category_ids'][] = $ms_category_id;
				}
			}

			$cart_data[$seller_id][$cart_product['product_id']]['price'] = isset($cart_data[$seller_id][$cart_product['product_id']]['price']) ? (float)$cart_data[$seller_id][$cart_product['product_id']]['price'] + (float)$cart_product['total'] : (float)$cart_product['total'];
			$cart_data[$seller_id][$cart_product['product_id']]['seller_id'] = $seller_id;

			// Product Discounts
			$product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$cart_product['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity <= '" . (int)$cart_product['quantity'] . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");
			if ($product_discount_query->num_rows) {
				$cart_data[$seller_id][$cart_product['product_id']]['quantity_discount'] = true;
			}

			// Product Specials
			$product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$cart_product['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");
			if ($product_special_query->num_rows) {
				$cart_data[$seller_id][$cart_product['product_id']]['special_price'] = true;
			}
		}

		foreach ($cart_data as $seller_id => &$products) {
			foreach ($products as &$product) {
				if (!empty($product['oc_category_ids'])) $product['oc_category_ids'] = array_unique($product['oc_category_ids']);
				if (!empty($product['ms_category_ids'])) $product['ms_category_ids'] = array_unique($product['ms_category_ids']);
			}
		}

		return $cart_data;
	}

	public function validateCouponUsage($coupon_info = array(), $data = array()) {
		$result = array(
			'is_available' => true,
			'suborder_total' => 0,
			'validation_failed' => array()
		);

		/**
		 * Validate status
		 */
		if (!empty($coupon_info['status']) && (int)$coupon_info['status'] !== (int)MsCoupon::STATUS_ACTIVE) {
			$result['validation_failed']['status'] = 1;
		}

		/**
		 * Validate customer is logged in
		 */
		if (!empty($coupon_info['login_required']) && $coupon_info['login_required'] && !$this->customer->getId()) {
			$result['validation_failed']['login_required'] = true;
		}

		/**
		 * Validate max uses per customer
		 */
		if (!empty($coupon_info['max_uses_customer']) && $this->customer->getId()) {
			$total_uses_by_customer = $this->MsLoader->MsCoupon->getCouponHistory(array('coupon_id' => $coupon_info['coupon_id'], 'customer_id' => $this->customer->getId()));
			if (!empty($total_uses_by_customer[0]['total_rows']) && (int)$total_uses_by_customer[0]['total_rows'] >= (int)$coupon_info['max_uses_customer'])
				$result['validation_failed']['max_uses_customer'] = true;
		}

		/**
		 * Validate total uses / max uses
		 */
		if (!empty($coupon_info['total_uses']) && !empty($coupon_info['max_uses'])) {
			if ((int)$coupon_info['total_uses'] >= $coupon_info['max_uses'])
				$result['validation_failed']['total_uses'] = true;
		}

		/**
		 * Validate date
		 */
		$current_date = strtotime(date('Y-m-d'));

		// Validate date coupon becomes available
		if (!empty($coupon_info['date_start'])) {
			if ($current_date < strtotime($coupon_info['date_start']))
				$result['validation_failed']['date_start'] = true;
		}

		// Validate date coupon becomes unavailable
		if (!empty($coupon_info['date_end'])) {
			if ($current_date > strtotime($coupon_info['date_end']))
				$result['validation_failed']['date_end'] = true;
		}

		/**
		 * Validate categories and products
		 */
		if (!empty($data)) {
			$coupon_info = array_merge($coupon_info, array(
				'products' => $this->MsLoader->MsCoupon->getProductByCouponId($coupon_info['coupon_id']),
				'oc_categories' => $this->MsLoader->MsCoupon->getOcCategoryByCouponId($coupon_info['coupon_id']),
				'ms_categories' => $this->MsLoader->MsCoupon->getMsCategoryByCouponId($coupon_info['coupon_id'])
			));

			foreach ($data as $product_id => $product_data) {
				/**
				 * Validate product's special price or quantity discount
				 */
				if (!empty($product_data['special_price']) || !empty($product_data['quantity_discount'])) {
					unset($data[$product_id]);
					continue;
				}

				// @todo 8.12: change config name in future releases
				$categories_validation = $this->config->get('msconf_allow_seller_categories')
					? $this->validateCouponMsCategories($coupon_info['ms_categories'], $product_data['ms_category_ids'])
					: $this->validateCouponOcCategories($coupon_info['oc_categories'], $product_data['oc_category_ids']);

				if (!$categories_validation['is_available'] || empty($categories_validation['categories'])) {
					$product_validation = $this->validateCouponProduct($coupon_info['products'], $product_id);

					if(!$product_validation['is_available']) unset($data[$product_id]);
				}
			}

			/**
			 * Validate minimum order total
			 */
			if (!empty($coupon_info['min_order_total'])) {
				foreach ($data as $product_id => $product_data) {
					if (!empty($product_data['price']))
						$result['suborder_total'] += (float)$product_data['price'];
				}

				if ((float)$result['suborder_total'] === (float)0 || $this->currency->format($result['suborder_total'], $this->config->get('config_currency'), '', false) < $this->currency->format($coupon_info['min_order_total'], $this->config->get('config_currency'), '', false))
					$result['validation_failed']['min_order_total'] = true;
			}
		}

		if (!empty($result['validation_failed'])) {
			$result['is_available'] = false;
		}

		return $result;
	}

	public function validateCouponProduct($coupon_products, $product_id) {
		$is_available = true;
		$coupon_product_ids = array();

		if (!empty($coupon_products)) {
			foreach ($coupon_products as $key => $products) {
				foreach ($products as $product) {
					$coupon_product_ids[$key][] = $product['product_id'];
				}
			}

			if ((!empty($coupon_product_ids['include']) && !in_array($product_id, $coupon_product_ids['include'])) || (!empty($coupon_product_ids['exclude']) && in_array($product_id, $coupon_product_ids['exclude']))) {
				$is_available = false;
			}
		}

		return array('is_available' => $is_available, 'products' => $coupon_product_ids);
	}

	public function validateCouponOcCategories($coupon_oc_categories, $oc_category_ids) {
		$is_available = true;
		$coupon_oc_category_ids = array();

		if (!empty($coupon_oc_categories)) {
			foreach ($coupon_oc_categories as $key => $oc_categories) {
				foreach ($oc_categories as $oc_category) {
					$coupon_oc_category_ids[$key][] = $oc_category['oc_category_id'];
				}
			}

			foreach ($oc_category_ids as $oc_category_id) {
				if ((!empty($coupon_oc_category_ids['include']) && !in_array($oc_category_id, $coupon_oc_category_ids['include'])) || (!empty($coupon_oc_category_ids['exclude']) && in_array($oc_category_id, $coupon_oc_category_ids['exclude']))) {
					$is_available = false;
				}
			}
		}

		return array('is_available' => $is_available, 'categories' => $coupon_oc_category_ids);
	}

	public function validateCouponMsCategories($coupon_ms_categories, $ms_category_ids) {
		$is_available = true;
		$coupon_ms_category_ids = array();

		if (!empty($coupon_ms_categories)) {
			foreach ($coupon_ms_categories as $key => $ms_categories) {
				foreach ($ms_categories as $ms_category) {
					$coupon_ms_category_ids[$key][] = $ms_category['ms_category_id'];
				}
			}

			foreach ($ms_category_ids as $ms_category_id) {
				if ((!empty($coupon_ms_category_ids['include']) && !in_array($ms_category_id, $coupon_ms_category_ids['include'])) || (!empty($coupon_ms_category_ids['exclude']) && in_array($ms_category_id, $coupon_ms_category_ids['exclude']))) {
					$is_available = false;
				}
			}
		}

		return array('is_available' => $is_available, 'categories' => $coupon_ms_category_ids);
	}
}
