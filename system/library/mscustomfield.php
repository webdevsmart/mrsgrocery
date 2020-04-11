<?php
class MsCustomField extends Model
{
	const LOCATION_PRODUCT = 1;
	/*const LOCATION_REGISTRATION = 2;
	const LOCATION_CHECKOUT = 3;
	const LOCATION_SELLER_PROFILE = 4;
	const LOCATION_CUSTOMER_PROFILE = 5;*/

	const STATUS_ACTIVE = 1;
	const STATUS_DISABLED = 2;


	/* ========================================   CUSTOM FIELD GROUPS   ============================================= */


	/**
	 * Gets custom field group(s) data.
	 *
	 * @param	array	$data	Conditions
	 * @param	array	$sort	Sort
	 * @param	array	$cols	Cols
	 * @return	array			Data for custom field group(s) data
	 */
	public function getCustomFieldGroups($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';
		if(isset($sort['filters'])) {
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
				mscfg.custom_field_group_id,
				mscfgd.`name`,
				mscfg.admin_id,
				(SELECT COUNT(*) FROM `" . DB_PREFIX . "ms_custom_field` WHERE custom_field_group_id = mscfg.custom_field_group_id) as `cf_count`,
				mscfg.status,
				mscfg.sort_order
			FROM `" . DB_PREFIX . "ms_custom_field_group` mscfg
			LEFT JOIN (SELECT custom_field_group_id, `name` FROM `" . DB_PREFIX . "ms_custom_field_group_description` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "') mscfgd
				ON (mscfgd.custom_field_group_id = mscfg.custom_field_group_id)
			WHERE 1 = 1"

			. (isset($data['custom_field_group_id']) ? " AND mscfg.custom_field_group_id = " . (int)$data['custom_field_group_id'] : "")
			. (isset($data['admin_id']) ? " AND mscfg.admin_id = " . (int)$data['admin_id'] : "")
			. (isset($data['status']) ? " AND mscfg.status = " . (int)$data['status'] : "")
			. (isset($data['sort_order']) ? " AND mscfg.sort_order = " . (int)$data['sort_order'] : "")

			. $wFilters

			. " GROUP BY mscfg.custom_field_group_id HAVING 1 = 1"

			. $hFilters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '')
		);

		$total = $this->db->query("SELECT FOUND_ROWS() as total_rows");
		if ($result->rows) {
			$result->rows[0]['total_rows'] = $total->row['total_rows'];

			foreach ($result->rows as &$row) {
				// Description
				$cfg_descriptions = $this->db->query("
					SELECT
						language_id,
						`name`
					FROM `" . DB_PREFIX ."ms_custom_field_group_description`
					WHERE custom_field_group_id = " . (int)$row['custom_field_group_id'] . "
					GROUP BY language_id
				");

				$cfg_descriptions_data = array();
				foreach ($cfg_descriptions->rows as $r) {
					$cfg_descriptions_data[$r['language_id']] = array(
						'name'	=>	$r['name']
					);
				}

				$row['languages'] = $cfg_descriptions_data;

				// Note
				$cf_notes = $this->db->query("
					SELECT
						language_id,
						`note`
					FROM `" . DB_PREFIX ."ms_custom_field_group_note`
					WHERE custom_field_group_id = " . (int)$row['custom_field_group_id'] . "
					GROUP BY language_id
				");

				foreach ($cf_notes->rows as $r) {
					if(isset($row['languages'][$r['language_id']])) {
						$row['languages'][$r['language_id']] = array_merge($row['languages'][$r['language_id']], array('note' => $r['note']));
					}
				}

				// Location
				$cfg_locations = $this->db->query("
					SELECT
						location_id
					FROM `" . DB_PREFIX ."ms_custom_field_group_to_location`
					WHERE custom_field_group_id = " . (int)$row['custom_field_group_id'] . "
				");

				$row['locations'] = array();
				foreach ($cfg_locations->rows as $r) {
					$row['locations'][] = $r['location_id'];
				}
			}
		}

		return isset($data['single']) && isset($result->rows[0]) ? $result->rows[0] : $result->rows;
	}

	/**
	 * Creates custom field group entries in DB.
	 * Updates existing entries if `custom_field_group_id` is passed in $data.
	 *
	 * @param	array	$data	Conditions
	 */
	public function createOrUpdateCustomFieldGroup($data = array()) {
		$this->db->query("
			INSERT INTO `" . DB_PREFIX . "ms_custom_field_group`
			SET " . (isset($data['custom_field_group_id']) ? "custom_field_group_id = '" . (int)$data['custom_field_group_id'] . "'," : "") . "
				admin_id = '" . (int)$data['admin_id'] . "',
				`status` = '" . (int)$data['status'] . "',
				sort_order = '" . (int)$data['sort_order'] . "'
			ON DUPLICATE KEY UPDATE
				admin_id = '" . (int)$data['admin_id'] . "',
				`status` = '" . (int)$data['status'] . "',
				sort_order = '" . (int)$data['sort_order'] . "'
		");

		$custom_field_group_id = isset($data['custom_field_group_id']) && $data['custom_field_group_id'] ? $data['custom_field_group_id'] : $this->db->getLastId();

		// Description
		foreach ($data['cfg_description'] as $language_id => $value) {
			$this->db->query("
				INSERT INTO `" . DB_PREFIX . "ms_custom_field_group_description`
				SET custom_field_group_id = '" . (int)$custom_field_group_id . "',
					language_id = '" . (int)$language_id . "',
					`name` = '" . $this->db->escape($value['name']) . "'
				ON DUPLICATE KEY UPDATE
					`name` = '" . $this->db->escape($value['name']) . "'
			");

			$this->db->query("
				INSERT INTO `" . DB_PREFIX . "ms_custom_field_group_note`
				SET custom_field_group_id = '" . (int)$custom_field_group_id . "',
					language_id = '" . (int)$language_id . "',
					`note` = '" . $this->db->escape($value['note']) . "'
				ON DUPLICATE KEY UPDATE
					`note` = '" . $this->db->escape($value['note']) . "'
			");
		}

		// Location
		$this->db->query("
			DELETE FROM `" . DB_PREFIX . "ms_custom_field_group_to_location`
			WHERE custom_field_group_id = '" . (int)$custom_field_group_id . "'
		");

		foreach ($data['cfg_locations'] as $location_id) {
			$this->db->query("
				INSERT INTO `" . DB_PREFIX . "ms_custom_field_group_to_location`
				SET custom_field_group_id = '" . (int)$custom_field_group_id . "',
					location_id = '" . (int)$location_id . "'
			");
		}
	}

	/**
	 * Deletes custom field group.
	 *
	 * @param	int		$custom_field_group_id	Custom field group id
	 */
	public function deleteCustomFieldGroup($custom_field_group_id) {
		$this->db->query("
			DELETE FROM `" . DB_PREFIX . "ms_custom_field_group`
			WHERE custom_field_group_id = '" . (int)$custom_field_group_id . "'
		");

		$this->db->query("
			DELETE FROM `" . DB_PREFIX . "ms_custom_field_group_description`
			WHERE custom_field_group_id = '" . (int)$custom_field_group_id . "'
		");

		$this->db->query("
			DELETE FROM `" . DB_PREFIX . "ms_custom_field_group_to_location`
			WHERE custom_field_group_id = '" . (int)$custom_field_group_id . "'
		");

		$this->db->query("
			DELETE FROM `" . DB_PREFIX . "ms_custom_field_group_note`
			WHERE custom_field_group_id = '" . (int)$custom_field_group_id . "'
		");

		$cfs = $this->db->query("
			SELECT
				custom_field_id
			FROM `" . DB_PREFIX . "ms_custom_field`
			WHERE custom_field_group_id = '" . (int)$custom_field_group_id . "'
		");

		$this->db->query("
			DELETE FROM `" . DB_PREFIX . "ms_custom_field`
			WHERE custom_field_group_id = '" . (int)$custom_field_group_id . "'
		");

		// @todo: think of refactoring this
		foreach ($cfs->rows as $cf) {
			$this->db->query("
				DELETE FROM `" . DB_PREFIX . "ms_custom_field_description`
				WHERE custom_field_id = '" . (int)$cf['custom_field_id'] . "'
			");

			$this->db->query("
				DELETE FROM `" . DB_PREFIX . "ms_custom_field_note`
				WHERE custom_field_id = '" . (int)$cf['custom_field_id'] . "'
			");

			$cfs_values = $this->db->query("
				SELECT
					custom_field_value_id
				FROM `" . DB_PREFIX . "ms_custom_field_value`
				WHERE custom_field_id = '" . (int)$cf['custom_field_id'] . "'
			");

			$this->db->query("
				DELETE FROM `" . DB_PREFIX . "ms_custom_field_value`
				WHERE custom_field_id = '" . (int)$cf['custom_field_id'] . "'
			");

			foreach ($cfs_values->rows as $cfs_value) {
				$this->db->query("
					DELETE FROM `" . DB_PREFIX . "ms_custom_field_value_description`
					WHERE custom_field_id = '" . (int)$cfs_value['custom_field_id'] . "'
				");
			}

			// Delete related custom product fields
			$this->db->query("
				DELETE FROM `" . DB_PREFIX . "ms_product_custom_field`
				WHERE custom_field_id = '" . (int)$cf['custom_field_id'] . "'
			");
		}
	}


	/* ===========================================   CUSTOM FIELDS   ================================================ */


	/**
	 * Gets custom field(s) data.
	 *
	 * @param	array	$data	Conditions
	 * @param	array	$sort	Sort
	 * @param	array	$cols	Cols
	 * @return	array			Data for custom field(s) data
	 */
	public function getCustomFields($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';
		if(isset($sort['filters'])) {
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
				mscf.custom_field_id,
				mscf.custom_field_group_id,
				mscfd.`name`,
				mscfgd.`name` as `group_name`,
				mscf.admin_id,
				mscf.type,
				mscf.required,
				mscf.`validation`,
				mscf.status,
				mscf.sort_order
			FROM `" . DB_PREFIX . "ms_custom_field` mscf
			LEFT JOIN (SELECT custom_field_id, `name` FROM `" . DB_PREFIX . "ms_custom_field_description` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "') mscfd
				ON (mscfd.custom_field_id = mscf.custom_field_id)
			LEFT JOIN (SELECT custom_field_group_id, `name` FROM `" . DB_PREFIX . "ms_custom_field_group_description` WHERE language_id = '" . (int)$this->config->get('config_language_id') . "') mscfgd
				ON (mscfgd.custom_field_group_id = mscf.custom_field_group_id)
			WHERE 1 = 1"

			. (isset($data['custom_field_id']) ? " AND mscf.custom_field_id = " . (int)$data['custom_field_id'] : "")
			. (isset($data['custom_field_group_id']) ? " AND mscf.custom_field_group_id = " . (int)$data['custom_field_group_id'] : "")
			. (isset($data['admin_id']) ? " AND mscf.admin_id = " . (int)$data['admin_id'] : "")
			. (isset($data['type']) ? " AND mscf.type = " . (int)$data['type'] : "")
			. (isset($data['required']) ? " AND mscf.required = " . (int)$data['required'] : "")
			. (isset($data['status']) ? " AND mscf.status = " . (int)$data['status'] : "")
			. (isset($data['sort_order']) ? " AND mscf.sort_order = " . (int)$data['sort_order'] : "")

			. $wFilters

			. " GROUP BY mscf.custom_field_id HAVING 1 = 1"

			. $hFilters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '')
		);

		$total = $this->db->query("SELECT FOUND_ROWS() as total_rows");
		if ($result->rows) {
			$result->rows[0]['total_rows'] = $total->row['total_rows'];

			foreach ($result->rows as &$row) {
				// Description
				$cf_descriptions = $this->db->query("
					SELECT
						language_id,
						`name`
					FROM `" . DB_PREFIX ."ms_custom_field_description`
					WHERE custom_field_id = " . (int)$row['custom_field_id'] . "
					GROUP BY language_id
				");

				$cf_descriptions_data = array();
				foreach ($cf_descriptions->rows as $r) {
					$cf_descriptions_data[$r['language_id']] = array(
						'name'	=>	$r['name']
					);
				}

				$row['languages'] = $cf_descriptions_data;

				// Note
				$cf_notes = $this->db->query("
					SELECT
						language_id,
						`note`
					FROM `" . DB_PREFIX ."ms_custom_field_note`
					WHERE custom_field_id = " . (int)$row['custom_field_id'] . "
					GROUP BY language_id
				");

				foreach ($cf_notes->rows as $r) {
					if(isset($row['languages'][$r['language_id']])) {
						$row['languages'][$r['language_id']] = array_merge($row['languages'][$r['language_id']], array('note' => $r['note']));
					}
				}

				// Custom Field Values
				$row['cf_values'] = $this->getCustomFieldValues($row['custom_field_id']);
			}
		}

		return isset($data['single']) && isset($result->rows[0]) ? $result->rows[0] : $result->rows;
	}

	/**
	 * Creates custom field entries in DB.
	 * Updates existing entries if `custom_field_id` is passed in $data.
	 *
	 * @param	array	$data	Conditions
	 */
	public function createOrUpdateCustomField($data = array()) {
		$this->db->query("
			INSERT INTO `" . DB_PREFIX . "ms_custom_field`
			SET " . (isset($data['custom_field_id']) ? " custom_field_id = '" . (int)$data['custom_field_id'] . "'," : "") . "
				custom_field_group_id = '" . (int)$data['custom_field_group_id'] . "',
				admin_id = '" . (int)$data['admin_id'] . "',
				`type` = '" . $this->db->escape($data['type']) . "',
				`required` = '" . (isset($data['required']) ? (int)$data['required'] : 0) . "',
				`validation` = '" . (isset($data['validation']) ? $this->db->escape($data['validation']) : '') . "',
				`status` = '" . (int)$data['status'] . "',
				sort_order = '" . (int)$data['sort_order'] . "'
			ON DUPLICATE KEY UPDATE
				custom_field_group_id = '" . (int)$data['custom_field_group_id'] . "',
				admin_id = '" . (int)$data['admin_id'] . "',
				`type` = '" . $this->db->escape($data['type']) . "',
				`required` = '" . (isset($data['required']) ? (int)$data['required'] : 0) . "',
				`validation` = '" . (isset($data['validation']) ? $this->db->escape($data['validation']) : '') . "',
				`status` = '" . (int)$data['status'] . "',
				sort_order = '" . (int)$data['sort_order'] . "'
		");

		$custom_field_id = isset($data['custom_field_id']) && $data['custom_field_id'] ? $data['custom_field_id'] : $this->db->getLastId();

		// Description
		foreach ($data['cf_description'] as $language_id => $value) {
			$this->db->query("
				INSERT INTO `" . DB_PREFIX . "ms_custom_field_description`
				SET custom_field_id = '" . (int)$custom_field_id . "',
					language_id = '" . (int)$language_id . "',
					`name` = '" . $this->db->escape($value['name']) . "'
				ON DUPLICATE KEY UPDATE
					`name` = '" . $this->db->escape($value['name']) . "'
			");

			$this->db->query("
				INSERT INTO `" . DB_PREFIX . "ms_custom_field_note`
				SET custom_field_id = '" . (int)$custom_field_id . "',
					language_id = '" . (int)$language_id . "',
					`note` = '" . $this->db->escape($value['note']) . "'
				ON DUPLICATE KEY UPDATE
					`note` = '" . $this->db->escape($value['note']) . "'
			");
		}

		// Custom Field Values
		if(isset($data['cf_value'])) {
			$this->db->query("
				DELETE FROM `" . DB_PREFIX . "ms_custom_field_value` WHERE custom_field_id = '" . (int)$custom_field_id . "'
			");

			$this->db->query("
				DELETE FROM `" . DB_PREFIX . "ms_custom_field_value_description` WHERE custom_field_id = '" . (int)$custom_field_id . "'
			");

			foreach ($data['cf_value'] as $cf_value) {
				$this->db->query("
					INSERT INTO `" . DB_PREFIX . "ms_custom_field_value`
					SET " . (isset($cf_value['custom_field_value_id']) ? " custom_field_value_id = '" . (int)$cf_value['custom_field_value_id'] . "'," : "") . "
						custom_field_id = '" . (int)$custom_field_id . "',
						sort_order = '" . (int)$cf_value['sort_order'] . "'
				");

				$custom_field_value_id = isset($cf_value['custom_field_value_id']) && $cf_value['custom_field_value_id'] ? $cf_value['custom_field_value_id'] : $this->db->getLastId();

				foreach ($cf_value['description'] as $language_id => $cfv_value) {
					$this->db->query("
						INSERT INTO `" . DB_PREFIX . "ms_custom_field_value_description`
						SET custom_field_value_id = '" . (int)$custom_field_value_id . "',
							custom_field_id = '" . (int)$custom_field_id . "',
							language_id = '" . (int)$language_id . "',
							`name` = '" . $this->db->escape($cfv_value['name']) . "'
					");
				}
			}
		}
	}

	/**
	 * Deletes custom field.
	 *
	 * @param	int		$custom_field_id	Custom field id
	 */
	public function deleteCustomField($custom_field_id) {
		$this->db->query("
			DELETE FROM `" . DB_PREFIX . "ms_custom_field`
			WHERE custom_field_id = '" . (int)$custom_field_id . "'
		");

		$this->db->query("
			DELETE FROM `" . DB_PREFIX . "ms_custom_field_description`
			WHERE custom_field_id = '" . (int)$custom_field_id . "'
		");

		$this->db->query("
			DELETE FROM `" . DB_PREFIX . "ms_custom_field_note`
			WHERE custom_field_id = '" . (int)$custom_field_id . "'
		");

		$this->db->query("
			DELETE FROM `" . DB_PREFIX . "ms_custom_field_value`
			WHERE custom_field_id = '" . (int)$custom_field_id . "'
		");

		$this->db->query("
			DELETE FROM `" . DB_PREFIX . "ms_custom_field_value_description`
			WHERE custom_field_id = '" . (int)$custom_field_id . "'
		");

		// Delete custom field for product
		$this->db->query("
			DELETE FROM `" . DB_PREFIX . "ms_product_custom_field`
			WHERE custom_field_id = '" . (int)$custom_field_id . "'
		");
	}

	/**
	 * Gets custom field's values for appropriate field types (checkbox, select etc.).
	 *
	 * @param	int		$custom_field_id	Custom field id
	 * @return	array						Custom field values data
	 */
	public function getCustomFieldValues($custom_field_id) {
		$cf_value_data = array();

		$result = $this->db->query("
			SELECT
				custom_field_value_id,
				custom_field_id,
				sort_order
			FROM `" . DB_PREFIX . "ms_custom_field_value`
			WHERE custom_field_id = " . (int)$custom_field_id . "
			ORDER BY sort_order ASC
		");

		foreach ($result->rows as $row) {
			$cf_value_descriptions = $this->db->query("
				SELECT
					language_id,
					`name`
				FROM `" . DB_PREFIX . "ms_custom_field_value_description`
				WHERE custom_field_value_id = '" . (int)$row['custom_field_value_id'] . "'
			");

			$cf_value_descriptions_data = array();
			foreach ($cf_value_descriptions->rows as $r) {
				$cf_value_descriptions_data[$r['language_id']] = array(
					'name'	=>	$r['name']
				);
			}

			$cf_value_data[] = array(
				'custom_field_value_id' => $row['custom_field_value_id'],
				'custom_field_id' => $row['custom_field_id'],
				'description' => $cf_value_descriptions_data,
				'sort_order' => $row['sort_order']
			);
		}

		return $cf_value_data;
	}

	/**
	 * Gets custom field type.
	 *
	 * @param	int		$custom_field_id	Custom field id
	 * @return	string						Type of custom field
	 */
	public function getCustomFieldType($custom_field_id) {
		$result = $this->db->query("
			SELECT
				`type`
			FROM `" . DB_PREFIX . "ms_custom_field`
			WHERE custom_field_id = '" . (int)$custom_field_id . "'
		");

		return $result->row['type'];
	}

	/**
	 * Gets custom field value name translated to requested language.
	 *
	 * @param	int		$custom_field_value_id	Custom field value id
	 * @param	int		$language_id			Language id
	 * @return	string							Custom field value name
	 */
	public function getCustomFieldValueName($custom_field_value_id, $language_id) {
		$result = $this->db->query("
			SELECT
				`name`
			FROM `" . DB_PREFIX . "ms_custom_field_value_description`
			WHERE custom_field_value_id = '" . (int)$custom_field_value_id . "'
				AND language_id = '" . (int)$language_id . "'
		");

		return isset($result->row['name']) ? $result->row['name'] : '';
	}

	/**
	 * Gets regex pattern for specified custom field.
	 *
	 * @param	int		$custom_field_id	Custom field id
	 * @return	string						Regex pattern
	 */
	public function getCustomFieldValidation($custom_field_id) {
		$result = $this->db->query("
			SELECT
				`validation`
			FROM `" . DB_PREFIX . "ms_custom_field`
			WHERE custom_field_id = '" . (int)$custom_field_id . "'
		");

		return $result->row['validation'];
	}


	/* =====================================   PRODUCT CUSTOM FIELDS   ============================================== */


	/**
	 * Gets custom fields' values for specified product.
	 *
	 * @param	array	$data	Conditions
	 * @return	array
	 */
	public function getProductCustomFields($data = array()) {
		$result = $this->db->query("
			SELECT
				mspcf.product_id,
				mspcf.custom_field_id,
				mscfd.`name` as `custom_field_name`,
				mscfgd.`name` as `custom_field_group_name`,
				mspcf.`value`
			FROM `" . DB_PREFIX . "ms_product_custom_field` mspcf
			LEFT JOIN (SELECT custom_field_id, custom_field_group_id FROM `" . DB_PREFIX . "ms_custom_field`) mscf
				ON (mscf.custom_field_id = mspcf.custom_field_id)
			LEFT JOIN (SELECT custom_field_id, `name` FROM `" . DB_PREFIX . "ms_custom_field_description` WHERE language_id = '" . $this->config->get('config_language_id') . "') mscfd
				ON (mscfd.custom_field_id = mspcf.custom_field_id)
			LEFT JOIN (SELECT custom_field_group_id, `name` FROM `" . DB_PREFIX . "ms_custom_field_group_description` WHERE language_id = '" . $this->config->get('config_language_id') . "') mscfgd
				ON (mscfgd.custom_field_group_id = mscf.custom_field_group_id)
			WHERE 1 = 1"
			. (isset($data['product_id']) ? " AND mspcf.product_id = " . (int)$data['product_id'] : "")
			. (isset($data['custom_field_id']) ? " AND mspcf.custom_field_id = " . (int)$data['custom_field_id'] : "")
			. (isset($data['value']) ? " AND mspcf.`value` LIKE '%" . $this->db->escape($data['value']) . "%'" : "")
		);

		return $result->rows;
	}

	/**
	 * Creates custom field entries for product in DB.
	 * Updates existing entries if `product_id` and `custom_field_id` are passed in $data.
	 *
	 * @param	array	$data	Conditions
	 */
	public function createOrUpdateProductCustomField($data = array()) {
		$this->db->query("
			INSERT INTO `" . DB_PREFIX . "ms_product_custom_field`
			SET product_id = '" . (int)$data['product_id'] . "',
				custom_field_id = '" . (int)$data['custom_field_id'] . "',
				`value` = '" . $this->db->escape($data['value']) . "'
			ON DUPLICATE KEY UPDATE
				`value` = '" . $this->db->escape($data['value']) . "'
		");
	}

	/**
	 * Gets products related to passed custom field id.
	 *
	 * @param	int		$custom_field_id	Custom field id.
	 * @return	array						Product ids.
	 */
	public function getProductsByCFId($custom_field_id) {
		$products_ids = array();

		$products = $this->db->query("
			SELECT
				product_id,
				`value`
			FROM `" . DB_PREFIX . "ms_product_custom_field`
			WHERE custom_field_id = '" . (int)$custom_field_id . "'
		");

		if($products->num_rows) {
			foreach ($products->rows as $row) {
				$value = (array)json_decode($row['value']);
				$type = key($value);

				if (empty($value[$type]) || (in_array($type, array('text', 'textarea', 'date', 'time', 'datetime')) && $value[$type][0] == '') || ($type == 'select' && $value[$type][0] == 0)) {
					continue;
				} else {
					array_push($products_ids, $row['product_id']);
				}
			}
		}

		return $products_ids;
	}
}