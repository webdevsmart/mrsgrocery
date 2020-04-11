<?php
class MsImportExportData extends Model {

	//todo
	public $CSV_SEPARATOR = ';';
	public $CSV_ENCLOSURE = '"';

	public function __construct($registry) {
		parent::__construct($registry);
	}

	public function addImportConfig($data) {
	    $this->db->query("INSERT INTO " . DB_PREFIX . "ms_import_config SET
		customer_id = '" . (int)$this->customer->getId() . "',
		config_name = '" . $this->db->escape($data['config_name']) . "',
		import_type = '" . $this->db->escape($data['import_type']) . "',
		attachment_code = '" . $this->db->escape($data['attachment_code']) . "',
		finish_row = '" . (int)$data['finish_row'] . "',
		update_key_id = '" . (int)$data['update_key_id'] . "',
		file_encoding = '" . $this->db->escape($data['file_encoding']) . "',
		mapping = '" . $this->db->escape(htmlspecialchars_decode($data['mapping'])) . "',
		date_added = NOW()
		");
		return $this->db->getLastId();
	}

	public function updateImportConfig($config_id,$data) {
		$this->db->query("UPDATE " . DB_PREFIX . "ms_import_config SET
		import_type = '" . $this->db->escape($data['import_type']) . "',
		attachment_code = '" . $this->db->escape($data['attachment_code']) . "',
		mapping = '" . $this->db->escape(htmlspecialchars_decode($data['mapping'])) . "',
		start_row = '" . (int)$data['start_row'] . "',
		finish_row = '" . (int)$data['finish_row'] . "',
		cell_container = '" .  $this->db->escape($data['cell_container']) . "',
		cell_separator = '" . $this->db->escape($data['cell_separator']) . "',
		update_key_id = '" . (int)$data['update_key_id'] . "',
		file_encoding = '" . $this->db->escape($data['file_encoding']) . "',
		date_modified = NOW()
		WHERE config_id = '" . (int)$config_id . "'
		");
		return $config_id;
	}

	public function getImportConfigs() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_import_config WHERE
			customer_id = '" . (int)$this->customer->getId() . "' OR customer_id = 0
			ORDER BY date_added DESC");
		return $query->rows;
	}

	public function getImportConfigById($config_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_import_config
		WHERE config_id = '" . (int)$config_id . "'
		");
		return $query->row;
	}

	public function importCategory($import_data, $field_type) {
		$start_row = $import_data['start_row'];
		$finish_row = $import_data['finish_row'];

        //find key field in mapping
		$csv_key = false;
		if ($import_data['update_key_id'] !== false){
			foreach($import_data['mapping'] as $csv_col=>$field_type_id){
				if ($import_data['update_key_id'] == $field_type_id){
					$csv_key = $csv_col;
				}
			}
		}

		$active_columns = array_keys($import_data['mapping']);
		$result = array();
		$file_name = $this->getFilename($import_data['attachment_code']);
		$row_num = 0;
		$all_rows_count = 0;
		$new_rows_count = 0;
		$update_rows_count = 0;

		if (($handle = fopen(DIR_UPLOAD.$file_name, 'r')) !== FALSE) {
			while(($data = fgetcsv($handle, 10*1024, $this->CSV_SEPARATOR, $this->CSV_ENCLOSURE)) !== FALSE) {
				$row_num++;
				if ($row_num < $start_row){
					$row_num++;
					continue;
				}
				if ($finish_row AND $row_num > $finish_row){
					$row_num++;
					continue;
				}

				$all_rows_count++;

				//set array with data from 1 row
				foreach ($data as $col_num=>$col_value){
					if (!in_array($col_num,$active_columns)){
						continue;
					}
					if ($import_data['file_encoding'] == 1){
						$col_value = iconv('windows-1251', 'UTF-8//IGNORE', $col_value);
					}
					$result[$row_num][$col_num] = trim($col_value);
				}

				//if set key field - create WHERE for query
				$update_condition = '';
				if (isset($field_type[$import_data['update_key_id']]['oc_sql_name']) AND isset($result[$row_num][$csv_key])){
					switch ($field_type[$import_data['update_key_id']]['oc_sql_name']){
						case 'category_id':
							$update_condition = " WHERE c.category_id = " . (int)$result[$row_num][$csv_key];
							break;
						case 'name':
							$update_condition = " WHERE cd.name = '". $this->db->escape($result[$row_num][$csv_key]) . "' AND cd.language_id = '". (int)$this->config->get('config_language_id') . "'
							";
							break;
					}
				}

				//if is set WHERE - create query
				if ($update_condition){
					$sql = "SELECT c.category_id FROM " . DB_PREFIX . "ms_category_description cd
					LEFT JOIN " . DB_PREFIX . "ms_category c USING (category_id)" . $update_condition;
					$update_query = $this->db->query($sql);
				}

				//create array, where key - oc field name and value - value for oc field
				$data = array();
				//TODO
				$data['parent_id'] = 0;
				$data['status'] = 1;
				foreach ($result[$row_num] as $col_num=>$col_value){
					$data[$field_type[$import_data['mapping'][$col_num]]['oc_sql_name']] = $col_value;
				}

				if (isset($update_query->row['category_id'])){
					$this->_updateCategory($update_query->row['category_id'], $data);
					$update_rows_count++;
				}else{
					$this->_addCategory($data);
					$new_rows_count++;
				}

			}
			fclose($handle);
		}else{
			//TODO error open file
		}

		//save results
		$this->session->data['import_result'][] = array(
			'name' => $this->language->get('ms_import_text_result_all_rows'),
			'value' => $all_rows_count
		);
		$this->session->data['import_result'][] = array(
			'name' => $this->language->get('ms_import_text_result_new_rows_count'),
			'value' => $new_rows_count
		);
		$this->session->data['import_result'][] = array(
			'name' => $this->language->get('ms_import_text_result_update_rows_count'),
			'value' => $update_rows_count
		);

		return $result;
	}

	public function importProduct($import_data, $field_type) {
		$start_row = $import_data['start_row'];
		if ($import_data['finish_row']){
            $finish_row = $import_data['finish_row'];
        }else{
            $finish_row = false;
        }

		//find key field in mapping
		$csv_key = false;
		if ($import_data['update_key_id'] !== false){
			foreach($import_data['mapping'] as $csv_col=>$field_type_id){
				if ($import_data['update_key_id'] == $field_type_id){
					$csv_key = $csv_col;
				}
			}
		}

		$active_columns = array_keys($import_data['mapping']);
		$result = array();
		$file_name = $this->getFilename($import_data['attachment_code']);
		$row_num = 1;
		$all_rows_count = 0;
		$new_rows_count = 0;
		$update_rows_count = 0;
		$duplicate_rows_count = 0;
		$update_ids = array();
		$product_ids = array();
		if (($handle = fopen(DIR_UPLOAD.$file_name, 'r')) !== FALSE) {
			while(($data = fgetcsv($handle, 10*1024, $import_data['cell_container'],$import_data['cell_separator'])) !== FALSE) {
				if ($row_num < $start_row){
					$row_num++;
					continue;
				}
                if ($finish_row AND $row_num > $finish_row){
					break;
				}
                $row_num++;
				$all_rows_count++;

				//set array with data from 1 row
				foreach ($data as $col_num=>$col_value){
					if (!in_array($col_num,$active_columns)){
						continue;
					}
					if ($import_data['file_encoding'] == 1){
						$col_value = iconv('windows-1251', 'UTF-8//IGNORE', $col_value);
					}
					$result[$row_num][$col_num] = trim($col_value);
				}

				if (empty($result[$row_num][$csv_key])){
					break;
				}

				//if set key field - create WHERE for query
				$update_condition = '';
				if (isset($field_type[$import_data['update_key_id']]['oc_sql_name']) AND isset($result[$row_num][$csv_key])){
					switch ($field_type[$import_data['update_key_id']]['oc_sql_name']){
						case 'product_id':
							$update_condition = " WHERE p.product_id = " . (int)$result[$row_num][$csv_key];
							break;
						case 'name':
							$update_condition = " WHERE pd.name = '". $this->db->escape($result[$row_num][$csv_key]) . "'";
							break;
						case 'sku':
							$update_condition = " WHERE p.sku = '" . $this->db->escape($result[$row_num][$csv_key]). "'";
							break;
                        case 'model':
                            $update_condition = " WHERE p.model = '" . $this->db->escape($result[$row_num][$csv_key]). "'";
                            break;
					}
					$update_condition.= ' AND mp.seller_id='. (int)$this->customer->getId() . ' AND pd.language_id='. (int)$this->config->get('config_language_id');
				}

				//if is set WHERE - create query
				if ($update_condition){
					$sql = "SELECT p.product_id FROM " . DB_PREFIX . "product_description pd
					LEFT JOIN " . DB_PREFIX . "product p USING (product_id)
					LEFT JOIN " . DB_PREFIX . "ms_product mp USING(product_id)
					" . $update_condition;
					$update_query = $this->db->query($sql);
				}

				//create array, where key - oc field name and value - value for oc field
				//attributes in other array
				$data = $import_data;
                $data['attributes'] = array();
				$data['new_attributes'] = array();
				foreach ($result[$row_num] as $col_num=>$col_value){
				    if (isset($field_type[$import_data['mapping'][$col_num]]['oc_sql_name']) AND $field_type[$import_data['mapping'][$col_num]]['oc_sql_name']){
						if ($field_type[$import_data['mapping'][$col_num]]['csv_col_name'] == '_ATTRIBUTE_') {
							$data['attributes'][$field_type[$import_data['mapping'][$col_num]]['oc_sql_name']] = $col_value;
						}else if($field_type[$import_data['mapping'][$col_num]]['csv_col_name'] == '_NEW_ATTRIBUTE_'){
							$data['new_attributes'][] = $col_value;
						}else{
                            $data[$field_type[$import_data['mapping'][$col_num]]['oc_sql_name']] = $col_value;
                        }

					}
				}

				$data['stores'] = array();
				foreach ($result[$row_num] as $col_num=>$col_value){
					if (isset($field_type[$import_data['mapping'][$col_num]]['oc_sql_name'])){
						if (strpos($field_type[$import_data['mapping'][$col_num]]['csv_col_name'], 'STORE')){
							$data['stores'][$field_type[$import_data['mapping'][$col_num]]['oc_sql_name']] = $col_value;
						}
					}
				}
				$data['descriptions'] = array();
				foreach ($result[$row_num] as $col_num=>$col_value){
					if (isset($field_type[$import_data['mapping'][$col_num]]['oc_sql_name'])){
						if (strpos($field_type[$import_data['mapping'][$col_num]]['csv_col_name'], 'DESCRIPTIONS')){
							$data['descriptions'][$field_type[$import_data['mapping'][$col_num]]['oc_sql_name']] = $col_value;
						}
					}
				}
				$data['names'] = array();
				foreach ($result[$row_num] as $col_num=>$col_value){
					if (isset($field_type[$import_data['mapping'][$col_num]]['oc_sql_name'])){
						if (strpos($field_type[$import_data['mapping'][$col_num]]['csv_col_name'], 'NAMES')){
							$data['names'][$field_type[$import_data['mapping'][$col_num]]['oc_sql_name']] = $col_value;
						}
					}
				}

				/*set_data_for_import_product_hook*/

				//TODO
				if (isset($update_query->row['product_id'])){
					if (!in_array($update_query->row['product_id'],$update_ids)){
						$product_id = $this->_updateProduct($update_query->row['product_id'], $data);
						$update_rows_count++;
						$update_ids[] = $update_query->row['product_id'];
					}else{
						$product_id = $update_query->row['product_id'];
						$duplicate_rows_count++;
					}

				}else{
					if ($import_data['new_product_limit'] !== false){
						if ($new_rows_count >= $import_data['new_product_limit']){
							continue;
						}
					}
					$product_id = $this->_addProduct($data);
					$new_rows_count++;
				}
				if ($product_id){
					$product_ids[] = $product_id;
				}
			}
			fclose($handle);
		}

		//save results
		$this->session->data['import_result'][] = array(
			'name' => $this->language->get('ms_import_text_result_all_rows'),
			'value' => $all_rows_count
		);
		$this->session->data['import_result'][] = array(
			'name' => $this->language->get('ms_import_text_result_duplicate_rows_count'),
			'value' => $duplicate_rows_count
		);
		$this->session->data['import_result'][] = array(
			'name' => $this->language->get('ms_import_text_result_new_rows_count'),
			'value' => $new_rows_count
		);
		$this->session->data['import_result'][] = array(
			'name' => $this->language->get('ms_import_text_result_update_rows_count'),
			'value' => $update_rows_count
		);
		$history_data = array(
			'name' => $file_name.'-product-'.date($this->language->get('datetime_format'), time()),
			'filename' => $file_name,
			'type' => 'product',
			'processed' => $all_rows_count,
			'added' => $new_rows_count,
			'updated' => $update_rows_count,
			'product_ids' => json_encode($product_ids),
			//TODO
			'errors' => 0
		);

		$this->_createHistory($history_data);

		return $result;
	}

	private function _addCategory($data){
		$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "ms_category` SET
			parent_id = '" . (int)$data['parent_id'] . "',
			seller_id = '" . (int)$this->customer->getId() . "',
			category_status = '" . (int)$data['status'] . "'"
			. (isset($data['category_id']) ? ", `category_id` = '" . $this->db->escape($data['category_id']) . "'" : "")
			. (isset($data['image']) ? ", `image` = '" . $this->db->escape($data['image']) . "'" : "")
		);

		$category_id = $this->db->getLastId();

        if ($category_id){
			$this->load->model("localisation/language");
			$languages = $this->model_localisation_language->getLanguages();

			foreach ($languages as $language) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_category_description`
				SET category_id = '" . (int)$category_id . "',
					language_id = '" . (int)$language['language_id'] . "'"
					. (isset($data['name']) ? ", name = '" . $this->db->escape($data['name']) . "'" : "")
					. (isset($data['description']) ? ", description = '" . $this->db->escape($data['description']) . "'" : "")
					. (isset($data['meta_title']) ? ", meta_title = '" . $this->db->escape($data['meta_title']) . "'" : "")
					. (isset($data['meta_description']) ? ", meta_description = '" . $this->db->escape($data['meta_description']) . "'" : "")
					. (isset($data['meta_keyword']) ? ", meta_keyword = '" . $this->db->escape($data['meta_keyword']) . "'" : ""));
			}
		}

        $stores = array($this->config->get('config_store_id'));
        foreach ($stores as $store_id) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "ms_category_to_store`
				SET category_id = '" . (int)$category_id . "',
					store_id = '" . (int)$store_id . "'");
        }

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

		if (isset($data['keyword']) AND $data['keyword']){
			$similarity_query = $this->db->query("SELECT * FROM ". DB_PREFIX . "url_alias WHERE keyword LIKE '" . $this->db->escape($data['keyword']) . "%'");
			$number = $similarity_query->num_rows;
			if ($number > 0) {
				$data['keyword'] = $data['keyword'] . "-" . $number;
			}
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'ms_category_id=" . (int)$category_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}

		$this->cache->delete('multimerch_seo_url');

		return $category_id;
	}

	private function _addProduct($data){
        //set default values
        if (!isset($data['quantity'])){
            $data['quantity'] = $data['default_quantity'];
        }
        if (!isset($data['status'])){
            $data['status'] = $data['default_product_status'];
        }
		if (isset($data['price']) AND $data['price']){
			$data['price'] = str_replace(',','.',$data['price']);
		}
		$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "product` SET
			date_added = NOW(), date_modified = NOW(),
			stock_status_id = '" . (int)$data['stock_status_id'] . "',
			quantity = '" . (int)$data['quantity'] . "',
			status = '" . (int)$data['status'] . "'"
			. (isset($data['product_id']) ? ", `product_id` = '" . $this->db->escape($data['product_id']) . "'" : "")
			. (isset($data['model']) ? ", `model` = '" . $this->db->escape($data['model']) . "'" : "")
			. (isset($data['sku']) ? ", `sku` = '" . $this->db->escape($data['sku']) . "'" : "")
			. (isset($data['price']) ? ", `price` = '" . $this->db->escape($data['price']) . "'" : "")
		);
		$product_id = $this->db->getLastId();
		if(!$product_id) return FALSE;

		// Add Product Images
		$image = '';
		if (isset($data['image']) OR isset($data['image_url'])){
			if (!file_exists(DIR_IMAGE . $data['images_path'])) {
				mkdir(DIR_IMAGE . $data['images_path']);
			}
			if (isset($data['image'])){
				$image = $data['images_path'].$data['image'];
			}else{
				if(@fopen($data['image_url'],'r')) {
					$image = $this->prepareImageByImageUrl($product_id,$data['image_url'],$data['images_path']);
				}
			}
		}
		$data['images'] = array();
		if (isset($data['images_url'])){
			$add_images = explode(',',$data['images_url']);
			foreach($add_images as $key =>$add_image){
				$add_image = trim($add_image);
				if(@fopen($add_image,'r')) {
					$data['images'][] = $this->prepareImageByImageUrl($product_id,$add_image,$data['images_path'],$key+1);
				}
			}
			$data['images'] = array_diff($data['images'], array(''));
		}
		$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($image) . "' WHERE product_id = '" . (int)$product_id . "'");
		foreach ($data['images'] as $image) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET
				product_id = '" . (int)$product_id . "',
				image = '" . $this->db->escape($image) . "'
			");
		}

		// Prepare Categories for Product
        $categories = array();
		//if categories in 1 column with separator
        if(isset($data['category']) && !empty($data['category'])) {
            $categories = explode($data['delimiter_category'], $data['category']);
			$categories = array_diff($categories, array('', NULL, false));
		}else if(isset($data['category1']) && !empty($data['category1'])){
			$categories[] = $data['category1'];
			if (isset($data['category2']) && !empty($data['category2'])){
				$categories[] = $data['category2'];
				if (isset($data['category3']) && !empty($data['category3'])){
					$categories[] = $data['category3'];
					if (isset($data['category4']) && !empty($data['category4'])){
						$categories[] = $data['category4'];
						if (isset($data['category5']) && !empty($data['category5'])){
							$categories[] = $data['category5'];
							if (isset($data['category6']) && !empty($data['category6'])){
								$categories[] = $data['category6'];
							}
						}
					}
				}
			}
        }

        // Add Product Category
        $this->db->query('DELETE FROM `' . DB_PREFIX . 'ms_product_to_category` WHERE product_id = \'' . (int)$product_id . '\'');
        if (!empty($categories)) {
            $categories = $this->_addProductCategories($categories, $data);
            foreach ($categories as $category_id) {
                $sql = "INSERT IGNORE INTO " . DB_PREFIX . "ms_product_to_category
				SET product_id = " . (int)$product_id . ",
					ms_category_id = " . (int)$category_id;
                $this->db->query($sql);
            }
        }

		$this->load->model("localisation/language");
		$languages = $this->model_localisation_language->getLanguages();
		foreach ($languages as $language) {
			if (!isset($data['meta_title']) AND isset($data['name']) AND $data['name']){
				$data['meta_title'] = $data['name'];
			}
			if (!isset($data['meta_description']) AND isset($data['description']) AND $data['description']){
				$data['meta_description'] = $this->MsLoader->MsHelper->generateMetaDescription($data['description']);
			}
			$this->db->query("INSERT INTO `" . DB_PREFIX . "product_description`
					SET product_id = '" . (int)$product_id . "',
					language_id = '" . (int)$language['language_id'] . "'"
				. (isset($data['name']) ? ", name = '" . $this->db->escape($data['name']) . "'" : "")
				. (isset($data['description']) ? ", description = '" . $this->db->escape($data['description']) . "'" : "")
				. (isset($data['meta_title']) ? ", meta_title = '" . $this->db->escape($data['meta_title']) . "'" : "")
				. (isset($data['meta_description']) ? ", meta_description = '" . $this->db->escape($data['meta_description']) . "'" : "")
				. (isset($data['meta_keyword']) ? ", meta_keyword = '" . $this->db->escape($data['meta_keyword']) . "'" : ""));
		}

		if (isset($data['names'][(int)$this->config->get('config_language_id')])) {
			$src_string = (isset($data['keyword']) && $data['keyword']) ? $data['keyword'] : $data['names'][(int)$this->config->get('config_language_id')];
			$keyword = $this->MsLoader->MsHelper->slugify($src_string);
			$similarity_query = $this->db->query("SELECT * FROM ". DB_PREFIX . "url_alias WHERE keyword LIKE '" . $this->db->escape($keyword) . "%'");
			$number = $similarity_query->num_rows;
			if ($number > 0) {
				$keyword = $keyword . "-" . $number;
			}
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($keyword) . "'");
		}

		$this->cache->delete('multimerch_seo_url');

        if (isset($data['attributes'])){
            foreach ($data['attributes'] as $attribute_id=>$attribute_value) {
                if ($attribute_value){
                    foreach ($languages as $language) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language['language_id'] . "', text = '" . $this->db->escape($attribute_value) . "'");
                    }
                }
            }
        }

		$data['product_store'] = array($this->config->get('config_store_id'));
		if (isset($data['product_store'])){
			foreach ($data['product_store'] as $store_id) {
				$sql = "INSERT INTO " . DB_PREFIX . "product_to_store
					SET product_id = " . (int)$product_id . ",
						store_id = " . (int)$store_id;
				$this->db->query($sql);
			}
		}

		//TODO product_status
		$sql = "INSERT INTO " . DB_PREFIX . "ms_product
				SET product_id = " . (int)$product_id . ",
					seller_id = " . (int)$this->customer->getId() . ",
					product_status = " . (int)MsProduct::STATUS_IMPORTED . ",
					product_approved = " . (int)$data['product_approved']
			. ( (isset($data['list_until']) && $data['list_until'] != NULL ) ? ", list_until = '" . $this->db->escape($data['list_until']) . "'" : "");

		$this->db->query($sql);

		//TODO
		if (isset($data['stores']) AND $data['stores']){
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
			foreach ($data['stores'] as $store_id=>$store_available){
				if ($store_available){
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = " . (int)$product_id . ", store_id = " . (int)$store_id);
				}
			}
		}

		if (isset($data['descriptions']) AND $data['descriptions']){
			foreach ($data['descriptions'] as $language_id=>$description) {
				$this->db->query("UPDATE `" . DB_PREFIX . "product_description` SET
						meta_description = '" . $this->db->escape($this->MsLoader->MsHelper->generateMetaDescription($description)) . "',
						description = '" . $this->db->escape($description) . "' WHERE
						language_id = '" . (int)$language_id . "' AND
						product_id = '" . (int)$product_id . "'
						");
			}
		}
		if (isset($data['names']) AND $data['names']){
			foreach ($data['names'] as $language_id=>$name) {
				$this->db->query("UPDATE `" . DB_PREFIX . "product_description` SET
						meta_title = '" . $this->db->escape($name) . "',
						name = '" . $this->db->escape($name) . "' WHERE
						language_id = '" . (int)$language_id . "' AND
						product_id = '" . (int)$product_id . "'
						");
			}
		}
		$this->load->model('localisation/currency');
		if (isset($data['currency']) AND $this->model_localisation_currency->getCurrencyByCode($data['currency']) AND isset($data['price']) AND $data['price']){
			$new_price = $this->currency->convert($data['price'],$data['currency'],$this->config->get('config_currency'));
			$this->db->query("UPDATE `" . DB_PREFIX . "product` SET
						price = '" . (float)$new_price . "' WHERE
						product_id = '" . (int)$product_id . "'
						");
		}
		if (isset($data['weight']) AND $data['weight']){
			$data['weight'] = str_replace(',','.',$data['weight']);
			if (isset($data['weight_class_id']) AND $data['weight_class_id']){
				$weight_class_id = (int)$data['weight_class_id'];
			}else{
				$weight_class_id = (int)$this->config->get('config_weight_class_id');
			}

			$this->db->query("UPDATE `" . DB_PREFIX . "product` SET
						weight = '" . (float)$data['weight'] . "',
						weight_class_id = '" . (int)$weight_class_id . "'
						WHERE product_id = '" . (int)$product_id . "'
						");
		}
		if (isset($data['tax_class_id']) AND $data['tax_class_id']){
			$this->db->query("UPDATE `" . DB_PREFIX . "product` SET
						tax_class_id = '" . (int)$data['tax_class_id'] . "' WHERE
						product_id = '" . (int)$product_id . "'
						");
		}
		/*add_product_finish_hook*/

		return $product_id;
	}

	private function _addProductCategories($categories, $data = array()) {
		$categories_id = array();
		$parent_id = 0;
		foreach ($categories as $category) {

			$category = trim($category, " \n\t");

			$result = $this->db->query('SELECT c.category_id FROM ' . DB_PREFIX . 'ms_category_description cd
						LEFT JOIN `' . DB_PREFIX . 'ms_category` c ON (c.category_id = cd.category_id)
						WHERE LOWER(cd.name) = LOWER(\'' . $this->db->escape($category) . '\')
						AND c.parent_id = \''.$parent_id.'\'
						AND c.seller_id = \''.$this->customer->getId().'\'
						LIMIT 1
					');
            if(isset($result->num_rows) AND $result->num_rows > 0 ) {
				$category_id = $result->row['category_id'];
			} else {
				$category_data = array(
					'parent_id' => $parent_id,
				    'name' => $category,
					'meta_title' => $category,
                    'keyword' => $this->MsLoader->MsHelper->slugify($category),
                    'status' => 1

				);
				$category_id = $this->_addCategory($category_data);
			}

			$parent_id = $category_id;

            if($data['fill_category']) {
				$categories_id[] = $category_id;
			}
		}
		if(!$data['fill_category']) {
			$categories_id[] = $category_id;
		}

        return array_unique($categories_id);
	}

	private function _updateCategory($category_id, $data){
		$this->db->query("UPDATE " . DB_PREFIX . "ms_category SET
			parent_id = '" . (int)$data['parent_id'] . "',
			seller_id = '" . (int)$this->customer->getId() . "',
			category_status = '" . (int)$data['status'] . "'"
			. (isset($data['image']) ? ", `image` = '" . $this->db->escape($data['image']) . "'" : "")

			. " WHERE category_id = '" . (int)$category_id . "'");


		if (isset($data['description'])){
			$this->load->model("localisation/language");
			$languages = $this->model_localisation_language->getLanguages();
			$this->db->query("DELETE FROM " . DB_PREFIX . "ms_category_description WHERE category_id = '" . (int)$category_id . "'");
			foreach ($languages as $language) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_category_description`
				SET category_id = '" . (int)$category_id . "',
					language_id = '" . (int)$language['language_id'] . "'"
					. (isset($data['name']) ? ", name = '" . $this->db->escape($data['name']) . "'" : "")
					. (isset($data['description']) ? ", description = '" . $this->db->escape($data['description']) . "'" : "")
					. (isset($data['meta_title']) ? ", meta_title = '" . $this->db->escape($data['meta_title']) . "'" : "")
					. (isset($data['meta_description']) ? ", meta_description = '" . $this->db->escape($data['meta_description']) . "'" : "")
					. (isset($data['meta_keyword']) ? ", meta_keyword = '" . $this->db->escape($data['meta_keyword']) . "'" : ""));
			}
		}
	}

	private function _updateProduct($product_id, $data){
		$log = new Log("ms_import.log");
		$product_seller = $this->db->query("SELECT seller_id FROM " . DB_PREFIX . "ms_product WHERE product_id = " . (int)$product_id);
		if (!isset($product_seller->row['seller_id']) OR (int)$product_seller->row['seller_id'] != (int)$this->customer->getId()){
			$log->write('Seller ID error');
			return $product_id;
		}

		//set default values
        if (!isset($data['quantity'])){
            $data['quantity'] = $data['default_quantity'];
        }
        if (!isset($data['status'])){
            $data['status'] = $data['default_product_status'];
        }

		if (isset($data['price']) AND $data['price']){
			$data['price'] = str_replace(',','.',$data['price']);
		}

		$image = '';
        if (isset($data['image']) OR isset($data['image_url'])){
            if (!file_exists(DIR_IMAGE . $data['images_path'])) {
                mkdir(DIR_IMAGE . $data['images_path']);
            }
            if (isset($data['image'])){
                $image = $data['images_path'].$data['image'];
            }else{
				if(@fopen($data['image_url'],'r')) {
					$image = $this->prepareImageByImageUrl($product_id,$data['image_url'],$data['images_path']);
				}
            }
        }
		$data['images'] = array();
		if (isset($data['images_url'])){
			$add_images = explode(',',$data['images_url']);
			foreach($add_images as $key =>$add_image){
				$add_image = trim($add_image);
				if(@fopen($add_image,'r')) {
					$data['images'][] = $this->prepareImageByImageUrl($product_id,$add_image,$data['images_path'],$key+1);
				}
			}
			$data['images'] = array_diff($data['images'], array(''));
		}

		$this->db->query("UPDATE " . DB_PREFIX . "product SET
			date_modified = NOW(),
			image = '" . $this->db->escape($image) . "',
			quantity = '" . (int)$data['quantity'] . "',
			status = '" . (int)$data['status'] . "'"
			. (isset($data['price']) ? ", `price` = '" . $this->db->escape($data['price']) . "'" : "")
			. (isset($data['model']) ? ", `model` = '" . $this->db->escape($data['model']) . "'" : "")
			. " WHERE product_id = '" . (int)$product_id . "'");

		$this->db->query('DELETE FROM `' . DB_PREFIX . 'product_image` WHERE product_id = \'' . (int)$product_id . '\'');
		foreach ($data['images'] as $image) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET
				product_id = '" . (int)$product_id . "',
				image = '" . $this->db->escape($image) . "'
			");
		}

        // Prepare Categories for Product
        $categories = array();
        if(isset($data['category']) && !empty($data['category'])) {
            $categories = explode($data['delimiter_category'], $data['category']);
            $categories = array_diff($categories, array('', NULL, false));
        }else if(isset($data['category1']) && !empty($data['category1'])){
            $categories[] = $data['category1'];
            if (isset($data['category2']) && !empty($data['category2'])){
                $categories[] = $data['category2'];
				if (isset($data['category3']) && !empty($data['category3'])){
					$categories[] = $data['category3'];
					if (isset($data['category4']) && !empty($data['category4'])){
						$categories[] = $data['category4'];
						if (isset($data['category5']) && !empty($data['category5'])){
							$categories[] = $data['category5'];
							if (isset($data['category6']) && !empty($data['category6'])){
								$categories[] = $data['category6'];
							}
						}
					}
				}
            }
        }

        // Add Product Category
        $this->db->query('DELETE FROM `' . DB_PREFIX . 'ms_product_to_category` WHERE product_id = \'' . (int)$product_id . '\'');
        if (!empty($categories)) {
            $categories = $this->_addProductCategories($categories, $data);
            foreach ($categories as $category_id) {
                $sql = "INSERT IGNORE INTO " . DB_PREFIX . "ms_product_to_category
				SET product_id = " . (int)$product_id . ",
					ms_category_id = " . (int)$category_id;
                $this->db->query($sql);
            }
        }

		$this->load->model("localisation/language");
        $languages = $this->model_localisation_language->getLanguages();

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
		foreach ($languages as $language) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "product_description`
				SET product_id = '" . (int)$product_id . "',
					language_id = '" . (int)$language['language_id'] . "'"
				. (isset($data['name']) ? ", name = '" . $this->db->escape($data['name']) . "'" : "")
				. (isset($data['description']) ? ", description = '" . $this->db->escape($data['description']) . "'" : "")
				. (isset($data['meta_title']) ? ", meta_title = '" . $this->db->escape($data['meta_title']) . "'" : "")
				. (isset($data['meta_description']) ? ", meta_description = '" . $this->db->escape($data['meta_description']) . "'" : "")
				. (isset($data['meta_keyword']) ? ", meta_keyword = '" . $this->db->escape($data['meta_keyword']) . "'" : ""));
		}

        $this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");
        if (isset($data['attributes'])){
		    foreach ($data['attributes'] as $attribute_id=>$attribute_value) {
                if ($attribute_value){
                    foreach ($languages as $language) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language['language_id'] . "', text = '" . $this->db->escape($attribute_value) . "'");
                    }
                }
            }
        }

//		if (isset($data['new_attributes'])){
//			$group_name = ControllerSellerAccountImport::$new_attribute_group_name;
//        	$query = $this->db->query("SELECT attribute_group_id FROM " . DB_PREFIX . "attribute_group_description WHERE LOWER(name) = LOWER('" .$this->db->escape($group_name) . "') AND language_id = " . (int)$this->config->get('config_language_id'));
//			if(isset($query->row['attribute_group_id'])) {
//				$attribute_group_id = $query->row['attribute_group_id'];
//			} else {
//				$this->db->query('INSERT INTO `' . DB_PREFIX . 'attribute_group` SET sort_order = 1');
//				$attribute_group_id = $this->db->getLastId();
//				$this->db->query('INSERT INTO `' . DB_PREFIX . 'attribute_group_description`
//				SET attribute_group_id = '.(int)$attribute_group_id.',
//				language_id = \'' . (int)$this->config->get('config_language_id') . '\',
//				name = \'' .$this->db->escape($group_name) . '\'
//				');
//			}
//			$this->db->query('INSERT IGNORE INTO `' . DB_PREFIX . 'ms_attribute_group`
//				SET attribute_group_id = '.(int)$attribute_group_id.',
//				attribute_group_status = 1,
//				seller_id = \'' . (int)$this->customer->getId() . '\'
//				');
//
//			$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "attribute SET
//							attribute_group_id = '" . (int)$attribute_group_id . "'
//						");
//			$attribute_id = $this->db->getLastId();
//			$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "ms_attribute SET
//							attribute_id = '" . (int)$attribute_id . "',
//							attribute_status = 1,
//							seller_id = '" . (int)$this->customer->getId() . "'
//						");
//			foreach ($data['new_attributes'] as $attribute_key=>$attribute_value) {
//				if ($attribute_value){
//					$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description SET
//							attribute_id  = '" . (int)$attribute_id . "',
//							name = '" . $this->db->escape('New arrtibute'. $attribute_key) . "',
//							language_id = '" . (int)$language['language_id'] . "'
//						");
//					foreach ($languages as $language) {
//						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language['language_id'] . "', text = '" . $this->db->escape($attribute_value) . "'");
//					}
//				}
//			}
//		}

		//TODO
		if (isset($data['stores']) AND $data['stores']){
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
			foreach ($data['stores'] as $store_id=>$store_available){
				if ($store_available){
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = " . (int)$product_id . ", store_id = " . (int)$store_id);
				}
			}
		}
		if (isset($data['names']) AND $data['names']){
			foreach ($data['names'] as $language_id=>$name) {
				$this->db->query("UPDATE `" . DB_PREFIX . "product_description` SET
						name = '" . $this->db->escape($name) . "' WHERE
						language_id = '" . (int)$language_id . "' AND
						product_id = '" . (int)$product_id . "'
						");
			}
		}
		if (isset($data['descriptions']) AND $data['descriptions']){
			foreach ($data['descriptions'] as $language_id=>$description) {
				$this->db->query("UPDATE `" . DB_PREFIX . "product_description` SET
						description = '" . $this->db->escape($description) . "' WHERE
						language_id = '" . (int)$language_id . "' AND
						product_id = '" . (int)$product_id . "'
						");
			}
		}
		$this->load->model('localisation/currency');
		if (isset($data['currency']) AND $this->model_localisation_currency->getCurrencyByCode($data['currency']) AND isset($data['price']) AND $data['price']){
			$new_price = $this->currency->convert($data['price'],$data['currency'],$this->config->get('config_currency'));
			$this->db->query("UPDATE `" . DB_PREFIX . "product` SET
						price = '" . (float)$new_price . "' WHERE
						product_id = '" . (int)$product_id . "'
						");
		}
		if (isset($data['weight']) AND $data['weight']){
			$data['weight'] = str_replace(',','.',$data['weight']);
			if (isset($data['weight_class_id']) AND $data['weight_class_id']){
				$weight_class_id = (int)$data['weight_class_id'];
			}else{
				$weight_class_id = (int)$this->config->get('config_weight_class_id');
			}

			$this->db->query("UPDATE `" . DB_PREFIX . "product` SET
						weight = '" . (float)$data['weight'] . "',
						weight_class_id = '" . (int)$weight_class_id . "'
						WHERE product_id = '" . (int)$product_id . "'
						");
		}
		if (isset($data['tax_class_id']) AND $data['tax_class_id']){
			$this->db->query("UPDATE `" . DB_PREFIX . "product` SET
						tax_class_id = '" . (int)$data['tax_class_id'] . "' WHERE
						product_id = '" . (int)$product_id . "'
						");
		}

		$this->db->query("UPDATE " . DB_PREFIX . "ms_product SET
			product_status = " . (int)MsProduct::STATUS_IMPORTED . "
			WHERE product_id = '" . (int)$product_id . "'");

		/*update_product_finish_hook*/

		return $product_id;
	}


	private function getFilename($attachment_code){
		$this->load->model('tool/upload');
		$upload_info = $this->model_tool_upload->getUploadByCode($attachment_code);
		if ($upload_info){
			return $upload_info['filename'];
		}else{
			return false;
		}
	}

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

    public function getSellerAttributes() {
        $query = $this->db->query("SELECT
					SQL_CALC_FOUND_ROWS
					a.*,
					agd.name as `ag_name`,
					ad.*,
					msa.seller_id,
					msa.attribute_status,
					mss.nickname
				FROM `" . DB_PREFIX . "attribute` a
				LEFT JOIN `" . DB_PREFIX . "attribute_description` ad
					USING (attribute_id)
				LEFT JOIN `" . DB_PREFIX . "ms_attribute` msa
					USING (attribute_id)
				LEFT JOIN `" . DB_PREFIX . "attribute_group_description` agd
					USING (attribute_group_id)
				LEFT JOIN `" . DB_PREFIX . "ms_seller` mss
					ON (mss.seller_id = msa.seller_id)
				WHERE
					ad.language_id = '" . (int)$this->config->get('config_language_id') . " ' 
					AND agd.language_id = '" . (int)$this->config->get('config_language_id') . " ' 
					AND msa.seller_id = '" . (int)$this->customer->getId() . " '
					AND (msa.attribute_status IS NULL OR msa.attribute_status = '" . (int)MsAttribute::STATUS_ACTIVE . "')
					");

        return $query->rows;
    }

    public function getStockStatus($stock_status_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "stock_status WHERE stock_status_id = '" . (int)$stock_status_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
        return $query->row;
    }

    private function prepareImageByImageUrl($product_id, $image_url, $images_path, $key=0){
		$from_url = $image_url;
		if ($key){
			$img_name = $product_id . '-' .$key. '-' . basename($from_url);
		}else{
			$img_name = $product_id . '-' . basename($from_url);
		}
		$to_url = DIR_IMAGE . $images_path . $img_name;
		$allowed_filetypes = $this->config->get('msconf_msg_allowed_file_types');
		$filetypes = explode(',', $allowed_filetypes);
		$filetypes = array_map('strtolower', $filetypes);
		$filetypes = array_map('trim', $filetypes);
		$ext = explode('.', $img_name);
		$ext = end($ext);
		if (in_array(strtolower($ext),$filetypes)) {
			if (!file_exists($to_url)) {
				copy($from_url, $to_url);
			}
			return $images_path . $img_name;
		}
		return '';
	}

	/**
	 *
	 * Get all the import records filtered by some condition.
	 *
	 * @param mixed $data
	 * @param mixed $sort
	 * @return mixed
	 */
	public function getImportHistory($data = array(), $sort = array()) {
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					import_id,
					seller_id,
					name,
					date_added,
					type,
					processed,
					added,
					updated,
					errors,
					ms.nickname
				FROM `" . DB_PREFIX . "ms_import_history`
				LEFT JOIN `" . DB_PREFIX . "ms_seller` ms
					USING (seller_id)
				WHERE 1 = 1 "
			. (isset($data['import_id']) ? " AND import_id =  " .  (int)$data['import_id'] : "")
			. (isset($data['name']) ? " AND name LIKE %  " .  $this->db->escape($data['name']) : "")
			. (isset($data['seller_id']) ? " AND mpp.seller_id =  " .  (int)$data['seller_id'] : "")
			. (isset($data['date_added']) ? " AND date_added =  " .  $this->db->escape($data['date_added']) : "")
			. (isset($data['type']) ? " AND type =  " .  $this->db->escape($data['type']) : "")
			. $filters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];
		return ($res->num_rows == 1 && isset($data['single']) ? $res->row : $res->rows);
	}

	private function _createHistory($data = array()) {
		$sql = "INSERT INTO " . DB_PREFIX . "ms_import_history SET 
				seller_id = '" . (int)$this->customer->getId() . "',				
				name = '" . $this->db->escape($data['name']) . "',
				filename = '" . $this->db->escape($data['filename']) . "',
				date_added = NOW(),
				type = '" . $this->db->escape($data['type']) . "',
				processed = '" . (int)$data['processed'] . "',
				added = '" . (int)$data['added'] . "',
				updated = '" . (int)$data['updated'] . "',
				product_ids = '" . $this->db->escape($data['product_ids']) . "',
				errors = '" . (int)$data['errors'] . "'
				";

		$this->db->query($sql);
		return $this->db->getLastId();
	}

	// Delete import
	public function deleteImport($import_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_import_history WHERE import_id = '" . (int)$import_id . "'");
	}

}