<?php
class MsReport extends Model {


	/* ===========================================   SALES   ======================================================== */


	/**
	 * Gets data for Sales reports with specified conditions.
	 *
	 * @param	array	$data	Conditions
	 * @param	array	$sort	Sorts
	 * @param	array	$cols	Cols
	 * @return	mixed			Data for Sales reports
	 */
	public function getSalesData($data = array(), $sort = array(), $cols = array()) {
		$this->load->language('multiseller/multiseller');

		$hFilters = $wFilters = '';
		if(isset($sort['filters'])) {
			$cols = array_merge($cols, array("`p.date_added`" => 1));
			foreach($sort['filters'] as $k => $v) {
				if (!isset($cols[$k])) {
					$wFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				} else {
					$hFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				}
			}
		}

		// @todo: think about returns and discounts, net_marketplace
		$result = $this->db->query("
			SELECT
				SQL_CALC_FOUND_ROWS
				o.order_id,
				@gross := TRUNCATE(op.`total`, 2) as `gross`,
				@tax := TRUNCATE(op.`tax`, 2) as `tax`,
				@net_seller := TRUNCATE(COALESCE(
					msopd.seller_net_amt - @tax,
					0
				), 2) as `net_seller`,
				@net_marketplace := ROUND(IF(
					IFNULL(msopd.store_commission_flat, 0) + IFNULL(msopd.store_commission_pct, 0) <> 0,
					IFNULL(msopd.store_commission_flat, 0) + IFNULL(msopd.store_commission_pct, 0),
					@gross - @net_seller
				), 2) as `net_marketplace`,
				@shipping := TRUNCATE(COALESCE(
					msopsd.shipping_cost,
					(SELECT SUM(`value`) / (SELECT COUNT(order_product_id) FROM `" . DB_PREFIX . "order_product` WHERE order_id = o.order_id) FROM `" . DB_PREFIX . "order_total` WHERE order_id = o.order_id AND `code` IN ('shipping', 'mm_shipping_total')),
					0
				), 2) as `shipping`,
				TRUNCATE(COALESCE(
					@gross + @shipping + @tax,
					0
				), 2) as `total`,
				op.`name` as `product_name`,
				op.`name_with_quantity` as `product_name_with_quantity`,
				mss.seller_id,
				COALESCE(
					c.`name`,
					'" . $this->language->get('ms_report_guest_checkout') . "'
				) as `customer_name`,
				mss.nickname as `seller_name`,
				o.date_added
			FROM `" . DB_PREFIX . "order` o
			LEFT JOIN (SELECT order_product_id, order_id, product_id, `name`, CONCAT_WS(' x ', `quantity`, `name`) as `name_with_quantity`, `total`, `tax` FROM `" . DB_PREFIX . "order_product`) op
				ON (o.order_id = op.order_id)
			LEFT JOIN (SELECT product_id, seller_id FROM `" . DB_PREFIX . "ms_product`) msp
				ON (op.product_id = msp.product_id)
			LEFT JOIN (SELECT seller_id, nickname FROM `" . DB_PREFIX . "ms_seller`) mss
				ON (mss.seller_id = msp.seller_id)
			LEFT JOIN (SELECT order_product_id, shipping_cost FROM `" . DB_PREFIX . "ms_order_product_shipping_data`) msopsd
				ON (op.order_product_id = msopsd.order_product_id)
			LEFT JOIN (SELECT order_product_id, seller_id, store_commission_flat, store_commission_pct, seller_net_amt FROM `" . DB_PREFIX . "ms_order_product_data`) msopd
				ON (op.order_product_id = msopd.order_product_id)
			LEFT JOIN (SELECT customer_id, CONCAT_WS(' ', `firstname`, `lastname`) as `name` FROM `" . DB_PREFIX . "customer`) c
				ON (c.customer_id = o.customer_id)
			WHERE o.order_status_id <> 0"
			. (isset($data['seller_id']) ? " AND msopd.seller_id = " . (int)$data['seller_id'] : "")
			. (isset($data['date_start']) ? " AND DATE(o.`date_added`) >= '" . $this->db->escape(date('Y-m-d', $data['date_start'])) . "'" : "")
			. (isset($data['date_end']) ? " AND DATE(o.`date_added`) <= '" . $this->db->escape(date('Y-m-d', $data['date_end'])) . "'" : "")

			. $wFilters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '')
		);

		$total = $this->db->query("SELECT FOUND_ROWS() as total_rows");
		if ($result->rows) {
			$result->rows[0]['total_rows'] = $total->row['total_rows'];
		}

