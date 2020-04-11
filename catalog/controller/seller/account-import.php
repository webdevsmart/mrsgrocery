<?php

class ControllerSellerAccountImport extends ControllerSellerAccount {

	private $error = array();
	private $m_name = 'ms_import-export-system';
	private $m_version;
	private $m_route;
	private $m_model;
	private $licenses = array('free', 'full');
	private $m_license;
	private $file_encodings = array(
		1 => 'Windows-1251',
		2 => 'UTF-8'
	);
	static $new_attribute_group_name = 'New attributes from import';

	private $oc_field_types = array(
		'category' => array(
			1 => array(
				'csv_col_name' => '_CATEGORY_ID_',
				'oc_field_name' => 'category id',
				'oc_sql_name' => 'category_id',
				'update_key' => true,
			),
			2 => array(
				'csv_col_name' => '_NAME_',
				'oc_field_name' => 'category name',
				'oc_sql_name' => 'name',
				'update_key' => true,
			),
			3 => array(
				'csv_col_name' => '_DESCRIPTION_',
				'oc_field_name' => 'category description',
				'oc_sql_name' => 'description',
				'update_key' => false

			),
			4 => array(
				'csv_col_name' => '_IMAGE_',
				'oc_field_name' => 'image',
				'oc_sql_name' => 'image',
				'update_key' => false

			),
		),
	    'product' => array(
//		    1 => array(
//			    'csv_col_name' => '_PRODUCT_ID_',
//			    'oc_field_name' => 'product id',
//			    'oc_sql_name' => 'product_id',
//			    'update_key' => false,
//		    ),
		    2 => array(
			    'csv_col_name' => '_SKU_',
			    'oc_field_name' => 'sku',
			    'oc_sql_name' => 'sku',
			    'update_key' => true,
				'requared' => true
		    ),
//		    5 => array(
//			    'csv_col_name' => '_IMAGE_',
//			    'oc_field_name' => 'product image',
//			    'oc_sql_name' => 'image',
//			    'update_key' => false
//
//		    ),
		    6 => array(
			    'csv_col_name' => '_PRICE_',
			    'oc_field_name' => 'price',
			    'oc_sql_name' => 'price',
			    'update_key' => false,
				'requared' => false
			),
		    7 => array(
			    'csv_col_name' => '_CATEGORIES_',
			    'oc_field_name' => 'all categories',
			    'oc_sql_name' => 'category',
			    'update_key' => false,
				'requared' => false
		    ),
            8 => array(
                'csv_col_name' => '_QUANTITY_',
                'oc_field_name' => 'quantity',
                'oc_sql_name' => 'quantity',
                'update_key' => false,
				'requared' => false
            ),
//            9 => array(
//                'csv_col_name' => '_STATUS_',
//                'oc_field_name' => 'status',
//                'oc_sql_name' => 'status',
//                'update_key' => false,
//				'requared' => false
//            ),
//            10 => array(
//                'csv_col_name' => '_IMAGES_',
//                'oc_field_name' => 'product images',
//                'oc_sql_name' => 'images',
//                'update_key' => false
//
//            ),
            11 => array(
                'csv_col_name' => '_CATEGORY1_',
                'oc_field_name' => 'category level 1',
                'oc_sql_name' => 'category1',
                'update_key' => false,
				'requared' => false
			),
            12 => array(
                'csv_col_name' => '_CATEGORY2_',
                'oc_field_name' => 'category level 2',
                'oc_sql_name' => 'category2',
                'update_key' => false,
				'requared' => false
            ),
            13 => array(
                'csv_col_name' => '_CATEGORY3_',
                'oc_field_name' => 'category level 3',
                'oc_sql_name' => 'category3',
                'update_key' => false,
				'requared' => false
            ),
            14 => array(
                'csv_col_name' => '_IMAGE_URL_',
                'oc_field_name' => 'primary image',
                'oc_sql_name' => 'image_url',
                'update_key' => false,
				'requared' => false
            ),
            15 => array(
                'csv_col_name' => '_IMAGES_URL_',
                'oc_field_name' => 'images',
                'oc_sql_name' => 'images_url',
                'update_key' => false,
				'requared' => false
            ),
			16 => array(
				'csv_col_name' => '_MODEL_',
				'oc_field_name' => 'model',
				'oc_sql_name' => 'model',
				'update_key' => true,
				'requared' => false
			),
//TODO
//			17 => array(
//				'csv_col_name' => '_NEW_ATTRIBUTE_',
//				'oc_field_name' => 'new attribute',
//				'oc_sql_name' => 'new_attribute',
//				'update_key' => false
//			)

			18 => array(
				'csv_col_name' => '_CATEGORY4_',
				'oc_field_name' => 'category level 4',
				'oc_sql_name' => 'category4',
				'update_key' => false,
				'requared' => false
			),
			19 => array(
				'csv_col_name' => '_CATEGORY5_',
				'oc_field_name' => 'category level 5',
				'oc_sql_name' => 'category5',
				'update_key' => false,
				'requared' => false
			),
			20 => array(
				'csv_col_name' => '_CATEGORY6_',
				'oc_field_name' => 'category level 6',
				'oc_sql_name' => 'category6',
				'update_key' => false,
				'requared' => false
			),
		),
		'seller' => array(
		)

	);

