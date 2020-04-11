<?php
class MsBadge extends Model {
	/**
	 * Creates badge.
	 *
	 * @param	array	$data	Conditions.
	 */
	public function createBadge($data = array()) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "ms_badge (image) VALUES('". $this->db->escape($data['image']) . "')");
		$badge_id = $this->db->getLastId();
		
		foreach ($data['description'] as $language_id => $value) {
			$this->db->query("
				INSERT INTO " . DB_PREFIX . "ms_badge_description
				SET badge_id = '" . (int)$badge_id . "',
					language_id = '" . (int)$language_id . "',
					`name` = '" . $this->db->escape($value['name']) . "',
					description = '" . $this->db->escape($value['description']) . "'
			");
		}
	}

	/**
	 * Edits badge.
	 *
	 * @param	int		$badge_id	Badge id.
	 * @param	array	$data		Conditions.
	 */
	public function editBadge($badge_id, $data) {
		$sql = "UPDATE " . DB_PREFIX . "ms_badge
				SET image = '" . $this->db->escape($data['image']) . "'
				WHERE badge_id = " . (int)$badge_id;
		$this->db->query($sql);
		
		foreach ($data['description'] as $language_id => $language) {
			$sql = "INSERT INTO " . DB_PREFIX . "ms_badge_description
					SET badge_id = '" . (int)$badge_id . "',
						language_id = '" . (int)$language_id . "',
						`name` = '". $this->db->escape($language['name']) ."',
						description = '". $this->db->escape(htmlspecialchars(nl2br($language['description']), ENT_COMPAT)) ."'
					ON DUPLICATE KEY UPDATE
						`name` = '". $this->db->escape($language['name']) ."',
						description = '". $this->db->escape(htmlspecialchars(nl2br($language['description']), ENT_COMPAT)) ."'
					";
					
			$this->db->query($sql);
		}
	}

	/**
	 * Gets badges.
	 *
	 * @param array $data
	 * @param array $sort
	 * @return mixed
	 */
	public function getBadges($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$res = $this->db->query("
			SELECT
				SQL_CALC_FOUND_ROWS
				*,
				mb.badge_id as 'mb.badge_id'
			FROM `" . DB_PREFIX . "ms_badge` mb
			LEFT JOIN `" . DB_PREFIX . "ms_badge_description` mbd
				ON (mb.badge_id = mbd.badge_id)"

			. (isset($data['seller_id']) || isset($data['seller_group_id']) ?
				"LEFT JOIN `" . DB_PREFIX . "ms_badge_seller_group` mbsg
					ON (mb.badge_id = mbsg.badge_id)"
				: ""
			)

			. " WHERE 1 = 1"
			. " AND mbd.language_id = '" . (isset($data['language_id']) ? (int)$data['language_id'] : (int)$this->config->get('config_language_id')) . "'"
			. (isset($data['badge_id']) ? " AND mb.badge_id = " .  (int)$data['badge_id'] : '')
			. (isset($data['seller_id']) ? " AND mbsg.seller_id = " .  (int)$data['seller_id'] : '')
			. (isset($data['seller_group_id']) ? " AND mbsg.seller_group_id = " .  (int)$data['seller_group_id'] : '')

			. $filters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '')
		);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];
		
		return ($res->num_rows == 1 && isset($data['single']) ? $res->row : $res->rows);
	}

	/**
	 * Gets badge name and description indifferent languages.
	 *
	 * @param	int		$badge_id	Badge id.
	 * @return	array				Badge's description.
	 */
	public function getBadgeDescriptions($badge_id) {
		$badge_data = array();

		$res = $this->db->query("
			SELECT
				*
			FROM " . DB_PREFIX . "ms_badge_description
			WHERE badge_id = '" . (int)$badge_id . "'
		");
	
		foreach ($res->rows as $result) {
			$badge_data[$result['language_id']] = array(
				'name'        => $result['name'],
				'description' => $result['description']
			);
		}
		
		return $badge_data;
	}

	/**
	 * Deletes badge.
	 *
	 * @param	int		$badge_id	Badge id.
	 */
	public function deleteBadge($badge_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_badge_seller_group WHERE badge_id = '" . (int)$badge_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_badge_description WHERE badge_id = '" . (int)$badge_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_badge WHERE badge_id = '" . (int)$badge_id . "'");
	}

	/**
	 * Gets sellers ids related to passed badge id.
	 *
	 * @param	int		$badge_id	Badge id.
	 * @return	array				Sellers ids.
	 */
	public function getSellersByBadgeId($badge_id) {
		$sellers_ids = array();

		$sellers = $this->db->query("
			SELECT DISTINCT
				GROUP_CONCAT(seller_id SEPARATOR ',') as `sellers_ids`
			FROM `" . DB_PREFIX . "ms_badge_seller_group`
			WHERE badge_id = '" . (int)$badge_id . "'
				AND seller_id IS NOT NULL
			GROUP BY badge_id
		");

		if($sellers->num_rows && $sellers->row['sellers_ids']) {
			$sellers_ids = explode(',', $sellers->row['sellers_ids']);
		}

		$seller_groups = $this->db->query("
			SELECT DISTINCT
				GROUP_CONCAT(seller_id SEPARATOR ',') as `sellers_ids`
			FROM `" . DB_PREFIX . "ms_seller`
			WHERE seller_group IN (
				SELECT
					seller_group_id
				FROM `" . DB_PREFIX . "ms_badge_seller_group`
				WHERE badge_id = '" . (int)$badge_id . "'
					AND seller_group_id IS NOT NULL
				GROUP BY badge_id
			)
			GROUP BY seller_group
		");

		if($seller_groups->num_rows && $seller_groups->row['sellers_ids']) {
			$sellers_ids = array_merge($sellers_ids, explode(',', $seller_groups->row['sellers_ids']));
		}

		$sellers_ids = array_unique($sellers_ids);

		return $sellers_ids;
	}

	/**
	 * Gets seller groups ids related to passed badge id.
	 *
	 * @param	int		$badge_id	Badge id.
	 * @return	array				Seller groups ids.
	 */
	public function getSellerGroupsByBadgeId($badge_id) {
		$seller_groups_ids = array();

		$seller_groups = $this->db->query("
			SELECT DISTINCT
				GROUP_CONCAT(seller_group_id SEPARATOR ',') as `seller_groups_ids`
			FROM `" . DB_PREFIX . "ms_badge_seller_group`
			WHERE badge_id = '" . (int)$badge_id . "'
				AND seller_group_id IS NOT NULL
			GROUP BY badge_id
		");

		if($seller_groups->num_rows && $seller_groups->row['seller_groups_ids']) {
			$seller_groups_ids = explode(',', $seller_groups->row['seller_groups_ids']);
		}

		return $seller_groups_ids;
	}
}