		return $result->rows;
	}

	/**
	 * Gets data for Sales reports by day/month.
	 *
	 * @param	array	$data	Conditions
	 * @param	array	$sort	Sorts
	 * @param	array	$cols	Cols
	 * @return	mixed			Data for Sales reports
	 */
	public function getSalesByPeriodData($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';
		if(isset($sort['filters'])) {
			$cols = array_merge($cols, array("`p.date_added`" => 1));
			foreach($sort['filters'] as $k => $v) {
				if (!isset($cols[$k])) {
					$wFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				} else {
					$hFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				}
			}
		}

		$result = $this->db->query("
			SELECT
				SQL_CALC_FOUND_ROWS "
				. (isset($data['period_month']) ? "DATE_FORMAT(o.`date_added`,'%M %Y')" : "DATE(o.`date_added`)") . " as `date_added`,
				COUNT(op.order_product_id) as `total_sales`,
				SUM(op.`total`) as `gross`,
				SUM(op.`tax`) as `tax`,
				SUM(op.`total` - IF(
					msopd.seller_net_amt IS NOT NULL,
					msopd.seller_net_amt - op.`tax`,
					0
				)) as `net_marketplace`,
				SUM(IF(
					msopd.seller_net_amt IS NOT NULL,
					msopd.seller_net_amt - op.`tax`,
					0
				)) as `net_seller`,
				ROUND(SUM(COALESCE(
					msopsd.shipping_cost,
					ot_shipping.`value` / (SELECT COUNT(order_product_id) FROM `" . DB_PREFIX . "order_product` WHERE order_id = op.order_id),
					0
				)), 2) as `shipping`,
				ROUND(SUM(
					op.`total` + op.`tax` + COALESCE(
						msopsd.shipping_cost,
						ot_shipping.`value` / (SELECT COUNT(order_product_id) FROM `" . DB_PREFIX . "order_product` WHERE order_id = op.order_id),
						0
					)
				), 2) as `total`
			FROM `" . DB_PREFIX . "order_product` op
			LEFT JOIN (SELECT order_id, order_status_id, date_added FROM `" . DB_PREFIX . "order`) o
				ON (o.order_id = op.order_id)
			LEFT JOIN (SELECT order_product_id, seller_id, store_commission_flat, store_commission_pct, seller_net_amt FROM `" . DB_PREFIX . "ms_order_product_data`) msopd
				ON (msopd.order_product_id = op.order_product_id)
			LEFT JOIN (SELECT order_product_id, shipping_cost FROM `" . DB_PREFIX . "ms_order_product_shipping_data`) msopsd
				ON (msopsd.order_product_id = op.order_product_id)
			LEFT JOIN (SELECT order_id, `value` FROM `" . DB_PREFIX . "order_total` WHERE `code` IN ('shipping', 'mm_shipping_total')) ot_shipping
				ON (ot_shipping.order_id = op.order_id)
			WHERE o.order_status_id <> 0"
			. (isset($data['seller_id']) ? " AND msopd.seller_id = " . (int)$data['seller_id'] : "")
			. (isset($data['date_start']) ? " AND DATE(o.`date_added`) >= '" . $this->db->escape(date('Y-m-d', $data['date_start'])) . "'" : "")
			. (isset($data['date_end']) ? " AND DATE(o.`date_added`) <= '" . $this->db->escape(date('Y-m-d', $data['date_end'])) . "'" : "")

			. $wFilters

			. " GROUP BY " . (isset($data['period_month']) ? "EXTRACT(YEAR_MONTH FROM o.`date_added`)" : "DATE(o.`date_added`)")

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '')
		);

		$total = $this->db->query("SELECT FOUND_ROWS() as total_rows");
		if ($result->rows) {
			$result->rows[0]['total_rows'] = $total->row['total_rows'];
		}

		return $result->rows;
	}

	/**
	 * Gets data for Sales reports by product.
	 *
	 * @param	array	$data	Conditions
	 * @param	array	$sort	Sorts
	 * @param	array	$cols	Cols
	 * @return	mixed			Data for Sales reports
	 */
	public function getSalesByProductData($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';
		if(isset($sort['filters'])) {
			$cols = array_merge($cols, array("`p.date_added`" => 1));
			foreach($sort['filters'] as $k => $v) {
				if (!isset($cols[$k])) {
					$wFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				} else {
					$hFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				}
			}
		}

		$result = $this->db->query("
			SELECT
				SQL_CALC_FOUND_ROWS
				op.`name` as `product_name`,
				mss.`nickname` as `seller_name`,
				COUNT(op.order_product_id) as `total_sales`,
				SUM(op.`total`) as `gross`,
				SUM(op.`tax`) as `tax`,
				SUM(op.`total` - IF(
					msopd.seller_net_amt IS NOT NULL,
					msopd.seller_net_amt - op.`tax`,
					0
				)) as `net_marketplace`,
				SUM(IF(
					msopd.seller_net_amt IS NOT NULL,
					msopd.seller_net_amt - op.`tax`,
					0
				)) as `net_seller`,
				ROUND(SUM(COALESCE(
					msopsd.shipping_cost,
					ot_shipping.`value` / (SELECT COUNT(order_product_id) FROM `" . DB_PREFIX . "order_product` WHERE order_id = op.order_id),
					0
				)), 2) as `shipping`,
				ROUND(SUM(
					op.`total` + op.`tax` + COALESCE(
						msopsd.shipping_cost,
						ot_shipping.`value` / (SELECT COUNT(order_product_id) FROM `" . DB_PREFIX . "order_product` WHERE order_id = op.order_id),
						0
					)
				), 2) as `total`
			FROM `" . DB_PREFIX . "order_product` op
			LEFT JOIN (SELECT product_id, seller_id FROM `" . DB_PREFIX . "ms_product`) msp
				ON (op.product_id = msp.product_id)
			LEFT JOIN (SELECT seller_id, nickname FROM `" . DB_PREFIX . "ms_seller`) mss
				ON (mss.seller_id = msp.seller_id)
			LEFT JOIN (SELECT order_id, order_status_id, date_added FROM `" . DB_PREFIX . "order`) o
				ON (o.order_id = op.order_id)
			LEFT JOIN (SELECT order_product_id, seller_id, store_commission_flat, store_commission_pct, seller_net_amt FROM `" . DB_PREFIX . "ms_order_product_data`) msopd
				ON (msopd.order_product_id = op.order_product_id)
			LEFT JOIN (SELECT order_product_id, shipping_cost FROM `" . DB_PREFIX . "ms_order_product_shipping_data`) msopsd
				ON (msopsd.order_product_id = op.order_product_id)
			LEFT JOIN (SELECT order_id, `value` FROM `" . DB_PREFIX . "order_total` WHERE `code` IN ('shipping', 'mm_shipping_total')) ot_shipping
				ON (ot_shipping.order_id = op.order_id)
			WHERE o.order_status_id <> 0"
			. (isset($data['seller_id']) ? " AND msopd.seller_id = " . (int)$data['seller_id'] : "")
			. (isset($data['date_start']) ? " AND DATE(o.`date_added`) >= '" . $this->db->escape(date('Y-m-d', $data['date_start'])) . "'" : "")
			. (isset($data['date_end']) ? " AND DATE(o.`date_added`) <= '" . $this->db->escape(date('Y-m-d', $data['date_end'])) . "'" : "")

			. $wFilters

			. " GROUP BY op.product_id"

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '')
		);

		$total = $this->db->query("SELECT FOUND_ROWS() as total_rows");
		if ($result->rows) {
			$result->rows[0]['total_rows'] = $total->row['total_rows'];
		}

		return $result->rows;
	}

	/**
	 * Gets data for Sales reports by seller.
	 *
	 * @param	array	$data	Conditions
	 * @param	array	$sort	Sorts
	 * @param	array	$cols	Cols
	 * @return	mixed			Data for Sales reports
	 */
	public function getSalesBySellerData($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';
		if(isset($sort['filters'])) {
			$cols = array_merge($cols, array("`p.date_added`" => 1));
			foreach($sort['filters'] as $k => $v) {
				if (!isset($cols[$k])) {
					$wFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				} else {
					$hFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				}
			}
		}

		// total = net_marketplace + net_seller
		$result = $this->db->query("
			SELECT
				SQL_CALC_FOUND_ROWS
				mss.`nickname` as `seller_name`,
				COUNT(mso.order_id) as `total_sales`,
				TRUNCATE(
					(SELECT SUM(`total`) FROM `" . DB_PREFIX . "order_product` WHERE FIND_IN_SET (order_id, GROUP_CONCAT(mso.order_id SEPARATOR ',')) AND FIND_IN_SET(product_id, (SELECT GROUP_CONCAT(product_id SEPARATOR ',') FROM `" . DB_PREFIX . "ms_product` WHERE seller_id = mss.seller_id)))
				, 2)  as `gross`,
				TRUNCATE(
					(SELECT SUM(IFNULL(store_commission_flat, 0) + IFNULL(store_commission_pct, 0)) FROM `" . DB_PREFIX . "ms_order_product_data` WHERE FIND_IN_SET (order_id, GROUP_CONCAT(mso.order_id SEPARATOR ',')) AND FIND_IN_SET(product_id, (SELECT GROUP_CONCAT(product_id SEPARATOR ',') FROM `" . DB_PREFIX . "ms_product` WHERE seller_id = mss.seller_id)))
				, 2) as `net_marketplace`,
				TRUNCATE(
					(SELECT SUM(IFNULL(seller_net_amt, 0)) FROM `" . DB_PREFIX . "ms_order_product_data` WHERE FIND_IN_SET (order_id, GROUP_CONCAT(mso.order_id SEPARATOR ',')) AND FIND_IN_SET(product_id, (SELECT GROUP_CONCAT(product_id SEPARATOR ',') FROM `" . DB_PREFIX . "ms_product` WHERE seller_id = mss.seller_id)))
				, 2) as `net_seller`,
				TRUNCATE(
					(SELECT SUM(IFNULL(store_commission_flat, 0) + IFNULL(store_commission_pct, 0)) FROM `" . DB_PREFIX . "ms_order_product_data` WHERE FIND_IN_SET (order_id, GROUP_CONCAT(mso.order_id SEPARATOR ',')) AND FIND_IN_SET(product_id, (SELECT GROUP_CONCAT(product_id SEPARATOR ',') FROM `" . DB_PREFIX . "ms_product` WHERE seller_id = mss.seller_id)))
					+
					(SELECT SUM(IFNULL(seller_net_amt, 0)) FROM `" . DB_PREFIX . "ms_order_product_data` WHERE FIND_IN_SET (order_id, GROUP_CONCAT(mso.order_id SEPARATOR ',')) AND FIND_IN_SET(product_id, (SELECT GROUP_CONCAT(product_id SEPARATOR ',') FROM `" . DB_PREFIX . "ms_product` WHERE seller_id = mss.seller_id)))
				, 2) as `total`
			FROM `" . DB_PREFIX . "ms_seller` mss
			LEFT JOIN (SELECT seller_id, order_id FROM `" . DB_PREFIX . "ms_suborder`) mso
				ON (mso.seller_id = mss.seller_id)
			LEFT JOIN (SELECT order_id, date_added, order_status_id FROM `" . DB_PREFIX . "order`) o
				ON (o.order_id = mso.order_id)
			WHERE o.order_status_id <> 0"
			. (isset($data['date_start']) ? " AND DATE(o.`date_added`) >= '" . $this->db->escape(date('Y-m-d', $data['date_start'])) . "'" : "")
			. (isset($data['date_end']) ? " AND DATE(o.`date_added`) <= '" . $this->db->escape(date('Y-m-d', $data['date_end'])) . "'" : "")

			. $wFilters

			. " GROUP BY mss.seller_id"

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '')
		);

		$total = $this->db->query("SELECT FOUND_ROWS() as total_rows");
		if ($result->rows) {
			$result->rows[0]['total_rows'] = $total->row['total_rows'];
		}

		return $result->rows;
	}

	/**
	 * Gets data for Sales reports by customer.
	 *
	 * @param	array	$data	Conditions
	 * @param	array	$sort	Sorts
	 * @param	array	$cols	Cols
	 * @return	mixed			Data for Sales reports
	 */
	public function getSalesByCustomerData($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';
		if(isset($sort['filters'])) {
			$cols = array_merge($cols, array("`p.date_added`" => 1));
			foreach($sort['filters'] as $k => $v) {
				if (!isset($cols[$k])) {
					$wFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				} else {
					$hFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				}
			}
		}

		// net_market = gross - net_seller
		$result = $this->db->query("
			SELECT
				SQL_CALC_FOUND_ROWS
				CONCAT_WS(' ', c.`firstname`, c.`lastname`) as `customer_name`,
				c.`email` as `email`,
				COUNT(o.order_id) as `total_orders`,
				TRUNCATE(COALESCE(
					(SELECT SUM(`total`) FROM `" . DB_PREFIX . "order_product` WHERE FIND_IN_SET (order_id, GROUP_CONCAT(o.order_id SEPARATOR ','))),
					0
				), 2) as `gross`,
				TRUNCATE(
					COALESCE(
						(SELECT SUM(`total`) FROM `" . DB_PREFIX . "order_product` WHERE FIND_IN_SET (order_id, GROUP_CONCAT(o.order_id SEPARATOR ','))),
						0
					)
					-
					COALESCE(
						(SELECT SUM(`seller_net_amt`) FROM `" . DB_PREFIX . "ms_order_product_data` WHERE FIND_IN_SET (order_id, GROUP_CONCAT(o.order_id SEPARATOR ','))),
						0
					)
				, 2) as `net_marketplace`,
				TRUNCATE(COALESCE(
					(SELECT SUM(`seller_net_amt`) FROM `" . DB_PREFIX . "ms_order_product_data` WHERE FIND_IN_SET (order_id, GROUP_CONCAT(o.order_id SEPARATOR ','))),
					0
				), 2) as `net_seller`,
				TRUNCATE(COALESCE(
					(SELECT SUM(`total`) FROM `" . DB_PREFIX . "order_product` WHERE FIND_IN_SET (order_id, GROUP_CONCAT(o.order_id SEPARATOR ','))),
					0
				), 2) as `total`
			FROM `" . DB_PREFIX . "customer` c
			LEFT JOIN (SELECT order_id, customer_id, order_status_id, date_added FROM `" . DB_PREFIX . "order`) o
				ON (o.customer_id = c.customer_id)
			WHERE o.order_status_id <> 0"
			. (isset($data['date_start']) ? " AND DATE(o.`date_added`) >= '" . $this->db->escape(date('Y-m-d', $data['date_start'])) . "'" : "")
			. (isset($data['date_end']) ? " AND DATE(o.`date_added`) <= '" . $this->db->escape(date('Y-m-d', $data['date_end'])) . "'" : "")

			. $wFilters

			. " GROUP BY c.customer_id"

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '')
		);

		$total = $this->db->query("SELECT FOUND_ROWS() as total_rows");
		if ($result->rows) {
			$result->rows[0]['total_rows'] = $total->row['total_rows'];
		}

		return $result->rows;
	}


	/* ==========================================   FINANCES   ====================================================== */


	/**
	 * Gets data for Transactions reports. Basically list of transactions.
	 *
	 * @param	array	$data	Conditions
	 * @param	array	$sort	Sorts
	 * @param	array	$cols	Cols
	 * @return	mixed			Data for Transactions reports
	 */
	public function getTransactionsData($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';
		if(isset($sort['filters'])) {
			$cols = array_merge($cols, array("`p.date_added`" => 1));
			foreach($sort['filters'] as $k => $v) {
				if (!isset($cols[$k])) {
					$wFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				} else {
					$hFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				}
			}
		}

		$result = $this->db->query("
			SELECT
				SQL_CALC_FOUND_ROWS
				msb.balance_id,
				mss.nickname as `seller_name`,
				msb.`description` as `description`,
				msb.amount as `gross`,
				msb.date_created as `date_created`
			FROM `" . DB_PREFIX . "ms_balance` msb
			LEFT JOIN (SELECT seller_id, nickname FROM `" . DB_PREFIX . "ms_seller`) mss
				ON (mss.seller_id = msb.seller_id)
			WHERE 1 = 1"
			. (isset($data['seller_id']) ? " AND msb.seller_id = " . (int)$data['seller_id'] : "")
			. (isset($data['date_start']) ? " AND DATE(msb.`date_created`) >= '" . $this->db->escape(date('Y-m-d', $data['date_start'])) . "'" : "")
			. (isset($data['date_end']) ? " AND DATE(msb.`date_created`) <= '" . $this->db->escape(date('Y-m-d', $data['date_end'])) . "'" : "")

			. $wFilters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '')
		);

		$total = $this->db->query("SELECT FOUND_ROWS() as total_rows");
		if ($result->rows) {
			$result->rows[0]['total_rows'] = $total->row['total_rows'];
		}

		return $result->rows;
	}

	/**
	 * Gets data for Seller financial report.
	 *
	 * @param	array	$data	Conditions
	 * @param	array	$sort	Sorts
	 * @param	array	$cols	Cols
	 * @return	mixed			Data for Seller financial reports
	 */
	public function getSellerFinancesData($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';
		if(isset($sort['filters'])) {
			$cols = array_merge($cols, array("`p.date_added`" => 1));
			foreach($sort['filters'] as $k => $v) {
				if (!isset($cols[$k])) {
					$wFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				} else {
					$hFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				}
			}
		}

		$result = $this->db->query("
			SELECT
				SQL_CALC_FOUND_ROWS
				mss.nickname as `seller_name`,
				COALESCE(
					(SELECT SUM(amount) FROM `" . DB_PREFIX . "ms_balance` WHERE seller_id = mss.seller_id AND amount > 0"
						. (isset($data['date_start']) && isset($data['date_end']) ? " AND date_created BETWEEN '" . $this->db->escape(date('Y-m-d', $data['date_start'])). "' AND '" . $this->db->escape(date('Y-m-d', $data['date_end'])). "'" : "")
					."),
					0
				) as `balance_in`,
				COALESCE(
					(SELECT SUM(amount) FROM `" . DB_PREFIX . "ms_balance` WHERE seller_id = mss.seller_id AND amount < 0"
						. (isset($data['date_start']) && isset($data['date_end']) ? " AND date_created BETWEEN '" . $this->db->escape(date('Y-m-d', $data['date_start'])). "' AND '" . $this->db->escape(date('Y-m-d', $data['date_end'])). "'" : "")
					."),
					0
				) as `balance_out`,
				COALESCE(
					(SELECT SUM(store_commission_flat) + SUM(store_commission_pct)
						FROM `" . DB_PREFIX . "ms_order_product_data` msopd
						LEFT JOIN (SELECT order_id, date_added FROM `" . DB_PREFIX . "order`) o
							ON (msopd.order_id = o.order_id)
						WHERE msopd.seller_id = mss.seller_id"
							. (isset($data['date_start']) && isset($data['date_end']) ? " AND o.date_added BETWEEN '" . $this->db->escape(date('Y-m-d', $data['date_start'])). "' AND '" . $this->db->escape(date('Y-m-d', $data['date_end'])). "'" : "")
					."),
					0
				) as `marketplace_earnings`,
				COALESCE(
					(SELECT SUM(seller_net_amt)
						FROM `" . DB_PREFIX . "ms_order_product_data` msopd
						LEFT JOIN (SELECT order_id, date_added FROM `" . DB_PREFIX . "order`) o
							ON (msopd.order_id = o.order_id)
						WHERE msopd.seller_id = mss.seller_id"
							. (isset($data['date_start']) && isset($data['date_end']) ? " AND o.date_added BETWEEN '" . $this->db->escape(date('Y-m-d', $data['date_start'])). "' AND '" . $this->db->escape(date('Y-m-d', $data['date_end'])). "'" : "")
					."),
					0
				) as `seller_earnings`,
				COALESCE(
					(SELECT SUM(mspgr.amount)
						FROM `" . DB_PREFIX . "ms_pg_request` mspgr
						WHERE mspgr.seller_id = mss.seller_id
							AND request_type <> '" . (int)MsPgRequest::TYPE_PAYOUT . "'
							AND request_status = '" . (int)MsPgRequest::STATUS_PAID . "'"
							. (isset($data['date_start']) && isset($data['date_end']) ? " AND mspgr.date_created BETWEEN '" . $this->db->escape(date('Y-m-d', $data['date_start'])). "' AND '" . $this->db->escape(date('Y-m-d', $data['date_end'])). "'" : "")
					."),
					0
				) as `payments_received`,
				COALESCE(
					(SELECT SUM(mspgr.amount)
						FROM `" . DB_PREFIX . "ms_pg_request` mspgr
						WHERE mspgr.seller_id = mss.seller_id
							AND request_type = '" . (int)MsPgRequest::TYPE_PAYOUT . "'
							AND request_status = '" . (int)MsPgRequest::STATUS_PAID . "'"
							. (isset($data['date_start']) && isset($data['date_end']) ? " AND mspgr.date_created BETWEEN '" . $this->db->escape(date('Y-m-d', $data['date_start'])). "' AND '" . $this->db->escape(date('Y-m-d', $data['date_end'])). "'" : "")
					."),
					0
				) as `payouts_paid`,
				COALESCE(
					(SELECT balance FROM `" . DB_PREFIX . "ms_balance`
						WHERE seller_id = mss.seller_id
						ORDER BY balance_id DESC
						LIMIT 1
					),
					0
				) as `current_balance`
			FROM `" . DB_PREFIX . "ms_seller` mss
			WHERE 1 = 1"

			. $wFilters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '')
		);

		$total = $this->db->query("SELECT FOUND_ROWS() as total_rows");
		if ($result->rows) {
			$result->rows[0]['total_rows'] = $total->row['total_rows'];
		}

		return $result->rows;
	}

	/**
	 * Gets data for Payouts report.
	 *
	 * @param	array	$data	Conditions
	 * @param	array	$sort	Sorts
	 * @param	array	$cols	Cols
	 * @return	mixed			Data for Payouts reports
	 */
	public function getPayoutsData($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';
		if(isset($sort['filters'])) {
			$cols = array_merge($cols, array("`p.date_added`" => 1));
			foreach($sort['filters'] as $k => $v) {
				if (!isset($cols[$k])) {
					$wFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				} else {
					$hFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				}
			}
		}

		$result = $this->db->query("
			SELECT
				SQL_CALC_FOUND_ROWS
				mspgr.request_id,
				mss.nickname as `seller_name`,
				mspgr.amount as `gross`,
				mspgp.payment_code,
				mspgr.description as `description`,
				mspgr.date_created as `date_created`
			FROM `" . DB_PREFIX . "ms_pg_request` mspgr
			LEFT JOIN (SELECT seller_id, nickname FROM `" . DB_PREFIX . "ms_seller`) mss
				ON (mss.seller_id = mspgr.seller_id)
			LEFT JOIN (SELECT payment_id, payment_code FROM `" . DB_PREFIX . "ms_pg_payment`) mspgp
				ON (mspgr.payment_id = mspgp.payment_id)
			WHERE request_type = '" . (int)MsPgRequest::TYPE_PAYOUT . "'
				AND request_status = '" . (int)MsPgRequest::STATUS_PAID . "'"
				. (isset($data['seller_id']) ? " AND mspgr.seller_id = " . (int)$data['seller_id'] : "")
				. (isset($data['date_start']) ? " AND DATE(mspgr.`date_created`) >= '" . $this->db->escape(date('Y-m-d', $data['date_start'])) . "'" : "")
				. (isset($data['date_end']) ? " AND DATE(mspgr.`date_created`) <= '" . $this->db->escape(date('Y-m-d', $data['date_end'])) . "'" : "")

				. $wFilters

				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
				. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '')
		);

		$total = $this->db->query("SELECT FOUND_ROWS() as total_rows");
		if ($result->rows) {
			$result->rows[0]['total_rows'] = $total->row['total_rows'];

			foreach ($result->rows as &$row) {
				$this->load->language('multimerch/payment/' . str_replace(MsPgPayment::ADMIN_SETTING_PREFIX, '', $row['payment_code']));
				$row['method'] = $this->language->get('text_method_name') !== 'text_method_name' ? $this->language->get('text_method_name') : $this->language->get('text_title');
			}
		}

		return $result->rows;
	}

	/**
	 * Gets data for Payments report. List of paid sign-up fees, listing fees, Paypal Adaptive payments.
	 *
	 * @param	array	$data	Conditions
	 * @param	array	$sort	Sorts
	 * @param	array	$cols	Cols
	 * @return	mixed			Data for Payments reports
	 */
	public function getPaymentsData($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';
		if(isset($sort['filters'])) {
			$cols = array_merge($cols, array("`p.date_added`" => 1));
			foreach($sort['filters'] as $k => $v) {
				if (!isset($cols[$k])) {
					$wFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				} else {
					$hFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				}
			}
		}

		$result = $this->db->query("
			SELECT
				SQL_CALC_FOUND_ROWS
				mspgp.payment_id,
				COALESCE(mss.`nickname`, 'Admin') as `seller_name`,
				mspgp.`amount` as `gross`,
				mspgp.`payment_code`,
				mspgr.`description` as `description`,
				mspgp.`date_created` as `date_created`
			FROM `" . DB_PREFIX . "ms_pg_payment` mspgp
			LEFT JOIN (SELECT seller_id, nickname FROM `" . DB_PREFIX . "ms_seller`) mss
				ON (mss.seller_id = mspgp.seller_id)
			LEFT JOIN (SELECT payment_id, description, request_type, request_status FROM `" . DB_PREFIX . "ms_pg_request`) mspgr
				ON (mspgp.payment_id = mspgr.payment_id)
			WHERE mspgr.request_type <> '" . (int)MsPgRequest::TYPE_PAYOUT . "'
				AND mspgp.payment_status = '" . (int)MsPgRequest::STATUS_PAID . "'"
			. (isset($data['seller_id']) ? " AND mspgp.seller_id = " . (int)$data['seller_id'] : "")
			. (isset($data['date_start']) ? " AND DATE(mspgp.`date_created`) >= '" . $this->db->escape(date('Y-m-d', $data['date_start'])) . "'" : "")
			. (isset($data['date_end']) ? " AND DATE(mspgp.`date_created`) <= '" . $this->db->escape(date('Y-m-d', $data['date_end'])) . "'" : "")

			. $wFilters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '')
		);

		$total = $this->db->query("SELECT FOUND_ROWS() as total_rows");
		if ($result->rows) {
			$result->rows[0]['total_rows'] = $total->row['total_rows'];

			foreach ($result->rows as &$row) {
				$this->load->language('multimerch/payment/' . str_replace(MsPgPayment::ADMIN_SETTING_PREFIX, '', $row['payment_code']));
				$row['method'] = $this->language->get('text_method_name') !== 'text_method_name' ? $this->language->get('text_method_name') : $this->language->get('text_title');
			}
		}

		return $result->rows;
	}


	/* ============================================   DASHBOARDS   ================================================== */


	/**
	 * Gets sum of all orders' gross total over a time period.
	 *
	 * @param	array	$data	Conditions.
	 * @return	float			Gross total.
	 */
	public function getSalesCount($data = array()) {
		$date_start = $this->_calculateDateStart($data);
		$date_end = $this->_calculateDateEnd($data);

		$result = $this->db->query("
			SELECT
				COALESCE(SUM(ot.`value`), 0) as `gross_total`
			FROM `" . DB_PREFIX . "order` o
			LEFT JOIN (SELECT order_id, `value` FROM `" . DB_PREFIX . "order_total` WHERE `code` = 'sub_total') ot
				ON (ot.order_id = o.order_id)
			WHERE o.`order_status_id` <> 0
				AND DATE(o.`date_added`) >= DATE('" . $this->db->escape($date_start) . "')
				AND DATE(o.`date_added`) <= DATE('" . $this->db->escape($date_end) . "')
		");

		return $result->row['gross_total'];
	}

	/**
	 * Gets number of orders created over a time period.
	 *
	 * @param	array	$data	Conditions.
	 * @return	int				Total orders count.
	 */
	public function getOrdersCount($data = array()) {
		$date_start = $this->_calculateDateStart($data);
		$date_end = $this->_calculateDateEnd($data);

		$result = $this->db->query("
			SELECT
				COUNT(1) as `total`
			FROM `" . DB_PREFIX . "order` o
			WHERE o.`order_status_id` <> 0
				AND DATE(o.`date_added`) >= DATE('" . $this->db->escape($date_start) . "')
				AND DATE(o.`date_added`) <= DATE('" . $this->db->escape($date_end) . "')"

			. (isset($data['seller_id']) ? " AND FIND_IN_SET(o.order_id, (SELECT GROUP_CONCAT(order_id SEPARATOR ',') FROM `" . DB_PREFIX . "ms_suborder` WHERE seller_id = " . (int)$data['seller_id'] . "))" : "")
		);

		return $result->row['total'];
	}

	/**
	 * Gets number of customers registered over a time period.
	 *
	 * @param	array	$data	Conditions.
	 * @return	int				Total customers count.
	 */
	public function getCustomersCount($data = array()) {
		$date_start = $this->_calculateDateStart($data);
		$date_end = $this->_calculateDateEnd($data);

		$result = $this->db->query("
			SELECT
				COUNT(1) as `total`
			FROM `" . DB_PREFIX . "customer`
			WHERE DATE(`date_added`) >= DATE('" . $this->db->escape($date_start) . "')
				AND DATE(`date_added`) <= DATE('" . $this->db->escape($date_end) . "')
		");

		return $result->row['total'];
	}

	/**
	 * Gets number of currently online customers.
	 *
	 * @return	int				Online customers count.
	 */
	public function getCustomersOnlineCount() {
		$result = $this->db->query("
			SELECT
				COUNT(1) as `total`
			FROM `" . DB_PREFIX . "customer_online`
		");

		return $result->row['total'];
	}

	/**
	 * Gets number of sellers registered over a time period.
	 *
	 * @param	array	$data	Conditions.
	 * @return	int				Total sellers count.
	 */
	public function getSellersCount($data = array()) {
		$date_start = $this->_calculateDateStart($data);
		$date_end = $this->_calculateDateEnd($data);

		$result = $this->db->query("
			SELECT
				COUNT(1) as `total`
			FROM `" . DB_PREFIX . "ms_seller`
			WHERE DATE(`date_created`) >= DATE('" . $this->db->escape($date_start) . "')
				AND DATE(`date_created`) <= DATE('" . $this->db->escape($date_end) . "')
		");

		return $result->row['total'];
	}

	/**
	 * Gets sum of active sellers' balances over a time period.
	 *
	 * @param	array	$data	Conditions.
	 * @return	float			Sum of active sellers' balances.
	 */
	public function getSellersBalanceCount($data = array()) {
		$date_start = $this->_calculateDateStart($data);
		$date_end = $this->_calculateDateEnd($data);

		$result = $this->db->query("
			SELECT COALESCE(
				(SELECT SUM(balance) FROM " . DB_PREFIX . "ms_balance
					WHERE balance_id IN (
							SELECT MAX(balance_id) FROM " . DB_PREFIX . "ms_balance
							LEFT JOIN " . DB_PREFIX . "ms_seller
							USING(seller_id)
							WHERE seller_status = " . (int)MsSeller::STATUS_ACTIVE . "
							GROUP BY seller_id
						)
						AND DATE(`date_created`) >= DATE('" . $this->db->escape($date_start) . "')
						AND DATE(`date_created`) <= DATE('" . $this->db->escape($date_end) . "')
				),
				0
			) as `total`
		");

		return $result->row['total'];
	}

	/**
	 * Gets number of products listed over a time period.
	 *
	 * @param	array	$data	Conditions.
	 * @return	int				Total products count.
	 */
	public function getProductsCount($data = array()) {
		$date_start = $this->_calculateDateStart($data);
		$date_end = $this->_calculateDateEnd($data);

		$result = $this->db->query("
			SELECT
				COUNT(1) as `total`
			FROM `" . DB_PREFIX . "product` p
			WHERE DATE(`date_added`) >= DATE('" . $this->db->escape($date_start) . "')
				AND DATE(`date_added`) <= DATE('" . $this->db->escape($date_end) . "')
		");

		return $result->row['total'];
	}

	/**
	 * Gets total products views over a time period.
	 *
	 * @param	array	$data	Conditions.
	 * @return	int				Total products views.
	 */
	public function getProductsViewCount($data = array()) {
		$date_start = $this->_calculateDateStart($data);
		$date_end = $this->_calculateDateEnd($data);

		$result = $this->db->query("
			SELECT
				COALESCE(SUM(p.`viewed`), 0) as `total`
			FROM `" . DB_PREFIX . "product` p
			WHERE DATE(p.`date_added`) >= DATE('" . $this->db->escape($date_start) . "')
				AND DATE(p.`date_added`) <= DATE('" . $this->db->escape($date_end) . "')"

			. (isset($data['seller_id']) ? " AND FIND_IN_SET(p.product_id, (SELECT GROUP_CONCAT(product_id SEPARATOR ',') FROM `" . DB_PREFIX . "ms_product` WHERE seller_id = " . (int)$data['seller_id'] . "))" : "")
		);

		return $result->row['total'];
	}

	/**
	 * Gets total orders count and gross total for each metric unit (week, month etc.) over a time period.
	 *
	 * @param	array	$data	Conditions. Possible keys: 'date_start', 'date_end', 'metric', 'seller_id'.
	 * @return	array			Sales information. Each element of array contains information (total orders, gross total)
	 * 							depending on passed in $data metric ('month', by default).
	 */
	public function getSalesAnalytics($data = array()) {
		$sales_analytics = array();

		$date_start = $this->_calculateDateStart($data);
		$date_end = $this->_calculateDateEnd($data);

		// Get metric
		$metric = !empty($data['metric']) ? $data['metric'] : 'month';

		switch ($metric) {
			case 'month':
				// Cache common results that won't change in future
				$sales_analytics_by_month_history = $this->cache->get('ms_admin_chart_sales_analytics_by_month_history' . (isset($data['seller_id']) ? '_' . (int)$data['seller_id'] : ''));

				if (!$sales_analytics_by_month_history) {
					if (isset($data['seller_id'])) {
						// We don't need to get main order status because transaction will be created only on order's complete state
						$result = $this->db->query("
							SELECT
								EXTRACT(YEAR_MONTH FROM msb.`date_created`) as `date`,
								COUNT(msb.`balance_id`) as `total`,
								SUM(msb.`amount`) as `gross_total`
							FROM `" . DB_PREFIX . "ms_balance` msb
							WHERE EXTRACT(YEAR_MONTH FROM msb.`date_created`) >= EXTRACT(YEAR_MONTH FROM DATE('" . $this->db->escape($date_start) . "'))
								AND EXTRACT(YEAR_MONTH FROM msb.`date_created`) < EXTRACT(YEAR_MONTH FROM DATE('" . $this->db->escape($date_end) . "'))
								AND msb.balance_type = " . (int)MsBalance::MS_BALANCE_TYPE_SALE . "
								AND msb.seller_id = " . (int)$data['seller_id'] . "
							GROUP BY EXTRACT(YEAR_MONTH FROM msb.`date_created`)
						");
					} else {
						$result = $this->db->query("
							SELECT
								EXTRACT(YEAR_MONTH FROM o.`date_added`) as `date`,
								COUNT(o.order_id) as `total`,
								SUM(ot.`value`) as `gross_total`
							FROM `" . DB_PREFIX . "order` o
							LEFT JOIN (SELECT order_id, `value` FROM `" . DB_PREFIX . "order_total` WHERE `code` = 'sub_total') ot
								ON (ot.order_id = o.order_id)
							WHERE o.order_status_id <> 0
								AND EXTRACT(YEAR_MONTH FROM o.`date_added`) >= EXTRACT(YEAR_MONTH FROM DATE('" . $this->db->escape($date_start) . "'))
								AND EXTRACT(YEAR_MONTH FROM o.`date_added`) < EXTRACT(YEAR_MONTH FROM DATE('" . $this->db->escape($date_end) . "'))
							GROUP BY EXTRACT(YEAR_MONTH FROM o.`date_added`)
						");
					}

					$sales_analytics_by_month_history = $result->rows;
					$this->cache->set('ms_admin_chart_sales_analytics_by_month_history' . (isset($data['seller_id']) ? '_' . (int)$data['seller_id'] : ''), $sales_analytics_by_month_history);
				}

				// Get result for current month
				if (isset($data['seller_id'])) {
					$result = $this->db->query("
						SELECT
							EXTRACT(YEAR_MONTH FROM msb.`date_created`) as `date`,
							COUNT(msb.`balance_id`) as `total`,
							SUM(msb.`amount`) as `gross_total`
						FROM `" . DB_PREFIX . "ms_balance` msb
						WHERE EXTRACT(YEAR_MONTH FROM msb.`date_created`) = EXTRACT(YEAR_MONTH FROM NOW())
							AND msb.balance_type = " . (int)MsBalance::MS_BALANCE_TYPE_SALE . "
							AND msb.seller_id = " . (int)$data['seller_id'] . "
						GROUP BY EXTRACT(YEAR_MONTH FROM msb.`date_created`)
					");
				} else {
					$result = $this->db->query("
						SELECT
							EXTRACT(YEAR_MONTH FROM o.`date_added`) as `date`,
							COUNT(o.order_id) as `total`,
							SUM(ot.`value`) as `gross_total`
						FROM `" . DB_PREFIX . "order` o
						LEFT JOIN (SELECT order_id, `value` FROM `" . DB_PREFIX . "order_total` WHERE `code` = 'sub_total') ot
							ON (ot.order_id = o.order_id)
						WHERE o.order_status_id <> 0
							AND EXTRACT(YEAR_MONTH FROM o.`date_added`) = EXTRACT(YEAR_MONTH FROM NOW())
						GROUP BY EXTRACT(YEAR_MONTH FROM o.`date_added`)
					");
				}

				$sales_analytics_by_month_current = $result->rows;

				// Merge results
				$results = array_merge($sales_analytics_by_month_history, $sales_analytics_by_month_current);
				foreach ($results as $result) {
					$sales_analytics[$result['date']] = array(
						'total' => $result['total'],
						'gross_total' => $result['gross_total']
					);
				}

				/**
				 * Add missing X-axis values.
				 *
				 * For example, if there are some orders in February 2017, no orders in March 2017 and April 2017, and
				 * then again some orders in May 2017, this code will set empty values for March and April, so X-axis
				 * values will be consistent.
				 */
				$date_start_ym = isset(array_values($results)[0]['date']) ? array_values($results)[0]['date'] : false;
				if ($date_start_ym) {
					$start_year = (int)substr($date_start_ym, 0, 4);
					$start_month = (int)substr($date_start_ym, -2);

					for ($y = $start_year; $y <= (int)date('Y'); $y++) {
						for ($m = $start_month; $m <= 12; $m++) {
							// Add leading zero
							$m = str_pad($m, 2, 0, STR_PAD_LEFT);

							if ($y.$m <= date('Ym') && !isset($sales_analytics[$y.$m]))
								$sales_analytics[$y.$m] = array(
									'total' => 0,
									'gross_total' => 0
								);
						}

						$start_month = str_pad(1, 2, 0, STR_PAD_LEFT);
					}
				}

				ksort($sales_analytics);

				foreach ($sales_analytics as $date_unformatted => &$data) {
					$year = (int)substr($date_unformatted, 0, 4);
					$month = (int)substr($date_unformatted, -2);
					$date_formatted = date('M Y', strtotime($year . "-" . $month));

					$data['date'] = $date_formatted;
				}

				/**
				 * If there is only one X-axis value, we add dummy values on the left and right, to make chart centered.
				 */
				if (count($sales_analytics) === 1) {
					array_unshift($sales_analytics, array('date' => '', 'total' => NULL, 'gross_total' => NULL));
					array_push($sales_analytics, array('date' => '', 'total' => NULL, 'gross_total' => NULL));
				}

				break;
		}

		return $sales_analytics;
	}

	/**
	 * Gets top 5 countries by gross total over a time period.
	 *
	 * @param	array	$data	Conditions.
	 * @return	array			Total orders created in each country and their gross total.
	 */
	public function getTopCountriesAnalytics($data = array()) {
		$date_start = $this->_calculateDateStart($data);
		$date_end = $this->_calculateDateEnd($data);

		$sql = "
			SELECT
				COUNT(o.order_id) AS `total`,
				SUM(ot.`value`) AS `gross_total`,
				c.name
			FROM `" . DB_PREFIX . "order` o
			LEFT JOIN (SELECT country_id, `name`, iso_code_3 FROM `" . DB_PREFIX . "country`) c
				ON (o.payment_country_id = c.country_id)
			LEFT JOIN (SELECT order_id, `value` FROM `" . DB_PREFIX . "order_total` WHERE `code` = 'sub_total') ot
				ON (ot.order_id = o.order_id)
			WHERE o.order_status_id <> 0
				AND c.`name` IS NOT NULL
				AND DATE(o.`date_added`) >= DATE('" . $this->db->escape($date_start) . "')
				AND DATE(o.`date_added`) <= DATE('" . $this->db->escape($date_end) . "')
			GROUP BY o.payment_country_id
			ORDER BY `gross_total` DESC
			LIMIT 5
		";

		return $this->_getAnalyticsData($sql);
	}

	/**
	 * Gets top 5 sellers by gross total over a time period.
	 *
	 * @param	array	$data	Conditions.
	 * @return	array			Total orders related to each seller and their gross total.
	 */
	public function getTopSellersAnalytics($data = array()) {
		$date_start = $this->_calculateDateStart($data);
		$date_end = $this->_calculateDateEnd($data);

		$sql = "
			SELECT
				SUM(op.quantity) as `total`,
				SUM(op.`total`) as `gross_total`,
				mss.nickname as `name`
			FROM `" . DB_PREFIX . "order_product` op
			LEFT JOIN (SELECT order_id, order_status_id, date_added FROM `" . DB_PREFIX . "order`) o
				ON (o.order_id = op.order_id)
			LEFT JOIN (SELECT order_id, product_id, order_product_id, seller_id FROM `" . DB_PREFIX . "ms_order_product_data`) msopd
				ON (msopd.product_id = op.product_id AND msopd.order_id = o.order_id AND msopd.order_product_id = op.order_product_id)
			LEFT JOIN (SELECT seller_id, nickname FROM `" . DB_PREFIX . "ms_seller`) mss
				ON (mss.seller_id = msopd.seller_id)
			WHERE o.order_status_id <> 0
				AND msopd.seller_id IS NOT NULL
				AND mss.nickname IS NOT NULL
				AND DATE(o.`date_added`) >= DATE('" . $this->db->escape($date_start) . "')
				AND DATE(o.`date_added`) <= DATE('" . $this->db->escape($date_end) . "')
			GROUP BY mss.seller_id
			ORDER BY `gross_total` DESC
			LIMIT 5
		";

		return $this->_getAnalyticsData($sql);
	}

	/**
	 * Gets top 5 customers by gross total over a time period.
	 *
	 * @param	array	$data	Conditions.
	 * @return	array			Total orders created by each customer and their gross total.
	 */
	public function getTopCustomersAnalytics($data = array()) {
		$date_start = $this->_calculateDateStart($data);
		$date_end = $this->_calculateDateEnd($data);

		$sql = "
			SELECT
				COUNT(o.order_id) as `total`,
				SUM(o.`total`) as `gross_total`,
				c.customer_name as `name`
			FROM `" . DB_PREFIX . "order` o
			LEFT JOIN (SELECT customer_id, CONCAT_WS(' ', firstname, lastname) as `customer_name` FROM `" . DB_PREFIX . "customer`) c
				ON (c.customer_id = o.customer_id)
			WHERE o.order_status_id <> 0
				AND c.customer_id IS NOT NULL
				AND DATE(o.`date_added`) >= DATE('" . $this->db->escape($date_start) . "')
				AND DATE(o.`date_added`) <= DATE('" . $this->db->escape($date_end) . "')
			GROUP BY o.customer_id
			ORDER BY `gross_total` DESC
			LIMIT 5
		";

		return $this->_getAnalyticsData($sql);
	}

	/**
	 * Gets top 5 products by gross total over a time period.
	 *
	 * @param	array	$data	Conditions.
	 * @return	array			Total orders with each product and their gross total.
	 */
	public function getTopProductsAnalytics($data = array()) {
		$date_start = $this->_calculateDateStart($data);
		$date_end = $this->_calculateDateEnd($data);

		$sql = "
			SELECT
				SUM(op.quantity) as `total`,
				SUM(op.`total`) as `gross_total`,
				pd.`name` as `name`
			FROM `" . DB_PREFIX . "order_product` op
			LEFT JOIN (SELECT order_id, order_status_id, date_added FROM `" . DB_PREFIX . "order`) o
				ON (o.order_id = op.order_id)
			LEFT JOIN (SELECT product_id, `name` FROM `" . DB_PREFIX . "product_description` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "') pd
				ON (op.product_id = pd.product_id)
			LEFT JOIN (SELECT order_id, product_id, order_product_id, seller_id FROM `" . DB_PREFIX . "ms_order_product_data`) msopd
				ON (msopd.product_id = op.product_id AND msopd.order_id = o.order_id AND msopd.order_product_id = op.order_product_id)
			WHERE o.order_status_id <> 0
				AND pd.`name` IS NOT NULL
				AND DATE(o.`date_added`) >= DATE('" . $this->db->escape($date_start) . "')
				AND DATE(o.`date_added`) <= DATE('" . $this->db->escape($date_end) . "')"

			. (isset($data['seller_id']) ? " AND msopd.seller_id = " . (int)$data['seller_id'] : "")

			. "	GROUP BY op.product_id
			ORDER BY `gross_total` DESC
			LIMIT 5
		";

		return $this->_getAnalyticsData($sql);
	}

	/**
	 * Gets top 5 products by total views at the marketplace over a period of time.
	 *
	 * @param	array	$data	Conditions.
	 * @return	array			Total orders with each product and their total views.
	 */
	public function getTopProductsByViewsAnalytics($data = array()) {
		$date_start = $this->_calculateDateStart($data);
		$date_end = $this->_calculateDateEnd($data);

		$sql = "
			SELECT
				p.viewed as `total_views`,
				pd.`name` as `name`
			FROM `" . DB_PREFIX . "product` p
			LEFT JOIN (SELECT product_id, seller_id FROM " . DB_PREFIX . "ms_product) msp
				ON (msp.product_id = p.product_id)
			LEFT JOIN (SELECT product_id, `name` FROM " . DB_PREFIX . "product_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "') pd
				ON (pd.product_id = p.product_id)
			WHERE p.viewed > 0
				AND DATE(p.`date_added`) >= DATE('" . $this->db->escape($date_start) . "')
				AND DATE(p.`date_added`) <= DATE('" . $this->db->escape($date_end) . "')"

			. (isset($data['seller_id']) ? " AND msp.seller_id =  " .  (int)$data['seller_id'] : "")

			. " ORDER BY `total_views` DESC
			LIMIT 5
		";

		return $this->_getAnalyticsData($sql);
	}

	/**
	 * Gets start date of time period for methods related to Admin Dashboard.
	 *
	 * Helper. Checks if $data['date_start'] is set, then returns it, and if not - returns '1970-01-01 00:00:00', the
	 * Unix epoch time.
	 *
	 * @param	array	$data	Conditions. Possible keys: 'date_start'.
	 * @return	string			Start date of a time period.
	 */
	private function _calculateDateStart($data) {
		return !empty($data['date_start']) ? $data['date_start'] : '1970-01-01 00:00:00';
	}

	/**
	 * Gets end date of time period for methods related to Admin Dashboard.
	 *
	 * Helper. Checks if $data['date_end'] is set, then returns it, and if not - returns current date.
	 *
	 * @param	array	$data	Conditions. Possible keys: 'date_end'.
	 * @return	string			End date of a time period.
	 */
	private function _calculateDateEnd($data) {
		return !empty($data['date_end']) ? $data['date_end'] : $this->db->query("SELECT NOW() as `now_date`")->row['now_date'];
	}

	/**
	 * Executes SQL query.
	 *
	 * In case there are less than 5 query results, complements them with 'zero-values'.
	 *
	 * @param	string	$sql	SQL query.
	 * @return	array			Query results.
	 */
	private function _getAnalyticsData($sql) {
		$result = $this->db->query($sql);

		if ((int)$result->num_rows < 5 && (int)$result->num_rows !== 0) {
			for ($i = $result->num_rows; $i < 5; $i++) {
				array_push($result->rows, array('total' => 0, 'gross_total' => 0, 'total_views' => 0, 'name' => ''));
			}
		}

		return $result->rows;
	}

	/**
	 * Gets 5 latest orders.
	 *
	 * @return	array			Orders information.
	 */
	public function getLastOrders() {
		$result = $this->db->query("
			SELECT
				o.order_id,
				o.date_added as `date_added`,
				CONCAT_WS(' ', firstname, lastname) as `customer_name`,
				o.customer_id,
				o.total,
				o.order_status_id,
				(SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS `order_status`
			FROM `" . DB_PREFIX . "order` o
			WHERE o.order_status_id <> 0
			ORDER BY o.date_added DESC
			LIMIT 5
		");

		return $result->rows;
	}

	/**
	 * Gets 5 latest suborders (seller orders).
	 *
	 * @param	array	$data	Conditions. Possible keys: 'seller_id'.
	 * @return	array			Suborders information.
	 */
	public function getSellerLastOrders($data = array()) {
		$result = $this->db->query("
			SELECT
				mso.suborder_id,
				mso.order_id,
				IFNULL(c.`name`, '" . $this->language->get('ms_conversation_customer_deleted') . "') as `customer_name`,
				mso.order_status_id,
				(SELECT `name` FROM " . DB_PREFIX . "ms_suborder_status_description WHERE ms_suborder_status_id = mso.order_status_id AND language_id = '" . (int)$this->config->get('config_language_id') . "') AS `order_status`,
				o.date_added
			FROM " . DB_PREFIX . "ms_suborder mso
			LEFT JOIN (SELECT order_id, customer_id, order_status_id, date_added FROM `" . DB_PREFIX . "order`) o
				ON (o.order_id = mso.order_id)
			LEFT JOIN (SELECT customer_id, CONCAT_WS(' ', `firstname`, `lastname`) as `name` FROM `" . DB_PREFIX . "customer`) c
				ON (c.customer_id = o.customer_id)
			WHERE o.order_status_id <> 0"
			. (isset($data['seller_id']) ? " AND mso.seller_id = " .  (int)$data['seller_id'] : "")
			. " ORDER BY o.date_added DESC
			LIMIT 5
		");

		return $result->rows;
	}

	/**
	 * Gets 5 latest messages to seller.
	 *
	 * @param	array	$data	Conditions. Possible keys: 'seller_id'.
	 * @return	array			Messages.
	 */
	public function getSellerLastMessages($data = array()) {
		$result = $this->db->query("
			SELECT
				msc.conversation_id,
				msc.title,
				(CASE
					WHEN msm.`from_admin` <> 0
					THEN '" . $this->language->get('ms_account_dashboard_msg_from_admin') . "'
					ELSE (SELECT CONCAT_WS(' ', firstname, lastname) FROM `" . DB_PREFIX . "customer` WHERE customer_id = msm.`from`)
				END) as `author`,
				msm.message,
				msm.date_created
			FROM `" . DB_PREFIX . "ms_conversation` msc
			LEFT JOIN (SELECT `message_id`, `conversation_id`, `from`, `from_admin`, `message`, `date_created` FROM `" . DB_PREFIX . "ms_message`) msm
				ON (msm.conversation_id = msc.conversation_id)
			WHERE 1 = 1"
			. (isset($data['seller_id']) ? " AND msc.conversation_id IN (SELECT conversation_id FROM `" . DB_PREFIX . "ms_conversation_participants` WHERE `customer_id` = " .  (int)$data['seller_id'] . ")" : '')

			. " ORDER BY msm.date_created DESC
			LIMIT 5
		");

		return $result->rows;
	}
}