	private $import_types = array('seller','product','category');

	public function __construct($registry) {
		parent::__construct($registry);

		foreach ($this->licenses as $license) {
			if(file_exists(DIR_SYSTEM . 'config/multimerch/' . $this->m_name . '_' . $license . '.php')) {
				$this->load->config('multimerch/' . $this->m_name . '_' . $license);
				$this->m_license = $license;
			}
		}

		$this->m_version = $this->config->get($this->m_name . '_extension_version');
		$this->m_route = $this->config->get($this->m_name . '_route');

		// Load model
		$this->load->model('tool/upload');
		$this->data = array_merge(isset($this->data) ? $this->data : array(), $this->load->language($this->m_route));
		$this->data['types'] = array(
//			'seller' => array(
//				'id' => 'seller',
//				'name' => $this->language->get('ms_import_text_type_seller')
//			),
			'product' => array(
				'id' => 'product',
				'name' => $this->language->get('ms_import_text_type_product')
			),
//			'category' => array(
//				'id' => 'category',
//				'name' => $this->language->get('ms_import_text_type_category')
//			)
		);

		//add attributes in $oc_field_types['products']
        $seller_attributes = MsLoader::getInstance()->MsImportExportData->getSellerAttributes();
        $i = 101;
        foreach ($seller_attributes as $seller_attribute){
            $this->oc_field_types['product'][$i] = array(
                'csv_col_name' => '_ATTRIBUTE_',
                'oc_field_name' => $seller_attribute['name'],
                'oc_sql_name' => $seller_attribute['attribute_id'],
                'update_key' => false,
				'requared' => false
            );
           $i++;
        }

		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));

		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();
		foreach ($languages as $language){
			$this->oc_field_types['product'][] = array(
				'csv_col_name' => '_DESCRIPTIONS_'.$language['language_id'].'_',
				'oc_field_name' => 'product description ('.$language['name'].')',
				'oc_sql_name' => $language['language_id'],
				'update_key' => false,
				'requared' => false
			);
		}
		foreach ($languages as $language){
			$this->oc_field_types['product'][] = array(
				'csv_col_name' => '_NAMES_'.$language['language_id'].'_',
				'oc_field_name' => 'product name ('.$language['name'].')',
				'oc_sql_name' => $language['language_id'],
				'update_key' => false,
				'requared' => false
			);
		}
		$this->oc_field_types['product'][] = array(
			'csv_col_name' => '_STORE_0_',
			'oc_field_name' => 'available to '.$this->config->get('config_name'),
			'oc_sql_name' => 0,
			'update_key' => false,
			'requared' => false
		);
		$this->load->model('setting/store');
		$stores = $this->model_setting_store->getStores();
		foreach ($stores as $store){
			$this->oc_field_types['product'][] = array(
				'csv_col_name' => '_STORE_'.$store['store_id'].'_',
				'oc_field_name' => 'available to '.$store['name'],
				'oc_sql_name' => $store['store_id'],
				'update_key' => false,
				'requared' => false
			);
		}
		$this->oc_field_types['product'][] = array(
			'csv_col_name' => '_CURRENCY_',
			'oc_field_name' => 'currency',
			'oc_sql_name' => 'currency',
			'update_key' => false,
			'requared' => false
		);
		$this->oc_field_types['product'][] = array(
			'csv_col_name' => '_WEIGHT_',
			'oc_field_name' => 'weight',
			'oc_sql_name' => 'weight',
			'update_key' => false,
			'requared' => false
		);

		$this->oc_field_types['product'][] = array(
			'csv_col_name' => '_WEIGHT_CLASS_ID_',
			'oc_field_name' => 'weight class_id',
			'oc_sql_name' => 'weight_class_id',
			'update_key' => false,
			'requared' => false
		);

		$this->oc_field_types['product'][] = array(
			'csv_col_name' => '_TAX_CLASS_ID_',
			'oc_field_name' => 'tax class id',
			'oc_sql_name' => 'tax_class_id',
			'update_key' => false,
			'requared' => false
		);

		/*set_oc_field_types_hook*/



		//fields name in language file
		foreach ($this->oc_field_types['product'] as $field_id=>$field_value){
			if (isset($this->data['ms_imports_text_field_type_'.$field_value['oc_sql_name']])){
				$this->oc_field_types['product'][$field_id]['oc_field_name'] = $this->data['ms_imports_text_field_type_'.$field_value['oc_sql_name']];
			}else{
				if (strpos($field_value['oc_field_name'],'available to') !== false AND isset($this->data['ms_imports_text_field_type_available_to'])){
					$this->oc_field_types['product'][$field_id]['oc_field_name'] = str_replace('available to', $this->data['ms_imports_text_field_type_available_to'], $this->oc_field_types['product'][$field_id]['oc_field_name']);
				}
				if (strpos($field_value['oc_field_name'],'product description') !== false AND isset($this->data['ms_imports_text_field_type_product_description'])){
					$this->oc_field_types['product'][$field_id]['oc_field_name'] = str_replace('product description', $this->data['ms_imports_text_field_type_product_description'], $this->oc_field_types['product'][$field_id]['oc_field_name']);
				}
				if (strpos($field_value['oc_field_name'],'product name') !== false AND isset($this->data['ms_imports_text_field_type_product_name'])){
					$this->oc_field_types['product'][$field_id]['oc_field_name'] = str_replace('product name', $this->data['ms_imports_text_field_type_product_name'], $this->oc_field_types['product'][$field_id]['oc_field_name']);
				}
			}

		}

		if ($this->config->get('msconf_import_category_type')) {
			unset($this->oc_field_types['product'][7]);
		}else{
			unset($this->oc_field_types['product'][11]);
			unset($this->oc_field_types['product'][12]);
			unset($this->oc_field_types['product'][13]);
			unset($this->oc_field_types['product'][18]);
			unset($this->oc_field_types['product'][19]);
			unset($this->oc_field_types['product'][20]);
		}

		$this->document->setTitle($this->language->get('ms_import_text_title'));
		$this->document->addScript('catalog/view/javascript/multimerch/account-import.js');
	}

	public function index() {
		$this->data['heading_title'] = $this->language->get('ms_imports_text_imports');
		$this->data['prepare'] = $this->url->link('seller/account-import/prepare', '', 'SSL');
		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('multiseller/import/account-import');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function prepare() {
		if (!$this->MsLoader->MsSeller->isSeller()) {
			$this->response->redirect($this->url->link('seller/account-profile', '', 'SSL'));
		}

		if (!$this->config->get('msconf_import_enable')) {
			$this->response->redirect($this->url->link('seller/account-product', '', 'SSL'));
		}

		$this->document->addScript('catalog/view/javascript/plupload/plupload.js');
		$this->document->addScript('catalog/view/javascript/plupload/plupload.html5.js');

		$this->MsLoader->MsHelper->addStyle('multimerch/bootstrap-nav-wizard');
		$this->MsLoader->MsHelper->addStyle('multimerch/import');

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->setBreadcrumbs(array(
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_account_products_breadcrumbs'),
				'href' => $this->url->link('seller/account-product', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_import_text_header'),
				'href' => $this->url->link('seller/account-import/prepare', '', 'SSL'),
			)
		));

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('multiseller/import/prepare');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function import() {
		if (!$this->MsLoader->MsSeller->isSeller()) {
			$this->response->redirect($this->url->link('seller/account-profile', '', 'SSL'));
		}
		if (!$this->config->get('msconf_import_enable')) {
			$this->response->redirect($this->url->link('seller/account-product', '', 'SSL'));
		}
		if (isset($this->request->post['update_key_id']) AND $this->request->post['update_key_id']){
			$this->session->data['import_data']['update_key_id'] = $this->request->post['update_key_id'];
		}else{
			$this->session->data['import_data']['update_key_id'] = 2;
		}
		$import_type = $this->session->data['import_data']['import_type'];
		switch ($import_type){
//				case 'category':
//					MsLoader::getInstance()->MsImportExportData->importCategory($this->session->data['import_data'],$this->oc_field_types['category']);
//					break;
			case 'product':
				MsLoader::getInstance()->MsImportExportData->importProduct($this->session->data['import_data'],$this->oc_field_types['product']);
				break;
			default:
				break;
		}
		$this->session->data['import_data'] = array();
		$this->response->redirect($this->url->link('seller/account-product', '', 'SSL'));
	}

	public function validate_import_data(){
		$json = array();
		if (!isset($this->session->data['import_data']['import_type'])){
			$json['errors'][] = $this->language->get('ms_import_text_error_type');
		}

		$upload_info = $this->model_tool_upload->getUploadByCode($this->session->data['import_data']['attachment_code']);
		$simple_data = MsLoader::getInstance()->MsImportExportFile->getSamplesData(DIR_UPLOAD.$upload_info['filename'],$this->session->data['import_data']);

		// check rows
		if (!$simple_data){
			$json['errors'][] = $this->language->get('ms_import_text_error_no_data_for_import');
		}
		//check column count on all rows
//		$first_row_count = count(reset($simple_data));
//		foreach($simple_data as $row){
//			if ($first_row_count != count($row)){
//				$json['errors'][] = $this->language->get('ms_import_text_error_invalid_data_for_import');
//			}
//		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		return;
	}

//	public function step1() {
//	    $this->session->data['import_data'] = array();
//		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
//			$json = array();
//			$this->session->data['import_data']['config_id'] = 0;
//			if ($this->request->post['config_id']){
//				$config = $this->MsLoader->MsImportExportData->getImportConfigById($this->request->post['config_id']);
//				if ($config){
//					$this->session->data['import_data'] = $config;
//				}
//			}else{
//				if (isset($this->request->post['type']) AND in_array($this->request->post['type'], $this->import_types)){
//					$this->session->data['import_data']['import_type'] = $this->request->post['type'];
//				}else{
//					$json['errors']['error_type'] = $this->language->get('ms_import_text_error_type');
//				}
//			}
//			$this->response->addHeader('Content-Type: application/json');
//			$this->response->setOutput(json_encode($json));
//			return;
//		}
//		$this->data['configs'] = $this->MsLoader->MsImportExportData->getImportConfigs();
//		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('multiseller/import/step1');
//		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
//	}

	public function step2() {
		if (!$this->MsLoader->MsSeller->isSeller()) {
			$this->response->redirect($this->url->link('seller/account-profile', '', 'SSL'));
		}
		if (!$this->config->get('msconf_import_enable')) {
			$this->response->redirect($this->url->link('seller/account-product', '', 'SSL'));
		}
		$this->session->data['import_data'] = array();
		$this->session->data['import_data']['import_type'] = 'product';
		$this->session->data['import_data']['update_key_id'] =0;
		$this->session->data['import_data']['config_id'] = 0;

		$this->session->data['import_data']['cell_separator'] = '"';
		$this->session->data['import_data']['cell_container'] = ';';
		$this->session->data['import_data']['start_row'] = 2;
		$this->session->data['import_data']['finish_row'] = '';
		$this->session->data['import_data']['file_encoding'] = 1;

		$seller_groupe = $this->MsLoader->MsSellerGroup->getSellerGroupBySellerId($this->customer->getId());
		if (isset($seller_groupe['seller_product_limit']) AND $seller_groupe['seller_product_limit']){
			$this->session->data['import_data']['new_product_limit'] = $seller_groupe['seller_product_limit'] - $seller_groupe['seller_product_quantity'];
			if ($this->session->data['import_data']['new_product_limit'] < 0){
				$this->session->data['import_data']['new_product_limit'] = 0;
			}
		}else{
			$this->session->data['import_data']['new_product_limit'] = false;
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$json = array();
//			if (!$this->request->post['cell_separator']){
//				$json['errors'][] = $this->language->get('ms_import_text_error_noset_rows_separator');
//			}
//			if (!$this->request->post['cell_container']){
//				$json['errors'][] = $this->language->get('ms_import_text_error_noset_cells_separator');
//			}
//
//			$this->request->post['cell_separator'] = str_replace ('&quot;', '"', $this->request->post['cell_separator']);
//			$this->request->post['cell_container'] = str_replace ('&quot;', '"', $this->request->post['cell_container']);
			if (!isset($this->request->post['attachment_code']) OR !$this->request->post['attachment_code']){
				if (!isset($this->session->data['import_data']['attachment_code'])){
					$json['errors']['attachment_code'] = $this->language->get('ms_import_text_error_file');
				}
			}else if(!$json){
				$this->session->data['import_data']['attachment_code'] = $this->request->post['attachment_code'];
//                $upload_info = $this->model_tool_upload->getUploadByCode($this->request->post['attachment_code']);
//                $field_captions = MsLoader::getInstance()->MsImportExportFile->getFieldCaption(DIR_UPLOAD.$upload_info['filename'],$this->request->post);
//                //TODO
//                if(count($field_captions)<3){
//                    $json['errors']['parsing_columns'] = $this->language->get('ms_import_text_error_parsing_columns');
//                }else{
////                    $this->session->data['import_data']['cell_separator'] = $this->request->post['cell_separator'];
////                    $this->session->data['import_data']['cell_container'] = $this->request->post['cell_container'];
//                }
			}
			if (!isset($this->request->post['file_encoding']) OR !$this->request->post['file_encoding']){
				if (!isset($this->session->data['import_data']['file_encoding'])){
					$json['errors']['file_encoding'] = $this->language->get('ms_import_text_error_encoding');
				}
			}else{
				//$this->session->data['import_data']['file_encoding'] = $this->request->post['file_encoding'];
			}

//            $this->session->data['import_data']['start_row'] = $this->request->post['start_row'];
//            $this->session->data['import_data']['finish_row'] = $this->request->post['finish_row'];

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}
		if (isset($this->session->data['import_data']['attachment_code'])){
			$upload_info = $this->model_tool_upload->getUploadByCode($this->session->data['import_data']['attachment_code']);
		}else{
			$upload_info = false;
		}
		if ($upload_info){
			$this->data['filename'] = $upload_info['name'];
			$this->data['attachment_code'] = $upload_info['code'];
		}else{
			$this->data['filename'] = '';
			$this->data['attachment_code'] = '';
		}
		if (isset($this->session->data['import_data']['file_encoding'])){
            $this->data['file_encoding'] = $this->session->data['import_data']['file_encoding'];
        }else{
            $this->data['file_encoding'] = 0;
        }
		if (isset($this->session->data['import_data']['start_row'])){
            $this->data['start_row'] = $this->session->data['import_data']['start_row'];
        }else{
            $this->data['start_row'] = 2;
        }
		if (isset($this->session->data['import_data']['finish_row'])){
            $this->data['finish_row'] = $this->session->data['import_data']['finish_row'];
        }else{
            $this->data['finish_row'] = '';
        }

		if (isset($this->session->data['import_data']['cell_separator'])){
            $this->data['cell_separator'] = htmlspecialchars($this->session->data['import_data']['cell_separator']);
        }else{
            $this->data['cell_separator'] = htmlspecialchars('"');
        }
		if (isset($this->session->data['import_data']['cell_container'])){
            $this->data['cell_container'] = htmlspecialchars($this->session->data['import_data']['cell_container']);
        }else{
            $this->data['cell_container'] = ';';
        }
		$this->data['example_url'] = ($this->config->get('config_secure') ? HTTPS_SERVER : HTTP_SERVER) . 'demo-import.csv';
		$this->data['file_encodings'] = $this->file_encodings;
		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('multiseller/import/step2');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function step3() {
		if (!$this->MsLoader->MsSeller->isSeller()) {
			$this->response->redirect($this->url->link('seller/account-profile', '', 'SSL'));
		}
		if (!$this->config->get('msconf_import_enable')) {
			$this->response->redirect($this->url->link('seller/account-product', '', 'SSL'));
		}
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$mapping_data = json_decode(html_entity_decode($this->request->post['mapping_data']));
			$this->session->data['import_data']['mapping'] = array();
			//get requared fields
			$requared_fields = array();
			foreach ($this->oc_field_types[$this->session->data['import_data']['import_type']] as $type_key=>$type){
				if (isset($type['requared']) AND $type['requared']){
					$requared_fields[$type_key] = $type['oc_sql_name'];
				}
			}
			foreach ($mapping_data as $data){
				$data=explode('-',$data);
				if (count($data) == 2){
					//$data[0] - csv column number, $data[1] - oc_field number
					$this->session->data['import_data']['mapping'][$data[0]] = $data[1];
					$current_import_data = $this->oc_field_types[$this->session->data['import_data']['import_type']][$data[1]];
					if(in_array($current_import_data['oc_sql_name'], $requared_fields)){
						unset($requared_fields[$data[1]]);
					}
				}
			}
			$json = array();
			foreach($requared_fields as $requared_field){
				$json['errors'][] = $this->language->get('ms_import_text_error_noset_'.$requared_field);
			}
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}

		$upload_info = $this->model_tool_upload->getUploadByCode($this->session->data['import_data']['attachment_code']);
		$this->data['field_captions'] = MsLoader::getInstance()->MsImportExportFile->getFieldCaption(DIR_UPLOAD.$upload_info['filename'],$this->session->data['import_data']);

		$samples = MsLoader::getInstance()->MsImportExportFile->getSamplesData(DIR_UPLOAD.$upload_info['filename'],$this->session->data['import_data'], false);
		$this->data['simples_fields'] = array_shift($samples);
		//if have mapping
        if (isset($this->session->data['import_data']['mapping'])){
			//if mapping from session
            if(is_array($this->session->data['import_data']['mapping'])){
                $this->data['mapping'] = $this->session->data['import_data']['mapping'];
				//if mapping from config
            }else{
                $this->data['mapping'] = unserialize($this->session->data['import_data']['mapping']);
            }
        }else{
            $this->data['mapping'] = array();
		}

		$this->data['oc_field_types'] = $this->oc_field_types[$this->session->data['import_data']['import_type']];
		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('multiseller/import/step3');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

	public function step4() {
		if (!$this->MsLoader->MsSeller->isSeller()) {
			$this->response->redirect($this->url->link('seller/account-profile', '', 'SSL'));
		}
		if (!$this->config->get('msconf_import_enable')) {
			$this->response->redirect($this->url->link('seller/account-product', '', 'SSL'));
		}
		//TODO
        $this->session->data['import_data']['default_quantity'] = 1000;
        $this->session->data['import_data']['default_product_status'] = 0;
        $this->session->data['import_data']['delimiter_category'] = '|';
        $this->session->data['import_data']['fill_category'] = 1;
        $this->session->data['import_data']['stock_status_id'] = 7;
        $this->session->data['import_data']['images_path'] = 'import/';
		$this->session->data['import_data']['product_approved'] = 1;
		$this->data['import_data'] = $this->session->data['import_data'];

        $this->data['type'] = $this->data['types'][$this->session->data['import_data']['import_type']]['id'];
		$upload_info = $this->model_tool_upload->getUploadByCode($this->session->data['import_data']['attachment_code']);
        if ($upload_info){
			$this->data['filename'] = $upload_info['name'];
		}else{
			$this->data['filename'] = $this->data['ms_imports_text_not_specified'];
		}

		if (isset($this->file_encodings[$this->session->data['import_data']['file_encoding']])){
			$this->data['file_encoding'] = $this->file_encodings[$this->session->data['import_data']['file_encoding']];
		}else{
			$this->data['file_encoding'] = $this->data['ms_imports_text_not_specified'];
		}

        if ($this->session->data['import_data']['default_product_status']){
            $this->data['import_data']['default_product_status'] = $this->data['ms_imports_text_enabled'];
        }else{
            $this->data['import_data']['default_product_status'] = $this->data['ms_imports_text_disabled'];
        }

        if ($this->session->data['import_data']['fill_category']){
            $this->data['import_data']['fill_category'] = $this->data['ms_imports_text_yes'];
        }else{
            $this->data['import_data']['fill_category'] = $this->data['ms_imports_text_no'];
        }

        $stock_status = MsLoader::getInstance()->MsImportExportData->getStockStatus($this->session->data['import_data']['stock_status_id']);
        if (isset($stock_status['name'])){
            $this->data['import_data']['stock_status'] = $stock_status['name'];
        }else{
            $this->data['import_data']['stock_status'] = '';
        }

		$this->data['fields'] = array();
		foreach ($this->session->data['import_data']['mapping'] as $field_caption=>$oc_field_id){
			if (isset($this->oc_field_types[$this->data['type']][$oc_field_id]['oc_field_name'])){
				$this->data['fields'][$field_caption] = $this->oc_field_types[$this->data['type']][$oc_field_id];
                $this->data['fields'][$field_caption]['oc_field_id'] = $oc_field_id;
			}
		}

		$this->data['samples'] = MsLoader::getInstance()->MsImportExportFile->getSamplesData(DIR_UPLOAD.$upload_info['filename'],$this->session->data['import_data'],true);

		$i=0;
		foreach ($this->data['fields'] as $field_key=>$field_data){
			$this->data['fields'][$field_key]['sample_data'] = $this->data['samples'][$this->session->data['import_data']['start_row']][$i];
			$i++;
		}

		$this->data['import'] = $this->url->link('seller/account-import/import');
		$this->data['config_id'] = $this->session->data['import_data']['config_id'];

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('multiseller/import/step4');
		$this->response->setOutput($this->load->view($template, array_merge($this->data, $children)));
	}

//	public function jxUploadFile() {
//		$this->load->language('tool/upload');
//		$this->load->language('multiseller/multiseller');
//		$json['errors'] = array();
//		if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
//			// Sanitize the filename
//			$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));
//
//			// Validate the filename length
//			if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
//				$json['errors'] = $this->language->get('error_filename');
//			}
//
//			// Validate file extension
//			$json['errors'] = $this->MsLoader->MsFile->checkFile($this->request->files['file'], 'csv');
//
//			// Check to see if any PHP files are trying to be uploaded
//			$content = file_get_contents($this->request->files['file']['tmp_name']);
//
//			if (preg_match('/\<\?php/i', $content)) {
//				$json['errors'] = $this->language->get('error_filetype');
//			}
//
//			// Return any upload error
//			if ($this->request->files['file']['errors'] != UPLOAD_ERR_OK) {
//				$json['errors'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
//			}
//		} else {
//			$json['errors'] = $this->language->get('error_upload');
//		}
//
//		if (empty($json['errors'])) {
//			unset($json['errors']);
//
//			// Hide the uploaded file name so people can not link to it directly.
//			$file = $filename . '.' . token(32);
//
//			move_uploaded_file($this->request->files['file']['tmp_name'], DIR_UPLOAD . $file);
//
//			$this->load->model('tool/upload');
//			$json['code'] = $this->model_tool_upload->addUpload($filename, $file);
//			$json['filename'] = $filename;
//			$json['success'] = $this->language->get('text_upload');
//		}
//
//		return $this->response->setOutput(json_encode($json));
//	}

	public function jxUploadFile() {
		$json = array();
		$file = array();

		$json['errors'] = $this->MsLoader->MsFile->checkPostMax($_POST, $_FILES);

		if ($json['errors']) {
			return $this->response->setOutput(json_encode($json));
		}

		// allow a maximum of N files
		$msconf_downloads_limits = $this->config->get('msconf_downloads_limits');
		foreach ($_FILES as $file) {
			if ($msconf_downloads_limits[1] > 0 && $this->request->post['fileCount'] > $msconf_downloads_limits[1]) {
				$json['errors'][] = sprintf($this->language->get('ms_error_product_download_maximum'),$msconf_downloads_limits[1]);
				$json['cancel'] = 1;
				$this->response->setOutput(json_encode($json));
				return;
			} else {
				$errors = $this->MsLoader->MsFile->checkImportFile($file);

				if ($errors) {
					$json['errors'] = array_merge($json['errors'], $errors);
				} else {
					$fileData = $this->MsLoader->MsFile->uploadImportFile($file);
					$this->load->model('tool/upload');
					$fileData['code'] = $this->model_tool_upload->addUpload($fileData['fileMask'], $fileData['fileName']);
					$json['files'][] = array (
						'code' => $fileData['code'],
						'fileName' => $fileData['fileName'],
						'fileMask' => $fileData['fileMask'],
						'filePages' => isset($pages) ? $pages : ''
					);
				}
			}
		}

		return $this->response->setOutput(json_encode($json));
	}


	public function jxSaveConfig() {
		$json = array();
		if (!isset($this->request->post['import_type']) OR !$this->request->post['import_type']){
			$json['error'] = $this->language->get('ms_import_text_error_type_not_set');
		}
		if (!isset($this->request->post['attachment_code']) OR !$this->request->post['import_type']){
			$json['error'] = $this->language->get('ms_import_text_error_file_not_set');
		}
		if (!$json) {
			if (isset($this->request->get['config_id'])){
                //allow update only seller config
                $config = MsLoader::getInstance()->MsImportExportData->getImportConfigById($this->request->get['config_id']);
			    if ((int)$config['customer_id'] == (int)$this->customer->getId()){
                    MsLoader::getInstance()->MsImportExportData->updateImportConfig($this->request->get['config_id'],$this->request->post);
                    $json['success'] = $this->language->get('ms_imports_text_import_update_config_success');
                }else{
                    $json['error'] = $this->language->get('ms_import_text_error_edit_only_you_config');
                }
			}else{
			    $this->session->data['import_data']['config_id'] = MsLoader::getInstance()->MsImportExportData->addImportConfig($this->request->post);
				$json['success'] = $this->language->get('ms_imports_text_import_save_config_success');
			}
		}
		return $this->response->setOutput(json_encode($json));
	}

}
