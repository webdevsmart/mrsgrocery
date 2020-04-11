<?php

class MsCoupon extends Model {
	const STATUS_ACTIVE = 1;
	const STATUS_DISABLED = 2;

	const TYPE_DISCOUNT_PERCENT = 1;
	const TYPE_DISCOUNT_FIXED = 2;

	/**
	 * Gets list of coupons.
	 *
	 * @param	array	$data	Conditions.
	 * @param	array	$sort	Data for sorting or filtering results.
	 * @return	array			List of coupons.
	 */
	public function getCoupons($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$result = $this->db->query("
			SELECT
				SQL_CALC_FOUND_ROWS
				msc.*,
				mss.nickname as `seller`
			FROM `" . DB_PREFIX . "ms_coupon` msc
			LEFT JOIN (SELECT seller_id, nickname FROM `" . DB_PREFIX . "ms_seller`) mss
				ON (mss.seller_id = msc.seller_id)
			WHERE 1 = 1"

			. (isset($data['coupon_id']) ? " AND msc.coupon_id = '" . (int)$data['coupon_id'] . "'" : "")
			. (isset($data['seller_id']) ? " AND msc.seller_id = '" . (int)$data['seller_id'] . "'" : "")
			. (isset($data['status']) ? " AND msc.status = '" . (int)$data['status'] . "'" : "")
			. (isset($data['code']) ? " AND msc.code LIKE '" . $this->db->escape($data['code']) . "'" : "")

			. $filters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) && $sort['limit'] >= 0 ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '')
		);

		$total = $this->db->query("SELECT FOUND_ROWS() as total_rows");
		if ($result->rows) {
			$result->rows[0]['total_rows'] = $total->row['total_rows'];

			if(isset($data['coupon_id'])) {
				foreach ($result->rows as &$row) {
					$row['customers'] = $this->getCustomerByCouponId($row['coupon_id']);
					$row['products'] = $this->getProductByCouponId($row['coupon_id']);
					$row['oc_categories'] = $this->getOcCategoryByCouponId($row['coupon_id']);
					$row['ms_categories'] = $this->getMsCategoryByCouponId($row['coupon_id']);
				}
			}
		}

		return $result->num_rows && (isset($data['coupon_id']) || isset($data['code'])) ? $result->rows[0] : $result->rows;
	}

	/**
	 * Creates coupon entries in DB.
	 * Updates existing entries if `coupon_id` is passed in $data.
	 *
	 * Required items to be passed in $data array:
	 * - 'name': name that identifies coupon
	 * - 'code': coupon code (e.g., ABC-XYZ-1234)
	 * - 'type': discount type (percent or fixed)
	 * - 'value': discount value (e.g., 10% or 10$)
	 * - 'date_start': the date from which coupon becomes available for customers
	 * - 'max_uses': maximum amount of uses for a coupon
	 * - 'status': coupon status
	 *
	 * @param	array	$data			Conditions.
	 * @return	int		$coupon_id		Coupon id.
	 */
	public function createOrUpdateCoupon($data = array()) {
		$this->db->query("
			INSERT INTO `" . DB_PREFIX . "ms_coupon`
			SET `name` = '" . $this->db->escape($data['name']) . "',
				`code` = '" . $this->db->escape($data['code']) . "',
				`type` = " . (int)$data['type'] . ",
				`value` = " . (float)$this->currency->format($data['value'], $this->config->get('config_currency'), '', FALSE) . ",
				`status` = " . (int)$data['status'] . ",
				`date_created` = NOW()"

			. (!empty($data['coupon_id']) ? ", `coupon_id` = " . (int)$data['coupon_id'] : "")
			. (!empty($data['seller_id']) ? ", `seller_id` = " . (int)$data['seller_id'] : "")
			. (!empty($data['description']) ? ", `description` = '" . $this->db->escape($data['description']) . "'" : "")
			. (!empty($data['date_start']) ? ", `date_start` = '" . $this->db->escape($data['date_start']) . "'" : "")
			. (!empty($data['date_end']) ? ", `date_end` = '" . $this->db->escape($data['date_end']) . "'" : "")
			. (!empty($data['max_uses']) ? ", `max_uses` = " . (int)$data['max_uses'] : "")
			. (!empty($data['max_uses_customer']) ? ", `max_uses_customer` = " . (int)$data['max_uses_customer'] : "")
			. (!empty($data['min_order_total']) ? ", `min_order_total` = " . (float)$data['min_order_total'] : "")
			. (!empty($data['login_required']) ? ", `login_required` = " . (int)$data['login_required'] : "")


			. " ON DUPLICATE KEY UPDATE
				`name` = '" . $this->db->escape($data['name']) . "',
				`code` = '" . $this->db->escape($data['code']) . "',
				`type` = " . (int)$data['type'] . ",
				`value` = " . (float)$this->currency->format($data['value'], $this->config->get('config_currency'), '', FALSE) . ",
				`status` = " . (int)$data['status']

			. (!empty($data['seller_id']) ? ", `seller_id` = " . (int)$data['seller_id'] : "")
			. (!empty($data['description']) ? ", `description` = '" . $this->db->escape($data['description']) . "'" : "")
			. (!empty($data['date_start']) ? ", `date_start` = '" . $this->db->escape($data['date_start']) . "'" : ", `date_start` = NULL")
			. (!empty($data['date_end']) ? ", `date_end` = '" . $this->db->escape($data['date_end']) . "'" : ", `date_end` = NULL")
			. (!empty($data['max_uses']) ? ", `max_uses` = " . (int)$data['max_uses'] : ", `max_uses` = NULL")
			. (!empty($data['max_uses_customer']) ? ", `max_uses_customer` = " . (int)$data['max_uses_customer'] : ", `max_uses_customer` = NULL")
			. (!empty($data['min_order_total']) ? ", `min_order_total` = " . (float)$data['min_order_total'] : ", `min_order_total` = 0")
			. (!empty($data['login_required']) ? ", `login_required` = " . (int)$data['login_required'] : "")
		);

		$coupon_id = !empty($data['coupon_id']) ? (int)$data['coupon_id'] : $this->db->getLastId();

		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_coupon_customer` WHERE `coupon_id` = '" . (int)$coupon_id . "'");
		if(isset($data['customers'])) {
			foreach ($data['customers'] as $key => $customers) {
				if((string)$key === 'exclude' || (string)$key === 'include') {
					foreach ($customers as $customer_id) {
						$this->db->query("
							INSERT INTO `" . DB_PREFIX . "ms_coupon_customer`
							SET `coupon_id` = " . (int)$coupon_id . ",
								`customer_id` = " . (int)$customer_id . ",
								`exclude` = " . ((string)$key === 'exclude' ? 1 : 0) . "
						");
					}
				}
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_coupon_product` WHERE `coupon_id` = '" . (int)$coupon_id . "'");
		if(isset($data['products'])) {
			foreach ($data['products'] as $key => $products) {
				if((string)$key === 'exclude' || (string)$key === 'include') {
					foreach ($products as $product_id) {
						$this->db->query("
							INSERT INTO `" . DB_PREFIX . "ms_coupon_product`
							SET `coupon_id` = " . (int)$coupon_id . ",
								`product_id` = " . (int)$product_id . ",
								`exclude` = " . ((string)$key === 'exclude' ? 1 : 0) . "
						");
					}
				}
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_coupon_oc_category` WHERE `coupon_id` = '" . (int)$coupon_id . "'");
		if(isset($data['oc_categories'])) {
			foreach ($data['oc_categories'] as $key => $oc_categories) {
				if((string)$key === 'exclude' || (string)$key === 'include') {
					foreach ($oc_categories as $oc_category_id) {
						$this->db->query("
							INSERT INTO `" . DB_PREFIX . "ms_coupon_oc_category`
							SET `coupon_id` = " . (int)$coupon_id . ",
								`oc_category_id` = " . (int)$oc_category_id . ",
								`exclude` = " . ((string)$key === 'exclude' ? 1 : 0) . "
						");
					}
				}
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_coupon_ms_category` WHERE `coupon_id` = '" . (int)$coupon_id . "'");
		if(isset($data['ms_categories'])) {
			foreach ($data['ms_categories'] as $key => $ms_categories) {
				if((string)$key === 'exclude' || (string)$key === 'include') {
					foreach ($ms_categories as $ms_category_id) {
						$this->db->query("
							INSERT INTO `" . DB_PREFIX . "ms_coupon_ms_category`
							SET `coupon_id` = " . (int)$coupon_id . ",
								`ms_category_id` = " . (int)$ms_category_id . ",
								`exclude` = " . ((string)$key === 'exclude' ? 1 : 0) . "
						");
					}
				}
			}
		}

		return $coupon_id;
	}

	/**
	 * Deletes coupon.
	 *
	 * @param	int		$coupon_id		Coupon id.
	 */
	public function deleteCoupon($coupon_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_coupon` WHERE `coupon_id` = '" . (int)$coupon_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_coupon_history` WHERE `coupon_id` = '" . (int)$coupon_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_coupon_customer` WHERE `coupon_id` = '" . (int)$coupon_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_coupon_product` WHERE `coupon_id` = '" . (int)$coupon_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_coupon_oc_category` WHERE `coupon_id` = '" . (int)$coupon_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_coupon_ms_category` WHERE `coupon_id` = '" . (int)$coupon_id . "'");
	}


	/************************************************************/


	// Coupon history

	/**
	 * Gets coupon usage history.
	 *
	 * @param	array	$data		Conditions.
	 * @return	array				Coupon history.
	 */
	public function getCouponHistory($data = array()) {
		$result = $this->db->query("
			SELECT
				SQL_CALC_FOUND_ROWS
				*
			FROM `" . DB_PREFIX . "ms_coupon_history`
			WHERE 1 = 1"

			. (isset($data['coupon_id']) ? " AND `coupon_id` = " . (int)$data['coupon_id'] : "")
			. (!empty($data['coupon_ids']) ? " AND `coupon_id` IN (" . implode(',', $data['coupon_ids'])  . ")" : "")
			. (isset($data['customer_id']) ? " AND `customer_id` = " . (int)$data['customer_id'] : "")
			. (isset($data['order_id']) ? " AND `order_id` = " . (int)$data['order_id'] : "")
			. (isset($data['suborder_id']) ? " AND `suborder_id` = " . (int)$data['suborder_id'] : "")
		);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($result->rows) $result->rows[0]['total_rows'] = $total->row['total'];

		return $result->num_rows && isset($data['single']) ? $result->row : $result->rows;
	}

	/**
	 * Creates coupon history entry.
	 *
	 * @param	int		$coupon_id	Coupon id.
	 * @param	array	$data		Conditions.
	 * @return	bool
	 */
	public function createCouponHistory($coupon_id, $data = array()) {
		$this->db->query("
			INSERT INTO `" . DB_PREFIX . "ms_coupon_history`
			SET `coupon_id` = " . (int)$coupon_id . ",
				`order_id` = " . (int)$data['order_id'] . ",
				`suborder_id` = " . (int)$data['suborder_id'] . ",
				`customer_id` = " . (int)$data['customer_id'] . ",
				`amount` = " . (float)$data['amount'] . ",
				`date_created` = NOW()
		");

		return true;
	}

	/**
	 * Deletes coupon history entry.
	 * Required params to be passed in $data:
	 * - either $data['coupon_id']
	 * - either $data['order_id']
	 * - either $data['suborder_id']
	 *
	 * @param	array	$data		Conditions.
	 * @return	bool
	 */
	public function deleteCouponHistory($data = array()) {
		$this->db->query("
			DELETE FROM `" . DB_PREFIX . "ms_coupon_history`
			WHERE 1 = 0"

			. (isset($data['coupon_id']) ? " AND `coupon_id` = " . (int)$data['coupon_id'] : "")
			. (isset($data['order_id']) ? " AND `order_id` = " . (int)$data['order_id'] : "")
			. (isset($data['suborder_id']) ? " AND `suborder_id` = " . (int)$data['suborder_id'] : "")
		);

		return true;
	}


	/************************************************************/


	// Helpers

	/**
	 * Gets customers by passed coupon id.
	 *
	 * @param	int		$coupon_id	Coupon id.
	 * @return	array				Customers.
	 */
	public function getCustomerByCouponId($coupon_id) {
		$result = $this->db->query("
			SELECT
				mscc.customer_id,
				CONCAT_WS(' ', c.firstname, c.lastname) as `customer_name`,
				mscc.exclude
			FROM `" . DB_PREFIX . "ms_coupon_customer` mscc
			LEFT JOIN (SELECT customer_id, firstname, lastname FROM `" . DB_PREFIX . "customer` WHERE `language_id` = " . (int)$this->config->get('config_language_id') . ") c
				ON (c.customer_id = mscc.customer_id)
			WHERE mscc.coupon_id = '" . (int)$coupon_id . "'
		");

		$customers = array();
		if($result->num_rows) {
			foreach ($result->rows as $row) {
				$key = $row['exclude'] == 0 ? 'include' : 'exclude';
				$customers[$key][] = array(
					'customer_id' => $row['customer_id'],
					'name' => $row['customer_name']
				);
			}
		}

		return $customers;
	}

	/**
	 * Gets products by passed coupon id.
	 *
	 * @param	int		$coupon_id	Coupon id.
	 * @return	array				Products.
	 */
	public function getProductByCouponId($coupon_id) {
		$result = $this->db->query("
			SELECT
				mscp.product_id,
				pd.`name` as `product_name`,
				mscp.exclude
			FROM `" . DB_PREFIX . "ms_coupon_product` mscp
			LEFT JOIN (SELECT product_id, `name` FROM `" . DB_PREFIX . "product_description` WHERE `language_id` = '" . (int)$this->config->get('config_language_id') . "') pd
				ON (pd.product_id = mscp.product_id)
			WHERE coupon_id = '" . (int)$coupon_id . "'
		");

		$products = array();
		if($result->num_rows) {
			foreach ($result->rows as $row) {
				$key = $row['exclude'] == 0 ? 'include' : 'exclude';
				$products[$key][] = array(
					'product_id' => $row['product_id'],
					'name' => $row['product_name']
				);
			}
		}

		return $products;
	}

	/**
	 * Gets OpenCart categories by passed coupon id.
	 *
	 * @param	int		$coupon_id	Coupon id.
	 * @return	array				OpenCart categories.
	 */
	public function getOcCategoryByCouponId($coupon_id) {
		$result = $this->db->query("
			SELECT
				mscoc.oc_category_id,
				cd.`name` as `oc_category_name`,
				mscoc.exclude
			FROM `" . DB_PREFIX . "ms_coupon_oc_category` mscoc
			LEFT JOIN (SELECT category_id, `name` FROM `" . DB_PREFIX . "category_description` WHERE `language_id` = " . (int)$this->config->get('config_language_id') . ") cd
				ON (cd.category_id = mscoc.oc_category_id)
			WHERE mscoc.coupon_id = '" . (int)$coupon_id . "'
		");

		$oc_categories = array();
		if($result->num_rows) {
			foreach ($result->rows as $row) {
				$key = $row['exclude'] == 0 ? 'include' : 'exclude';
				$oc_categories[$key][] = array(
					'oc_category_id' => $row['oc_category_id'],
					'name' => $row['oc_category_name']
				);
			}
		}

		return $oc_categories;
	}

	/**
	 * Gets MultiMerch categories by passed coupon id.
	 *
	 * @param	int		$coupon_id	Coupon id.
	 * @return	array				MultiMerch categories.
	 */
	public function getMsCategoryByCouponId($coupon_id) {
		$result = $this->db->query("
			SELECT
				mscmc.ms_category_id,
				mscd.`name` as `ms_category_name`,
				mscmc.exclude
			FROM `" . DB_PREFIX . "ms_coupon_ms_category` mscmc
			LEFT JOIN (SELECT category_id, `name` FROM `" . DB_PREFIX . "ms_category_description` WHERE `language_id` = " . (int)$this->config->get('config_language_id') . ") mscd
				ON (mscd.category_id = mscmc.ms_category_id)
			WHERE mscmc.coupon_id = '" . (int)$coupon_id . "'
		");

		$ms_categories = array();
		if($result->num_rows) {
			foreach ($result->rows as $row) {
				$key = $row['exclude'] == 0 ? 'include' : 'exclude';
				$ms_categories[$key][] = array(
					'ms_category_id' => $row['ms_category_id'],
					'name' => $row['ms_category_name']
				);
			}
		}

		return $ms_categories;
	}

	/**
	 * Checks whether coupon belongs to seller.
	 *
	 * @param	int		$coupon_id		Coupon id.
	 * @param	int		$seller_id		Seller id.
	 * @return	bool					True if belongs, false if not.
	 */
	public function checkCouponBelongsToSeller($coupon_id, $seller_id) {
		$result = $this->db->query("SELECT 1 FROM `" . DB_PREFIX . "ms_coupon` WHERE `coupon_id` = " . (int)$coupon_id . " AND `seller_id` = " . (int)$seller_id);

		return $result->num_rows ?: false;
	}

	/**
	 * Increments value of a total uses field in a `ms_coupon` table.
	 *
	 * @param	int		$coupon_id
	 * @return	bool
	 */
	public function incrementTotalUses($coupon_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "ms_coupon` SET `total_uses` = `total_uses` + 1 WHERE `coupon_id` = " . (int)$coupon_id);

		return true;
	}
}