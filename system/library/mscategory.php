<?php
class MsCategory extends Model {
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 2;
	const STATUS_DISABLED = 4;


	/* ============================================   MS CATEGORIES   =============================================== */


	/**
	 * Gets MsCategory(ies) created by seller(s).
	 *
	 * @param	array	$data	Conditions.
	 * @param	array	$sort	Data for sorting or filtering results.
	 * @return	array			MsCategory(ies) created by seller(s).
	 */
	public function getCategories($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					msc.*,
					mscd.*"

				. (isset($data['category_id']) ?
					", (SELECT DISTINCT keyword
						FROM " . DB_PREFIX . "url_alias
						WHERE `query` = 'ms_category_id=" . (int)$data['category_id'] . "'
					) AS keyword"
				: "")

				. ", (SELECT GROUP_CONCAT(mscd1.name ORDER BY `level` SEPARATOR ' > ')
					FROM `" . DB_PREFIX . "ms_category_path` mscp
					LEFT JOIN `" . DB_PREFIX . "ms_category_description` mscd1
						ON (mscp.path_id = mscd1.category_id AND mscp.category_id != mscp.path_id)
					WHERE
						mscp.category_id = msc.category_id
						AND mscd1.language_id = '" . (int)$this->config->get('config_language_id') . "'
					GROUP BY mscp.category_id) AS path,
					mss.nickname
				FROM `" . DB_PREFIX . "ms_category` msc
				LEFT JOIN `" . DB_PREFIX . "ms_category_description` mscd
					USING (category_id)
				LEFT JOIN `" . DB_PREFIX . "ms_seller` mss
					ON (mss.seller_id = msc.seller_id)
				WHERE mscd.language_id = '" . (int)$this->config->get('config_language_id') . "'"

				. (isset($data['category_id']) ? " AND msc.category_id = '" . (int)$data['category_id'] . "'" : "")
				. (isset($data['parent_id']) ? " AND msc.parent_id = '" . (int)$data['parent_id'] . "'" : "")
				. (isset($data['category_status']) ? " AND msc.category_status = '" . (int)$data['category_status'] . "'" : "")
				. (isset($data['seller_ids']) ? " AND msc.seller_id IN (" . $data['seller_ids'] . ")" : "")
				. (isset($data['exclude_category_ids']) ? " AND msc.category_id NOT IN (" . $data['exclude_category_ids'] . ")" : "")

				. $filters

				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
				. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->num_rows) {
			if(isset($data['single'])) {
				$res->row['total_rows'] = $total->row['total'];
			} else {
				$res->rows[0]['total_rows'] = $total->row['total'];
			}
		}

		if(isset($data['category_id'])) {
			$res->row['languages'] = $this->_getDescriptions($data['category_id']);
			$res->row['filters'] = $this->_getFilters($data['category_id']);
			$res->row['stores'] = $this->_getStores($data['category_id']);
			if (isset($data['ms_path'])){
				$res->row['ms_path'] =  $this->getMsCategoryPath($data['category_id']);
			}
		}

		return ($res->num_rows && isset($data['single'])) ? $res->row : $res->rows;
	}

	/**
	 * Creates seller's category.
	 *
	 * @param	array	$data			Conditions.
	 * @return	int		$category_id	Category id.
	 */
	public function createCategory($data = array()) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_category`
				SET parent_id = '" . (int)$data['parent_id'] . "',
					seller_id = '" . (int)$data['seller_id'] . "',
					sort_order = '" . (int)$data['sort_order'] . "',
					category_status = '" . (int)$data['status'] . "'"
				. (isset($data['image']) ? ", `image` = '" . $this->db->escape($data['image']) . "'" : ""));

		$category_id = $this->db->getLastId();

		// descriptions
		if (isset($data['category_description'])) $this->_saveDescriptions($category_id, $data['category_description']);

		// filters
		if (isset($data['category_filter'])) $this->_saveFilters($category_id, $data['category_filter']);

		// category to store
		if (isset($data['category_store'])) $this->_saveStores($category_id, $data['category_store']);

		// seo keyword
		if (!empty($data['keyword'])) $this->_saveKeyword($category_id, $data['keyword']);

		// category path
		if(isset($data['parent_id'])) $this->_savePath($category_id, $data);

		return $category_id;
	}

	/**
	 * Updates seller's category.
	 *
	 * @param	int		$category_id	Category id.
	 * @param	array	$data			Conditions.
	 */
	public function updateCategory($category_id, $data = array()) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_category_description WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_category_filter WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_category_to_store WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query LIKE 'ms_category_id=" . (int)$category_id . "'");

		$this->db->query("UPDATE " . DB_PREFIX . "ms_category
			SET parent_id = '" . (int)$data['parent_id'] . "',
				seller_id = '" . (int)$data['seller_id'] . "',
				sort_order = '" . (int)$data['sort_order'] . "',
				category_status = '" . (int)$data['status'] . "'"
			. (isset($data['image']) ? ", `image` = '" . $this->db->escape($data['image']) . "'" : "")

			. " WHERE category_id = '" . (int)$category_id . "'");

		// descriptions
		if (isset($data['category_description'])) $this->_saveDescriptions($category_id, $data['category_description']);

		// filters
		if (isset($data['category_filter'])) $this->_saveFilters($category_id, $data['category_filter']);

		// category to store
		if (isset($data['category_store'])) $this->_saveStores($category_id, $data['category_store']);

		// seo keyword
		if (!empty($data['keyword'])) $this->_saveKeyword($category_id, $data['keyword']);

		// category path
		if(isset($data['parent_id'])) {
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_category_path` WHERE path_id = '" . (int)$category_id . "' ORDER BY level ASC");

			if ($query->rows) {
				foreach ($query->rows as $category_path) {
					// Delete the path below the current one
					$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_category_path` WHERE category_id = '" . (int)$category_path['category_id'] . "' AND level < '" . (int)$category_path['level'] . "'");

					$path = array();

					// Get the nodes new parents
					$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_category_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

					foreach ($query->rows as $result) {
						$path[] = $result['path_id'];
					}

					// Get whats left of the nodes current path
					$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_category_path` WHERE category_id = '" . (int)$category_path['category_id'] . "' ORDER BY level ASC");

					foreach ($query->rows as $result) {
						$path[] = $result['path_id'];
					}

					// Combine the paths with a new level
					$level = 0;

					foreach ($path as $path_id) {
						$this->db->query("REPLACE INTO `" . DB_PREFIX . "ms_category_path` SET category_id = '" . (int)$category_path['category_id'] . "', `path_id` = '" . (int)$path_id . "', level = '" . (int)$level . "'");

						$level++;
					}
				}
			} else {
				// Delete the path below the current one
				$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_category_path` WHERE category_id = '" . (int)$category_id . "'");

				// Fix for records with no paths
				$this->_savePath($category_id, $data);
			}
		}
	}

	/**
	 * Deletes seller's category.
	 *
	 * @param	int		$category_id	Category id.
	 */
	public function deleteCategory($category_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_category WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_category_description WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_category_filter WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_category_to_store WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query LIKE 'ms_category_id=" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_category_path WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_product_to_category WHERE ms_category_id = '" . (int)$category_id . "'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_category_path WHERE path_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$this->deleteCategory($result['category_id']);
		}

		$this->cache->delete('multimerch_seo_url');
	}

	/**
	 * Gets full MsCategory hierarchy.
	 *
	 * @param	int		$category_id	Category id.
	 * @return	string					Comma separated categories ids.
	 */
	public function getMsCategoryPath($category_id) {
		$sql = "SELECT GROUP_CONCAT(mscp.path_id ORDER BY `level` SEPARATOR ',') as category_path
			FROM `" . DB_PREFIX . "ms_category_path` mscp
			WHERE mscp.category_id = '" . (int)$category_id . "'
			GROUP BY mscp.category_id";

		$res = $this->db->query($sql);

		return $res->num_rows ? $res->row['category_path'] : '';
	}

	/**
	 * Gets Multimerch category name translated in selected language.
	 *
	 * @param	int		$category_id	Category id.
	 * @param	int		$language_id	Language id.
	 * @return	string					Category name translated in selected language.
	 */
	public function getMsCategoryName($category_id, $language_id) {
		$result = $this->db->query("
			SELECT
				`name`
			FROM `" . DB_PREFIX . "ms_category_description`
			WHERE category_id = '" . (int)$category_id . "'
				AND language_id = '" . (int)$language_id . "'
		");

		return $result->row['name'] ?: '';
	}


	/* ============================================   HELPERS   ===================================================== */


	/**
	 * Checks the legitimacy of the category.
	 *
	 * @param	int		$category_id	Category id.
	 * @return	bool					True if category is MsCategory, false if not.
	 */
	public function isMsCategory($category_id, $data = array()) {
		$sql = "SELECT 1 FROM " . DB_PREFIX. "ms_category
				WHERE category_id = " . (int)$category_id
			. (isset($data['seller_id']) ? " AND seller_id = " . (int)$data['seller_id'] : "");

		$res = $this->db->query($sql);

		return $res->num_rows ? true : false;
	}

	/**
	 * Changes category to seller relation.
	 *
	 * @param	int		$category_id	Category id.
	 * @param	int		$seller_id		Seller id.
	 */
	public function changeSeller($category_id, $seller_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "ms_category`
			SET `seller_id` = '" . (int)$seller_id . "'
			WHERE `category_id` = '" . (int)$category_id . "'");
	}

	/**
	 * Changes category status.
	 *
	 * @param	int		$category_id	Category id.
	 * @param	int		$status_id		Category status id.
	 */
	public function changeStatus($category_id, $status_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "ms_category`
			SET `category_status` = '" . (int)$status_id . "'
			WHERE `category_id` = '" . (int)$category_id . "'");
	}

	/**
	 * Gets products related to passed category id.
	 *
	 * @param	int		$category_id	Category id.
	 * @return	array					Product ids.
	 */
	public function getProductsByCategoryId($category_id) {
		$products_ids = array();

		$products = $this->db->query("
			SELECT
				GROUP_CONCAT(product_id SEPARATOR ',') as `products_ids`
			FROM `" . DB_PREFIX . "ms_product_to_category`
			WHERE ms_category_id = '" . (int)$category_id . "'
			GROUP BY ms_category_id
		");

		if($products->num_rows && $products->row['products_ids']) {
			$products_ids = explode(',', $products->row['products_ids']);
		}

		$child_categories = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "ms_category_path WHERE path_id = '" . (int)$category_id . "'");

		foreach ($child_categories->rows as $child_category) {
			$products_2 = $this->db->query("
				SELECT
					GROUP_CONCAT(product_id SEPARATOR ',') as `products_ids`
				FROM `" . DB_PREFIX . "ms_product_to_category`
				WHERE ms_category_id = '" . (int)$child_category['category_id'] . "'
				GROUP BY ms_category_id
			");

			if($products_2->num_rows && $products_2->row['products_ids']) {
				$products_ids_2 = explode(',', $products_2->row['products_ids']);
				$products_ids = array_merge($products_ids, $products_ids_2);
			}
		}

		return $products_ids;
	}

	/**
	 * Gets child categories ids for passed category id.
	 *
	 * @param	int		$category_id	Category id.
	 * @return	array					Child categories ids.
	 */
	public function getChildCategoriesByCategoryId($category_id) {
		$child_categories = $this->db->query("
			SELECT
				GROUP_CONCAT(category_id SEPARATOR ',') as `categories_ids`
			FROM `" . DB_PREFIX . "ms_category_path`
			WHERE path_id = '" . (int)$category_id . "'
			GROUP BY path_id
		");

		$categories_ids = array();
		if($child_categories->num_rows && $child_categories->row['categories_ids']) {
			$categories_ids = explode(',', $child_categories->row['categories_ids']);
		}

		return $categories_ids;
	}

	/**
	 * Gets name, description, meta_title, meta_description and meta_keyword of MsCategory.
	 *
	 * @param	int		$category_id	Category id.
	 * @return	array					MsCategory description data.
	 */
	private function _getDescriptions($category_id) {
		$category_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_category_description WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_description_data[$result['language_id']] = array(
				'name'             => htmlentities($result['name']),
				'meta_title'       => htmlentities($result['meta_title']),
				'meta_description' => htmlentities($result['meta_description']),
				'meta_keyword'     => htmlentities($result['meta_keyword']),
				'description'      => $result['description']
			);
		}

		return $category_description_data;
	}

	/**
	 * Gets filters of MsCategory.
	 *
	 * @param	int		$category_id	Category id.
	 * @return	array					MsCategory filters data.
	 */
	private function _getFilters($category_id) {
		$category_filter_data = array();

		$query = $this->db->query("SELECT
				mscf.oc_filter_id,
				fd.name,
				(SELECT `name` FROM `" . DB_PREFIX . "filter_group_description` fgd WHERE f.filter_group_id = fgd.filter_group_id AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS `group`
			FROM `" . DB_PREFIX . "ms_category_filter` mscf
			LEFT JOIN `" . DB_PREFIX . "filter` f
				ON (mscf.oc_filter_id = f.filter_id)
			LEFT JOIN `" . DB_PREFIX . "filter_description` fd
				ON (mscf.oc_filter_id = fd.filter_id)
			WHERE mscf.category_id = '" . (int)$category_id . "'
				AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $result) {
			$category_filter_data[] = array(
				'filter_id' => $result['oc_filter_id'],
				'name' => strip_tags(html_entity_decode($result['group'] . ' &gt; ' . $result['name'], ENT_QUOTES, 'UTF-8'))
			);
		}

		return $category_filter_data;
	}

	/**
	 * Gets MsCategory to stores relation.
	 *
	 * @param	int		$category_id	Category id.
	 * @return	array					MsCategory to stores data.
	 */
	private function _getStores($category_id) {
		$category_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_category_to_store WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_store_data[] = $result['store_id'];
		}

		return $category_store_data;
	}

	/**
	 * Saves category descriptions.
	 *
	 * @param	int		$category_id	Category id.
	 * @param	array	$descriptions	Category's name, description, meta_title, meta_description, meta_keyword.
	 */
	private function _saveDescriptions($category_id, $descriptions = array()) {
		foreach ($descriptions as $language_id => $value) {
			if (isset($value['meta_title']) AND !$value['meta_title']){
				$value['meta_title'] = $value['name'];
			}
			if (isset($value['meta_description']) AND !$value['meta_description']){
				$value['meta_description'] = $this->MsLoader->MsHelper->generateMetaDescription($value['meta_description']);
			}
			$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_category_description`
				SET category_id = '" . (int)$category_id . "',
					language_id = '" . (int)$language_id . "',
					`name` = '" . $this->db->escape($value['name']) . "',
					`description` = '" . $this->db->escape($value['description']) . "'"
				. (isset($value['meta_title']) ? ", meta_title = '" . $this->db->escape($value['meta_title']) . "'" : "")
				. (isset($value['meta_description']) ? ", meta_description = '" . $this->db->escape($value['meta_description']) . "'" : "")
				. (isset($value['meta_keyword']) ? ", meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'" : ""));
		}
	}

	/**
	 * Saves category filters.
	 *
	 * @param	int		$category_id	Category id.
	 * @param	array	$filters		Category to filters.
	 */
	private function _saveFilters($category_id, $filters = array()) {
		foreach ($filters as $filter_id) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_category_filter`
				SET category_id = '" . (int)$category_id . "',
					oc_filter_id = '" . (int)$filter_id . "'");
		}
	}

	/**
	 * Saves category to store relation.
	 *
	 * @param	int		$category_id	Category id.
	 * @param	array	$stores			Category to stores.
	 */
	private function _saveStores($category_id, $stores = array()) {
		foreach ($stores as $store_id) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_category_to_store`
				SET category_id = '" . (int)$category_id . "',
					store_id = '" . (int)$store_id . "'");
		}
	}

	/**
	 * Saves seo url keyword for category
	 *
	 * @param	int		$category_id	Category id.
	 * @param	string	$keyword		Category's keyword.
	 */
	private function _saveKeyword($category_id, $keyword) {
		$similarity_query = $this->db->query("SELECT * FROM ". DB_PREFIX . "url_alias WHERE keyword LIKE '" . $this->db->escape($keyword) . "%'");
		$number = $similarity_query->num_rows;

		if ($number > 0) {
			$keyword = $keyword . "-" . $number;
		}
		$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'ms_category_id=" . (int)$category_id . "', keyword = '" . $this->db->escape($keyword) . "'");

		$this->cache->delete('multimerch_seo_url');
	}

	/**
	 * Saves categories hierarchy. Uses MySQL Hierarchical Data Closure Table Pattern.
	 *
	 * @param	int		$category_id	Category id.
	 * @param	array	$data			Conditions.
	 */
	private function _savePath($category_id, $data = array()) {
		$level = 0;

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_category_path` WHERE `category_id` = '" . (int)$data['parent_id'] . "' ORDER BY `level` ASC");

		foreach ($query->rows as $result) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . (int)$level . "'");

			$level++;
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_category_path`
			SET `category_id` = '" . (int)$category_id . "',
				`path_id` = '" . (int)$category_id . "',
				`level` = '" . (int)$level . "'
			ON DUPLICATE KEY UPDATE
				`level` = '" . (int)$level . "'");
	}


	/* ============================================   OC CATEGORIES   =============================================== */


	/**
	 * Gets Opencart categories with passed parent category id.
	 *
	 * @param	array	$data	Conditions.
	 * @param	array 	$sort	Data for sorting or filtering results.
	 * @return	array			Opencart categories.
	 */
	public function getOcCategories($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$query = $this->db->query("
			SELECT
				SQL_CALC_FOUND_ROWS
				c.category_id,
				c.status,
				c.sort_order,
				cd.name,
				(SELECT GROUP_CONCAT(cd1.name ORDER BY `level` SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;')
					FROM `" . DB_PREFIX . "category_path` cp
					LEFT JOIN `" . DB_PREFIX . "category_description` cd1
						ON (cp.path_id = cd1.category_id AND cp.category_id != cp.path_id)
					WHERE
						cp.category_id = c.category_id
						AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "'
					GROUP BY cp.category_id) AS path
			FROM " . DB_PREFIX . "category c
			LEFT JOIN (SELECT category_id, name FROM " . DB_PREFIX . "category_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "') cd
				ON (c.category_id = cd.category_id)
			LEFT JOIN " . DB_PREFIX . "category_to_store c2s
				ON (c.category_id = c2s.category_id)
			WHERE c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'"

			. (isset($data['parent_id']) ? " AND c.parent_id = '" . (int)$data['parent_id'] . "'" : "")
			. (isset($data['category_status']) ? " AND c.status = '" . (int)$data['category_status'] . "'" : "")

			. $filters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : "")
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : "")

		);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($query->num_rows) {
			$query->rows[0]['total_rows'] = $total->row['total'];
		}

		foreach ($query->rows as &$row) {
			$row['name'] = str_replace('&amp;', '&', $row['name']);
		}

		return $query->rows;
	}

	/**
	 * Gets full OcCategory hierarchy.
	 *
	 * @param	int		$category_id	Category id.
	 * @return	string					Comma separated categories ids.
	 */
	public function getOcCategoryPath($category_id) {
		$sql = "SELECT GROUP_CONCAT(cp.path_id ORDER BY `level` SEPARATOR ',') as category_path
			FROM `" . DB_PREFIX . "category_path` cp
			WHERE cp.category_id = '" . (int)$category_id . "'
			GROUP BY cp.category_id";

		$res = $this->db->query($sql);

		return $res->num_rows ? $res->row['category_path'] : '';
	}

	/**
	 * Gets Opencart category name translated in selected language.
	 *
	 * @param	int		$category_id	Category id.
	 * @param	int		$language_id	Language id.
	 * @return	string					Category name translated in selected language.
	 */
	public function getOcCategoryName($category_id, $language_id) {
		$result = $this->db->query("
			SELECT
				`name`
			FROM `" . DB_PREFIX . "category_description`
			WHERE category_id = '" . (int)$category_id . "'
				AND language_id = '" . (int)$language_id . "'
		");

		return !empty($result->row['name']) ? $result->row['name'] : '';
	}

	/**
	 * Gets commission of Opencart's category.
	 *
	 * @param	string		$categories_id		Coma-seperated categories ids.
	 * @param	int			$type				Commission type.
	 * @param	array		$data				Conditions.
	 * @return	array|bool						Most appropriate commission rates fro selected categories based on product price.
	 */
	public function getOcCategoryCommission($categories_id, $type = 0, $data = array()) {
		if(!$categories_id) return false;

		$res = $this->db->query("SELECT commission_id FROM " . DB_PREFIX . "ms_category_commission WHERE category_id IN (" . $categories_id . ")");

		if(!$res->num_rows) return false;

		/**	@var array $temp_rates - Temporary array for commission rates. Used for finding the most appropriate commission in multiple categories case */
		$temp_rates = array();

		/**	@var int $commission_id - Id of the selected commission */
		$commission_id = 0;

		// Convert to int just in case
		$type = (int)$type;

		foreach ($res->rows as $row) {
			if(!isset($row['commission_id'])) continue;

			$commission_id = $row['commission_id'];

			$rates[$commission_id] = $this->MsLoader->MsCommission->getCommissionRates($commission_id);
			$rates[$commission_id]['commission_id'] = $commission_id;

			if(isset($data['price']) && isset($rates[$commission_id][$type])) {
				$commission_fee = (float)$rates[$commission_id][$type]['flat'] + ((float)$rates[$commission_id][$type]['percent'] * (float)$data['price'] / 100);

				$rates[$commission_id][$type]['calculated_fee'] = $temp_rates[$commission_id] = $commission_fee;
			}
		}

		// Find commission_id with max rates based on product price
		if(!empty($temp_rates)) {
			arsort($temp_rates);
			$commission_id = key($temp_rates);
		}

		return isset($rates[$commission_id]) ? $rates[$commission_id] : false;
	}

	/**
	 * Saves commission settings for Opencart's category.
	 *
	 * @param	int		$category_id	Category id.
	 * @param	int		$commission_id	Commission id.
	 */
	public function saveCategoryCommission($category_id, $commission_id) {
		$sql = "INSERT INTO " . DB_PREFIX . "ms_category_commission
				SET category_id = " . (int)$category_id . ",
					commission_id = " . (is_null($commission_id) ? 'NULL' : (int)$commission_id) . "
				ON DUPLICATE KEY UPDATE
					commission_id = " . (is_null($commission_id) ? 'NULL' : (int)$commission_id);

		$this->db->query($sql);
	}

}