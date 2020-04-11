<?php

class MsPayout extends Model {
	/**
	 * Gets list of payouts.
	 *
	 * Payout is linked to invoice(s).
	 *
	 * @param	array	$data	Conditions.
	 * @param	array	$sort	Data for sorting or filtering results.
	 * @return	array			List of payouts.
	 */
	public function getPayouts($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$result = $this->db->query("
			SELECT
				SQL_CALC_FOUND_ROWS
				*
			FROM `" . DB_PREFIX . "ms_payout`
			WHERE 1 = 1"
			. (isset($data['payout_id']) ? " AND payout_id = '" . (int)$data['payout_id'] . "'" : "")

			. $filters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) && $sort['limit'] >= 0 ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '')
		);

		$total = $this->db->query("SELECT FOUND_ROWS() as total_rows");
		if ($result->rows) {
			$result->rows[0]['total_rows'] = $total->row['total_rows'];

			foreach ($result->rows as &$row) {
				$row['invoice_ids'] = $this->getInvoiceIdByPayoutId($row['payout_id']);
			}
		}

		return $result->rows;
	}

	/**
	 * Creates payout and links it with invoices.
	 *
	 * Required items to be passed in $data array:
	 * - 'date_payout_period': for what period payout is generated;
	 * - 'invoice_ids': ids of linked invoices.
	 *
	 * @param	array	$data			Conditions.
	 * @return	int		$payout_id		Payout id.
	 */
	public function createPayout($data = array()) {
		// Create payout
		$this->db->query("
			INSERT INTO `" . DB_PREFIX . "ms_payout`
			SET `date_created` = NOW(),
				`date_payout_period` = '" . $this->db->escape($data['date_payout_period']) . "'
		");

		// Get created payout's id
		$payout_id = $this->db->getLastId();

		$this->updatePayout($payout_id, array(
			'name' => $this->language->get('ms_payout_payout') . ' #' . $payout_id . ' (' . date($this->language->get('date_format_short'), strtotime($data['date_payout_period'])) . ')'
		));

		// Link created payout with invoices
		foreach ($data['invoice_ids'] as $invoice_id) {
			// Delete all older relations payout - invoice_id for current invoice_id
			$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_payout_to_invoice` WHERE `invoice_id` = '" . (int)$invoice_id . "'");

			$this->db->query("
				INSERT INTO `" . DB_PREFIX . "ms_payout_to_invoice`
				SET `payout_id` = '" . (int)$payout_id . "',
					`invoice_id` = '" . (int)$invoice_id . "'
			");
		}

		return $payout_id;
	}

	/**
	 * Updates payout.
	 *
	 * Items available for update:
	 * - 'name': some name that identifies payout;
	 *
	 * @param	int		$payout_id		Payout id.
	 * @param	array	$data			Conditions.
	 */
	public function updatePayout($payout_id, $data = array()) {
		$this->db->query("
			UPDATE `" . DB_PREFIX . "ms_payout`
				SET payout_id = payout_id"

			. (isset($data['name']) ? ", `name` = '" . $this->db->escape($data['name']) . "'" : "")

			. " WHERE `payout_id` = '" . (int)$payout_id . "'
		");
	}

	/**
	 * Deletes payout.
	 *
	 * @param	int		$payout_id		Payout id.
	 */
	public function deletePayout($payout_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_payout` WHERE `payout_id` = '" . (int)$payout_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_payout_to_invoice` WHERE `payout_id` = '" . (int)$payout_id . "'");
	}

	/**
	 * Gets payout id by passed invoice id.
	 *
	 * @param	int			$invoice_id		Invoice id.
	 * @return	int|bool					Payout id if it is found, false on if not.
	 */
	public function getPayoutIdByInvoiceId($invoice_id) {
		$result = $this->db->query("
			SELECT
				payout_id
			FROM `" . DB_PREFIX . "ms_payout_to_invoice`
			WHERE invoice_id = '" . (int)$invoice_id . "'
			ORDER BY payout_id DESC LIMIT 1
		");

		return isset($result->row['payout_id']) ? $result->row['payout_id'] : false;
	}

	/**
	 * Gets invoice(s) id by passed payout id.
	 *
	 * @param	int		$payout_id		Payout id.
	 * @return	array					Array of invoices ids.
	 */
	public function getInvoiceIdByPayoutId($payout_id) {
		$result = $this->db->query("
			SELECT
				GROUP_CONCAT(invoice_id separator ',') as `invoice_ids`
			FROM `" . DB_PREFIX . "ms_payout_to_invoice`
			WHERE payout_id = '" . (int)$payout_id . "'
			GROUP BY payout_id
		");

		return $result->num_rows && isset($result->row['invoice_ids']) ? explode(',', $result->row['invoice_ids']) : array();
	}

	/**
	 * Gets list of seller available for payouts.
	 *
	 * @param	array	$data	Conditions.
	 * @param	array	$sort	Data for sorting or filtering results.
	 * @return	array			List of sellers.
	 */
	public function getSellers($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$result = $this->db->query("
			SELECT
				SQL_CALC_FOUND_ROWS
				ms.seller_id,
				ms.nickname as `nickname`,
				
				@balance_to_date := (SELECT COALESCE(
					(SELECT
						balance
					FROM `" . DB_PREFIX ."ms_balance`
					WHERE seller_id = ms.seller_id"
					. (isset($data['date_filter']) ? " AND DATE(`date_created`) <= '" . $this->db->escape(date('Y-m-d', $data['date_filter'])) . "'" : "")
					. " ORDER BY balance_id DESC LIMIT 1
				), 0)) as `balance_to_date`,
				
				@pending := (SELECT COALESCE(
					(SELECT
						SUM(COALESCE(amount, 0))
					FROM `" . DB_PREFIX . "ms_pg_request`
					WHERE request_status = '" . (int)MsPgRequest::STATUS_UNPAID . "'
						AND request_type = '" . (int)MsPgRequest::TYPE_PAYOUT . "'
						AND seller_id = ms.seller_id
				), 0)) as `pending`,
				
				ROUND(COALESCE(
					@balance_to_date - @pending - " .
					(isset($data['date_filter']) ?
						"(SELECT ABS(COALESCE(
							(SELECT
								SUM(amount)
							FROM `" . DB_PREFIX ."ms_balance`
							WHERE seller_id = ms.seller_id
								AND balance_type = '" . (int)MsBalance::MS_BALANCE_TYPE_WITHDRAWAL . "'"
								. (isset($data['date_filter']) ? " AND DATE(`date_created`) > '" . $this->db->escape(date('Y-m-d', $data['date_filter'])) . "'" : "")
						. " ), 0)))"
					: "0")
				. ", 0), 2) as `balance`,
				
				mspr.request_id as `invoice_id`,
				mspr.request_status as `invoice_status`,
				mspr.date_created as `date_last_paid`		
			FROM `" . DB_PREFIX . "ms_seller` ms
			LEFT JOIN (SELECT request_id, request_status, amount, date_created FROM `" . DB_PREFIX . "ms_pg_request`) mspr
				ON mspr.request_id = (SELECT request_id FROM `" . DB_PREFIX . "ms_pg_request` WHERE seller_id = ms.seller_id ORDER BY date_created DESC LIMIT 1)
			WHERE 1 = 1"
			. (isset($data['seller_id']) ? " AND seller_id = '" . (int)$data['seller_id'] . "'" : "")

			. $filters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) && $sort['limit'] >= 0 ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '')
		);

		$total = $this->db->query("SELECT FOUND_ROWS() as total_rows");
		if ($result->rows) {
			$result->rows[0]['total_rows'] = $total->row['total_rows'];
		}

		return $result->rows;
	}
}