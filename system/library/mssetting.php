<?php
final class MsSetting extends Model {
	private $_slr_settings = array(
		'slr_full_name' => '',
		'slr_address_line1' => '',
		'slr_address_line2' => '',
		'slr_city' => '',
		'slr_google_geolocation' => '',
		'slr_state' => '',
		'slr_zip' => '',
		'slr_country' => 0,
		'slr_company' => '',
		'slr_website' => '',
		'slr_phone' => '',
		'slr_logo' => '',
		'slr_ga_tracking_id' => '',
		'slr_product_validation' => MsProduct::MS_PRODUCT_VALIDATION_NONE
	);

	private $_slr_gr_settings = array(
		'slr_gr_product_number_limit' => '',
		'slr_gr_product_validation' => MsProduct::MS_PRODUCT_VALIDATION_NONE
	);


	// Seller related methods

	/**
	 * Returns default set of settings for seller
	 *
	 * @return array
	 */
	public function getSellerDefaults() {
		return $this->_slr_settings;
	}

	/**
	 * Get seller settings
	 *
	 * @param array $data
	 * @return array|mixed
	 */
	public function getSellerSettings($data = array()) {
		$sql = "SELECT
					`name`,
					`value`,
					`is_encoded`
				FROM `" . DB_PREFIX . "ms_seller_setting` mss
				WHERE 1 = 1 "
				. (isset($data['seller_id']) ? " AND `seller_id` =  " .  (int)$data['seller_id'] : '')
				. (isset($data['name']) ? " AND `name` = '" . $this->db->escape($data['name']) . "'" : '')
				. (isset($data['code']) ? " AND `name` LIKE '" . $this->db->escape($data['code']) . "%'" : '');

		$res = $this->db->query($sql);

		$settings = array();
		foreach ($res->rows as $row) {
			$settings[$row['name']] = !$row['is_encoded'] ? $row['value'] : json_decode($row['value'], true);
			if(isset($data['single']) && $data['single']) $settings = $settings[$row['name']];
		}

		return $settings;
	}

	/**
	 * Calculates seller setting value.
	 *
	 * The priority of return is as follows:
	 * - if setting is set for seller, return its value
	 * - if setting is not set for seller, but is set for seller's group, return its value
	 * - if none of above are set, return default value
	 *
	 * @param	int			$seller_id
	 * @param	string		$seller_setting_name
	 * @param	string		$group_setting_name
	 * @return	string|int
	 */
	public function calculateSellerSettingValue($seller_id, $seller_setting_name, $group_setting_name = '') {
		if (!isset($this->_slr_settings[$seller_setting_name]))
			return '';

		$query = $this->db->query("
			SELECT
				ss.`value` as `seller_value`,
				gs.`value` as `group_value`
			FROM `" . DB_PREFIX . "ms_seller` s
			LEFT JOIN (SELECT `seller_id`, `name`, `value` FROM `" . DB_PREFIX . "ms_seller_setting`) ss
				ON (ss.seller_id = s.seller_id AND ss.`name` = '" . $this->db->escape($seller_setting_name) . "')
			LEFT JOIN (SELECT `seller_group_id`, `name`, `value` FROM `" . DB_PREFIX . "ms_seller_group_setting`) gs
				ON (gs.seller_group_id = s.seller_group AND gs.`name` = '" . $this->db->escape($group_setting_name) . "')
			WHERE s.seller_id = " . (int)$seller_id
		);

		if (isset($query->row['seller_value']) && $query->row['seller_value']) {
			return $query->row['seller_value'];
		} elseif (isset($query->row['group_value']) && $query->row['group_value']) {
			return $query->row['group_value'];
		} else {
			return $this->_slr_settings[$seller_setting_name];
		}
	}

	/**
	 * Creates or updates seller setting
	 *
	 * @param array $data
	 */
	public function createSellerSetting($data = array()) {
		foreach ($data['settings'] as $name => $value) {
			$value = is_array($value) ? json_encode($value) : $this->db->escape($value);
			$sql = "INSERT INTO " . DB_PREFIX . "ms_seller_setting
			 SET seller_id = " . (isset($data['seller_id']) ? (int)$data['seller_id'] : 'NULL') . ",
				name = '" . $this->db->escape($name) . "',
				value = '" . $value . "'
				ON DUPLICATE KEY UPDATE
				value = '" . $value . "'";
			$this->db->query($sql);
		}
	}

	/**
	 * Deletes seller setting
	 *
	 * @param array $data
	 */
	public function deleteSellerSetting($data = array()) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "ms_seller_setting
			WHERE name LIKE '" . $this->db->escape($data['code']) . "%'"
			. (isset($data['name']) ? " AND name = '" . $this->db->escape($data['name']) . "'" : '')
		);
	}


	/************************************************************/


	// Seller group related methods

	/**
	 * Returns default set of settings for seller group
	 *
	 * @return array
	 */
	public function getSellerGroupDefaults() {
		return $this->_slr_gr_settings;
	}

	/**
	 * Get seller group settings
	 *
	 * @param array $data
	 * @return array|mixed
	 */
	public function getSellerGroupSettings($data = array()) {
		$sql = "SELECT
					name,
					value,
					is_encoded
				FROM `" . DB_PREFIX . "ms_seller_group_setting` msgs
				WHERE 1 = 1 "
			. (isset($data['seller_group_id']) ? " AND seller_group_id =  " .  (int)$data['seller_group_id'] : '')
			. (isset($data['name']) ? " AND name = '" . $this->db->escape($data['name']) . "'" : '')
			. (isset($data['code']) ? " AND name LIKE '" . $this->db->escape($data['code']) . "%'" : '');

		$res = $this->db->query($sql);

		$settings = array();

		foreach ($res->rows as $row) {
			$settings[$row['name']] = !$row['is_encoded'] ? $row['value'] : json_decode($row['value'], true);
			if(isset($data['single']) && $data['single']) $settings = $settings[$row['name']];
		}

		return $settings;
	}

	/**
	 * Creates or updates seller group setting
	 *
	 * @param array $data
	 */
	public function createSellerGroupSetting($data = array()) {
		foreach ($data['settings'] as $name => $value) {
			$value = is_array($value) ? json_encode($value) : $this->db->escape($value);
			$sql = "INSERT INTO " . DB_PREFIX . "ms_seller_group_setting
				SET seller_group_id = " . (isset($data['seller_group_id']) ? (int)$data['seller_group_id'] : 'NULL') . ",
					name = '" . $this->db->escape($name) . "',
					value = '" . $value . "'
					ON DUPLICATE KEY UPDATE
					value = '" . $value . "'";
			$this->db->query($sql);
		}
	}

	/**
	 * Deletes seller group setting
	 *
	 * @param array $data
	 */
	public function deleteSellerGroupSetting($data = array()) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "ms_seller_group_setting
			WHERE name LIKE '" . $this->db->escape($data['code']) . "%'"
			. (isset($data['name']) ? " AND name = '" . $this->db->escape($data['name']) . "'" : '')
		);
	}
}

?>
