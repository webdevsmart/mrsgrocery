<?php
class MsSuborderStatus extends Model {

    public function __construct($registry) {
		parent::__construct($registry);
	}

	// MS order statuses methods

	/**
	 * Gets suborder statuses.
	 *
	 * @param array $data
	 * @return array
	 */
	public function getMsSuborderStatuses($data = array(), $sort = array(), $cols = array()) {
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

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					*
					FROM `" . DB_PREFIX . "ms_suborder_status` s
					JOIN `" . DB_PREFIX . "ms_suborder_status_description` sd
						USING(ms_suborder_status_id)

					WHERE 1 = 1"

			. (isset($data['ms_suborder_status_id']) ? " AND s.ms_suborder_status_id =  " .  (int)$data['ms_suborder_status_id'] : '')
			. (isset($data['language_id']) ? " AND sd.language_id =  " .  (int)$data['language_id'] : '')
			. (isset($data['status_id_exclude']) ? " AND s.ms_suborder_status_id NOT IN (" . $data['status_id_exclude'] . ")" : "")

			. $wFilters

			. " HAVING 1 = 1 "

			. $hFilters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);
		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];

		if(isset($data['ms_suborder_status_id'])) {
			$sql = "SELECT
					*
					FROM " . DB_PREFIX . "ms_suborder_status_description
					WHERE ms_suborder_status_id = " . (int)$data['ms_suborder_status_id'];

			$descriptions = $this->db->query($sql);
			$ms_suborder_status_description_data = array();
			foreach ($descriptions->rows as $result) {
				$ms_suborder_status_description_data[$result['language_id']] = array(
					'name'             => $result['name']
				);
			}
			$res->row['languages'] = $ms_suborder_status_description_data;
		}

		return (isset($data['ms_suborder_status_id'])) ? $res->row : $res->rows;
	}

	public function getSubStatusName($data = array()) {
		if (!isset($data['language_id'])){
			$data['language_id'] = 	(int)$this->config->get('config_language_id');
		}
		$result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_suborder_status` s
					JOIN `" . DB_PREFIX . "ms_suborder_status_description` sd
					USING(ms_suborder_status_id)
					WHERE s.ms_suborder_status_id = " .  (int)$data['order_status_id'] . "
					AND sd.language_id =  " .  (int)$data['language_id'] . "
					");
		if (!isset($result->row['name'])){
			$result->row['name'] = $this->MsLoader->MsHelper->getStatusName(array('order_status_id' => $this->config->get('config_order_status_id')));
		}
		return $result->row['name'];
	}

	/**
	 * add SubOrder Status
	 *
	 * @param array $data
	 * @return integer $ms_suborder_status_id
	 */
	public function addMsSuborderStatus($data) {
		$sql = "INSERT INTO `" . DB_PREFIX . "ms_suborder_status` VALUES (NULL)";
		$this->db->query($sql);
		$ms_suborder_status_id = $this->db->getLastId();
		foreach ($data['description'] as $language_id => $row) {
			$sql = "INSERT INTO " . DB_PREFIX . "ms_suborder_status_description
						SET ms_suborder_status_id = " . (int)$ms_suborder_status_id . ",
							name = '" . $this->db->escape($row['name']) . "',
							language_id = " . (int)$language_id;

			$this->db->query($sql);
		}
		return $ms_suborder_status_id;
	}

	/**
	 * edit SubOrder Status
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function editMsSuborderStatus($data) {
		foreach ($data['description'] as $language_id => $row) {
			$this->db->query("UPDATE " . DB_PREFIX . "ms_suborder_status_description
			SET name = '" . $this->db->escape($row['name']) . "'
			WHERE ms_suborder_status_id = " . (int)$data['ms_suborder_status_id'] . "
				AND language_id = " . (int)$language_id
			);
		}
		return true;
	}

	/**
	 * delete SubOrder Status
	 *
	 * @param integer $ms_suborder_status_id
	 * @return boolean
	 */
	public function deleteMsSuborderStatus($ms_suborder_status_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_suborder_status WHERE ms_suborder_status_id = " . (int)$ms_suborder_status_id);
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_suborder_status_description WHERE ms_suborder_status_id = " . (int)$ms_suborder_status_id);
		return true;
	}

	/**
	 * Gets OpenCart's order statuses.
	 *
	 * @param	array	$data	Conditions.
	 * @param	array	$sort	Data for sorting or filtering results.
	 * @return	array			Opencart order statuses.
	 */
	public function getOcOrderStatuses($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				// @todo: strict search or not
				$filters .= " AND {$k} LIKE '" . $this->db->escape($v) . "%'";
			}
		}

		$result = $this->db->query("
			SELECT
				SQL_CALC_FOUND_ROWS
				*
			FROM `" . DB_PREFIX . "order_status` os
			WHERE os.language_id = '" . (int)$this->config->get('config_language_id') . "'"
			. (isset($data['status_id_exclude']) ? " AND os.order_status_id NOT IN (" . $data['status_id_exclude'] . ")" : "")

			. $filters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '')
		);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($result->num_rows) {
			$result->rows[0]['total_rows'] = $total->row['total'];
		}

		return $result->rows;
	}

	/**
	 * Gets Multimerch suborder state information by passed suborder_state_id.
	 *
	 * If $suborder_state_id is not set or set to 0, forces cache creation and returns array with all states-statuses linkings.
	 *
	 * @param	int		$suborder_state_id	Multimerch suborder state id.
	 * @return	array						Suborder state info, containing linked ms statuses ids.
	 */
	public function getSuborderStateData($suborder_state_id = 0) {
		$suborder_state_info = $this->cache->get('ms_suborder_state_' . $suborder_state_id);

		if (!$suborder_state_info) {
			foreach ($this->config->get('msconf_suborder_state') as $state_id => $statuses) {
				if($suborder_state_id && (int)$suborder_state_id === (int)$state_id) {
					$suborder_state_info = $statuses;
				} elseif (!$suborder_state_id) {
					$suborder_state_info[$state_id] = $statuses;
				}

				$this->cache->set('ms_suborder_state_' . $state_id, $statuses);
			}
		}

		return $suborder_state_info;
	}

	/**
	 * Gets Multimerch suborder state id by passed suborder_status_id.
	 *
	 * @param	int			$suborder_status_id		Suborder status id.
	 * @return	int|bool							Suborder state id or false if not found.
	 */
	public function getSuborderStateByStatusId($suborder_status_id) {
		$suborder_state_id = false;

		$suborder_states_data = $this->getSuborderStateData();

		foreach ($suborder_states_data as $state_id => $statuses) {
			if(in_array($suborder_status_id, $statuses))
				$suborder_state_id = $state_id;
		}

		return $suborder_state_id;
	}
}