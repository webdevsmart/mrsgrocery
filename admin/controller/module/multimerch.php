<?php

if (!class_exists('ControllerMultimerchBase')) {
    die('
<h3>MultiMerch Installation Error - ControllerMultimerchBase class not found</h3>
<pre>This usually means vQmod is missing or broken. Please make sure that:

1. You have installed MultiMerch Core
2. You have installed the latest version of vQmod available at <a href="http://vqmod.com/">http://vqmod.com/</a>
3. You have run vQmod installation script at <a target="_blank" href="'.HTTP_CATALOG.'vqmod/install/">'.HTTP_CATALOG.'vqmod/install/</a> successfully (see <a target="_blank" href="https://github.com/vqmod/vqmod/wiki/Installing-vQmod-on-OpenCart">Installing vQmod on OpenCart</a> for more information)
4. Your vqmod/ and vqmod/vqcache/ folders are server-writable. Contact your hosting provider for more information
5. You have copied all MultiMerch files and folders from the upload/ folder to your OpenCart root
</pre>
    ');
}

class ControllerModuleMultimerch extends ControllerMultimerchBase {
	private $_controllers = array(
		"multimerch/attribute",
		"multimerch/badge",
		"multimerch/base",
		"multimerch/category",
		"multimerch/conversation",
		"multimerch/custom-field",
		"multimerch/coupon",
		"multimerch/dashboard",
		"multimerch/debug",
		"multimerch/event",
		"multimerch/import",
		"multimerch/option",
		"multimerch/order",
		"multimerch/payment",
		"multimerch/payment-gateway",
		"multimerch/payment-request",
		"multimerch/payout",
		"multimerch/product",
		"multimerch/question",

		"multimerch/report",
		"multimerch/report/finances-payment",
		"multimerch/report/finances-payout",
		"multimerch/report/finances-seller",
		"multimerch/report/finances-transaction",
		"multimerch/report/sales",
		"multimerch/report/sales-customer",
		"multimerch/report/sales-day",
		"multimerch/report/sales-month",
		"multimerch/report/sales-product",
		"multimerch/report/sales-seller",

		"multimerch/review",
		"multimerch/seller",
		"multimerch/seller-group",
		"multimerch/settings",
		"multimerch/shipping-method",
		"multimerch/social_link",
		"multimerch/suborder-status",
		"multimerch/transaction",

		"total/mm_shipping_total",
		"total/ms_coupon"
	);
	
	private $settings = array(
		"mxtconf_installed" => 1,

		/* license */
		"msconf_license_key" => '',
		"msconf_license_activated" => 0,

		/* badges */
		"msconf_badge_enabled" => 0,
		"msconf_badge_width" => 50,
		"msconf_badge_height" => 50,

		/* social links */
		"msconf_sl_status" => 0,
		"msconf_sl_icon_width" => 30,
		"msconf_sl_icon_height" => 30,

		/* messaging */
		"mmess_conf_enable" => 1,

		/* disqus comments */
		'mxtconf_disqus_enable' => 0,
		'mxtconf_disqus_shortname' => '',

		/* core settings */
		"msconf_seller_validation" => MsSeller::MS_SELLER_VALIDATION_NONE,

		"msconf_nickname_rules" => 0, // 0 - alnum, 1 - latin extended, 2 - utf

		"msconf_change_group" => 0,

		"msconf_credit_order_statuses" => array(
			'oc' => array(5),
			'ms' => array()
		),
		"msconf_debit_order_statuses" => array(
			'oc' => array(8),
			'ms' => array()
		),
		"msconf_paypal_sandbox" => 1,
		"msconf_paypal_address" => "",

		"msconf_allow_withdrawal_requests" => 1,

		"msconf_allow_free_products" => 1,
		"msconf_allow_digital_products" => 0,

		"msconf_allow_seller_categories" => 0,
		"msconf_allow_multiple_categories" => 0,

		"msconf_allow_seller_attributes" => 0,
		"msconf_allow_seller_options" => 0,
		"msconf_allowed_seller_option_types" => array(
			'choose' => array('select', 'radio', 'checkbox'),
			'input' => array('text', 'textarea'),
			'file' => array('file'),
			'date' => array('date', 'time', 'datetime')
		),

		"msconf_product_included_fields" => array('price', 'images'),

		"msconf_images_limits" => array(0,0),
		"msconf_downloads_limits" => array(0,0),

		"msconf_enable_shipping" => 0, // 0 - no, 1 - yes, 2 - seller select

		"msconf_allow_relisting" => 0,

		"msconf_product_image_path" => 'sellers/',
		"msconf_temp_image_path" => 'tmp/',
		"msconf_temp_download_path" => 'tmp/',
		"msconf_default_seller_group_id" => 1,

		/* Reviews */
		"msconf_reviews_enable" => 0,

		/* Import */
		"msconf_import_enable" => 0,
		"msconf_import_category_type" => 0, // 0 - All levels in a single cell, 1 - Different levels in different cells

		/* Shipping */
		"msconf_shipping_type" => 0, // 0 - disable, 1 - default store shipping, 2 - MM Vendor shipping
		"msconf_vendor_shipping_type" => 3, // 1 - Combined, 2 - Per-Product, 3 - Both
		"mm_shipping_total_status" => 1,
		"mm_shipping_total_sort_order" => 1,

		/* Fee settings */
		"msconf_fee_priority" => 2,

		/* Logging */
		"msconf_logging_level" => \MultiMerch\Logger\Logger::LEVEL_ERROR,
		"msconf_logging_filename" => 'ms_logging.log',

		/* Questions */
		"msconf_allow_questions" => 0,

		/* SEO */
		'msconf_config_seo_url_enable' => 0,
		'msconf_store_slug' => 'store',
		"msconf_sellers_slug" => 'sellers',
		"msconf_products_slug" => 'products',

		/* Coupons */
		"msconf_allow_seller_coupons" => 1,
		"ms_coupon_status" => 1,
		"ms_coupon_sort_order" => 4,

		/* Google map */
		"msconf_google_api_key" => '',


		// deprecated
		"msconf_enable_quantities" => 1, // 0 - no, 1 - yes, 2 - shipping dependent
		"msconf_enable_rte" => 1,
		"msconf_minimum_withdrawal_amount" => "50",
		"msconf_withdrawal_waiting_period" => 0,

		"msconf_notification_email" => "",
		"msconf_allow_inactive_seller_products" => 0,
		"msconf_disable_product_after_quantity_depleted" => 0,
		"msconf_graphical_sellermenu" => 1,
		"msconf_enable_seller_banner" => 1,

		"msconf_allow_specials" => 1,
		"msconf_allow_discounts" => 1,

		"msconf_attribute_display" => 1, // 0 - MM, 1 - OC, 2 - both

		"msconf_provide_buyerinfo" => 0, // 0 - no, 1 - yes, 2 - shipping dependent

		"msconf_allow_partial_withdrawal" => 1,

		"msconf_enable_update_seo_urls" => 0,
		"msconf_hide_customer_email" => 1,

		"msconf_enable_private_messaging" => 2, // 0 - no, 2 - yes (email only)

		/* Deprecated for MM 8.7.3 release */
		"msconf_seller_terms_page" => "",
		'mxtconf_ga_seller_enable' => 0,
		"msconf_change_seller_nickname" => 1,
		"msconf_minimum_product_price" => 0,
		"msconf_maximum_product_price" => 0,
		"msconf_allowed_image_types" => 'png,jpg,jpeg',
		"msconf_allowed_download_types" => 'zip,rar,pdf',
		"msconf_rte_whitelist" => "",
		"msconf_msg_allowed_file_types" => 'png,jpg,jpeg,zip,rar,pdf,csv',
		"msconf_seller_avatar_seller_profile_image_width" => 100,
		"msconf_seller_avatar_seller_profile_image_height" => 100,
		"msconf_seller_avatar_seller_list_image_width" => 228,
		"msconf_seller_avatar_seller_list_image_height" => 228,
		"msconf_seller_avatar_product_page_image_width" => 100,
		"msconf_seller_avatar_product_page_image_height" => 100,
		"msconf_seller_avatar_dashboard_image_width" => 100,
		"msconf_seller_avatar_dashboard_image_height" => 100,
		"msconf_preview_seller_avatar_image_width" => 100,
		"msconf_preview_seller_avatar_image_height" => 100,
		"msconf_preview_product_image_width" => 100,
		"msconf_preview_product_image_height" => 100,
		"msconf_product_seller_profile_image_width" => 100,
		"msconf_product_seller_profile_image_height" => 100,
		"msconf_product_seller_products_image_width" => 100,
		"msconf_product_seller_products_image_height" => 100,
		"msconf_product_seller_product_list_seller_area_image_width" => 40,
		"msconf_product_seller_product_list_seller_area_image_height" => 40,
		"msconf_product_seller_banner_width" => 750,
		"msconf_product_seller_banner_height" => 100,
		"msconf_min_uploaded_image_width" => 0,
		"msconf_min_uploaded_image_height" => 0,
		"msconf_max_uploaded_image_width" => 0,
		"msconf_max_uploaded_image_height" => 0
	);

	public function __construct($registry) {
		parent::__construct($registry);
		$this->registry = $registry;
		$this->data = array_merge(
			$this->data,
			$this->load->language('multiseller/multiseller'),
			$this->load->language('multimerch/multimerch')
		);
		$this->load->model("multimerch/install");
		$this->load->model("multimerch/upgrade");
		$this->load->model('setting/setting');
		$this->load->model('extension/extension');
		$this->data['token'] = $this->session->data['token'];

		$this->ms_logger = new \MultiMerch\Logger\Logger();
	}

	private function _editSettings($prefix = '') {
		$set = $this->model_setting_setting->getSetting($prefix);
		$installed_extensions = $this->model_extension_extension->getInstalled('module');

		$extensions_to_be_installed = array();
		foreach ($this->settings as $name=>$value) {
			if (!array_key_exists($name,$set))
				$set[$name] = $value;

			if ((strpos($name,'_module') !== FALSE) && (!in_array(str_replace('_module','',$name),$installed_extensions))) {
				$extensions_to_be_installed[] = str_replace('_module','',$name);
			}
		}

		foreach($set as $s=>$v) {
			if ((strpos($s,'_module') !== FALSE)) {
				if (!isset($this->request->post[$s])) {
					$set[$s] = '';
				} else {
					unset($this->request->post[$s][0]);
					$set[$s] = $this->request->post[$s];
				}
				continue;
			}

			if (isset($this->request->post[$s])) {
				$set[$s] = $this->request->post[$s];
				//Disable OC review if MM review enabled
				if ($s == 'msconf_reviews_enable' AND $this->request->post[$s]){
					$this->model_setting_setting->editSettingValue('config','config_review_status',0);
				}
				$this->data[$s] = $this->request->post[$s];
			} elseif ($this->config->get($s)) {
				$this->data[$s] = $this->config->get($s);
			} else {
				if (isset($this->settings[$s]))
					$this->data[$s] = $this->settings[$s];
			}
		}

		$this->model_setting_setting->editSetting($prefix, $set);

		foreach ($extensions_to_be_installed as $ext) {
			$this->model_extension_extension->install('module',$ext);
		}
	}

	public function install() {
		
		if ($this->cache->get('multimerch_module_is_installed')){
			return;
		}

		$this->validate(__FUNCTION__);

		/** @see \ModelMultimerchInstall::createSchema */
		$this->model_multimerch_install->createSchema();

		/** @see \ModelMultimerchInstall::createData */
		$this->model_multimerch_install->createData();
		$this->model_setting_setting->editSetting('mxtconf', $this->settings);
		$this->model_setting_setting->editSetting('msconf', $this->settings);
		$this->model_setting_setting->editSetting('mmess_conf', $this->settings);
		$this->model_setting_setting->editSetting('mm_shipping_total', $this->settings);
		$this->model_setting_setting->editSetting('ms_coupon', $this->settings);
		$this->model_multimerch_install->createAdditionalSettings();

		$this->load->model('user/user_group');

		foreach ($this->_controllers as $c) {
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', $c);
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', $c);
		}

		$dirs = array(
			DIR_IMAGE . $this->settings['msconf_product_image_path'],
			DIR_IMAGE . $this->settings['msconf_temp_image_path'],
			DIR_DOWNLOAD . $this->settings['msconf_temp_download_path']
		);

		$this->session->data['success'] = $this->language->get('ms_success_installed');
		$this->session->data['error'] = "";

		foreach ($dirs as $dir) {
			if (!file_exists($dir)) {
				if (!mkdir($dir, 0755)) {
					$this->session->data['error'] .= sprintf($this->language->get('ms_error_directory'), $dir);
				}
			} else {
				if (!is_writable($dir)) {
					$this->session->data['error'] .= sprintf($this->language->get('ms_error_directory_notwritable'), $dir);
				} else {
					$this->session->data['error'] .= sprintf($this->language->get('ms_error_directory_exists'), $dir);
				}
			}
		}

		$this->cache->set('multimerch_module_is_installed', true);
	}

	public function uninstall() {
		/** @see \ModelMultimerchInstall::deleteSchema */
		$this->model_multimerch_install->deleteSchema();

		/** @see \ModelMultimerchInstall::deleteData */
		$this->model_multimerch_install->deleteData();

		$this->load->model('user/user_group');

		foreach ($this->_controllers as $c) {
			$this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', $c);
			$this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', $c);
		}

		$this->model_setting_setting->editSetting("mxtconf", array("mxtconf_installed" => 0));

		// Uninstall MsPPAdaptive module
		$this->model_extension_extension->uninstall('payment', 'ms_pp_adaptive');

		// @todo: check if cache deletes
		$this->cache->delete('ms_order_states_info');
		$this->cache->delete('ms_order_state');

		$this->cache->delete('multimerch_module_is_installed');
	}

	public function saveSettings() {
		$json = array();

		if ($this->request->post['msconf_shipping_type'] == 2 AND $this->request->post['msconf_vendor_shipping_type'] == 2){
			$shipping_delivery_times = $this->MsLoader->MsShippingMethod->getShippingDeliveryTimes();
			if (empty($shipping_delivery_times)){
				$json['errors'][] = $this->language->get('ms_settings_error_vendor_shipping_times');
			}
		}

		if ($this->request->post['msconf_sellers_slug'] ==$this->request->post['msconf_products_slug']){
			$json['errors'][] = $this->language->get('ms_settings_error_vendor_duplicate_seo_slug');
		}

		if (!empty($json['errors'])) {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}

		if (!isset($this->request->post['msconf_credit_order_statuses']))
			$this->request->post['msconf_credit_order_statuses'] = array(
				'oc' => array(),
				'ms' => array()
			);

		if (!isset($this->request->post['msconf_debit_order_statuses']))
			$this->request->post['msconf_debit_order_statuses'] = array(
				'oc' => array(),
				'ms' => array()
			);

		if (!isset($this->request->post['msconf_product_options']))
			$this->request->post['msconf_product_options'] = array();

		if (!isset($this->request->post['msconf_product_included_fields']))
			$this->request->post['msconf_product_included_fields'] = array();

		foreach (array('mxtconf', 'mmess_conf', 'msconf') as $prefix) {
			$this->_editSettings($prefix);
		}

		// Install MM Shipping Total module if Vendor shipping type is selected
		if(isset($this->request->post['msconf_shipping_type'])) {
			if ((int)$this->request->post['msconf_shipping_type'] == 2) {
				$this->model_setting_setting->editSetting('mm_shipping_total', array(
					'mm_shipping_total_status' => 1
				));
			} else {
				$this->model_setting_setting->editSetting('mm_shipping_total', array(
					'mm_shipping_total_status' => 0
				));
			}
		}

		if (isset($this->request->post['msconf_allow_seller_coupons'])) {
			if ($this->request->post['msconf_allow_seller_coupons']) {
				$this->model_extension_extension->install('total', 'ms_coupon');
				$this->model_setting_setting->editSetting('ms_coupon', array('ms_coupon_status' => 1, 'ms_coupon_sort_order' => 4));
			} else {
				$this->model_extension_extension->uninstall('total', 'ms_coupon');
			}
		}

		//if isset new api key - update seller geo positions
		if ($this->request->post['msconf_google_api_key'] AND $this->request->post['msconf_google_api_key'] != $this->request->post['msconf_google_api_key_old']) {
			$this->config->set('msconf_google_api_key', $this->request->post['msconf_google_api_key']);
			//TODO check api key
			$this->MsLoader->MsSeller->updateSellerPositions();
		}

		//mm_admin_save_settings_end_hook

		$this->response->setOutput(json_encode($json));
	}

	public function index() {
		$this->validate(__FUNCTION__);

		foreach($this->settings as $s=>$v) {
			$this->data[$s] = $this->config->get($s);
		}

		$this->document->addScript('view/javascript/jquery/jquery-ui/jquery-ui.min.js');
		$this->document->addScript('view/javascript/multimerch/settings.js');

		$this->document->addScript('view/javascript/multimerch/tag-editor/jquery.tag-editor.min.js');
		$this->document->addScript('view/javascript/multimerch/tag-editor/jquery.caret.min.js');
		$this->document->addStyle('view/javascript/multimerch/tag-editor/jquery.tag-editor.css');

		$this->load->model("localisation/order_status");
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$this->load->model("catalog/option");
		$this->data['options'] = $this->model_catalog_option->getOptions();
		$this->load->model("localisation/language");
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		$this->data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL');

		$this->load->model('design/layout');
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		$this->data['currency_code'] = $this->config->get('config_currency');

		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');

		if (isset($this->error['image'])) {
			$this->data['error_image'] = $this->error['image'];
		} else {
			$this->data['error_image'] = array();
		}

		$this->load->model('catalog/information');
		$this->data['informations'] = $this->model_catalog_information->getInformations();
		$this->data['product_included_fields'] = array(
			'price' => $this->language->get('ms_catalog_products_field_price'),
			'quantity' => $this->language->get('ms_catalog_products_field_quantity'),
			'marketplace_category' => $this->language->get('ms_catalog_products_field_marketplace_category'),
			'tags' => $this->language->get('ms_catalog_products_field_tags'),
			'attributes' => $this->language->get('ms_catalog_products_field_attributes'),
			'options' => $this->language->get('ms_catalog_products_field_options'),
			'special_prices' => $this->language->get('ms_catalog_products_field_special_prices'),
			'quantity_discounts' => $this->language->get('ms_catalog_products_field_quantity_discounts'),
			'images' => $this->language->get('ms_catalog_products_field_images'),
			'files' => $this->language->get('ms_catalog_products_field_files'),
			'model' => $this->language->get('ms_catalog_products_field_model'),
			'sku' => $this->language->get('ms_catalog_products_field_sku'),
			'upc' => $this->language->get('ms_catalog_products_field_upc'),
			'ean' => $this->language->get('ms_catalog_products_field_ean'),
			'jan' => $this->language->get('ms_catalog_products_field_jan'),
			'isbn' => $this->language->get('ms_catalog_products_field_isbn'),
			'mpn' => $this->language->get('ms_catalog_products_field_mpn'),
			'manufacturer' => $this->language->get('ms_catalog_products_field_manufacturer'),
			'dateAvailable' => $this->language->get('ms_catalog_products_field_date_available'),
			'taxClass' => $this->language->get('ms_catalog_products_field_tax_class'),
			'subtract' => $this->language->get('ms_catalog_products_field_subtract'),
			'stores' => $this->language->get('ms_catalog_products_field_stores'),
			'stockStatus' => $this->language->get('ms_catalog_products_field_stock_status'),
			'metaDescription' => $this->language->get('ms_catalog_products_field_meta_description'),
			'metaKeywords' => $this->language->get('ms_catalog_products_field_meta_keyword'),
			'metaTitle' => $this->language->get('ms_catalog_products_field_meta_title'),
			'seoURL' => $this->language->get('ms_catalog_products_field_seo_url'),
			'filters' => $this->language->get('ms_catalog_products_filters'),
			'minOrderQty' => $this->language->get('ms_catalog_products_min_order_qty'),
			'relatedProducts' => $this->language->get('ms_catalog_products_related_products'),
			'dimensions' => $this->language->get('ms_catalog_products_dimensions'),
			'weight' => $this->language->get('ms_catalog_products_weight')
		);
		ksort($this->data['product_included_fields']);

		$this->data['allowed_seller_option_types'] = array(
			'choose' => array('select', 'radio', 'checkbox'),
			'input' => array('text', 'textarea'),
			'file' => array('file'),
			'date' => array('date', 'time', 'datetime')
		);

		$this->data['shipping_delivery_times'] = $this->MsLoader->MsShippingMethod->getShippingDeliveryTimes();

		$this->data['suborder_statuses'] = $this->MsLoader->MsSuborderStatus->getMsSuborderStatuses(array(
			'language_id' => $this->config->get('config_language_id')
		));

		// OC order states
		$oc_order_states = $this->MsLoader->MsOrderData->getOrderStateData();
		foreach ($oc_order_states as $state_id => $status_ids) {
			foreach ($status_ids as $status_id) {
				$this->data['oc_order_states'][$state_id][] = array(
					'id' => $status_id,
					'name' => $this->MsLoader->MsHelper->getStatusName(array('order_status_id' => $status_id))
				);
			}
		}

		// MS suborder states
		$ms_suborder_states = $this->MsLoader->MsSuborderStatus->getSuborderStateData();
		foreach ($ms_suborder_states as $state_id => $status_ids) {
			foreach ($status_ids as $status_id) {
				$this->data['ms_suborder_states'][$state_id][] = array(
					'id' => $status_id,
					'name' => $this->MsLoader->MsSuborderStatus->getSubStatusName(array('order_status_id' => $status_id))
				);
			}
		}

		$this->document->setTitle($this->language->get('ms_settings_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('module/multimerch', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_settings_breadcrumbs'),
				'href' => $this->url->link('module/multimerch', '', 'SSL'),
			)
		));

		if(isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

		if(isset($this->session->data['error_warning'])) {
			$this->data['error_warning'] = $this->session->data['error_warning'];
			unset($this->session->data['error_warning']);
		}

		if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/.htaccess')){
			if (file_exists($_SERVER['DOCUMENT_ROOT'].'/.htaccess.txt')){
				$this->data['error_htaccess'] = $this->language->get('ms_error_htaccess');
			}else{
				$this->data['error_htaccess'] = $this->language->get('ms_error_htaccess_txt');
			}
		}

		if ($this->data['msconf_shipping_type'] == 2 AND $this->data['msconf_vendor_shipping_type'] == 2){
			$shipping_methods = $this->MsLoader->MsShippingMethod->getShippingMethods();
			if (empty($shipping_methods)){
				$this->data['error_vendor_shipping_methods'] = sprintf($this->language->get('ms_settings_error_vendor_shipping_methods'), $this->url->link('multimerch/shipping-method', 'token=' . $this->session->data['token'], 'SSL'));
			}
		}

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multimerch/settings.tpl', $this->data));
	}

	public function upgradeDb() {
		if ($this->MsLoader->MsHelper->isInstalled() && !$this->model_multimerch_upgrade->isDbLatest()) {
			$upgrade_info = $this->model_multimerch_upgrade->upgradeDb();

			if(!empty($upgrade_info)) {
				$changelog = "";
				foreach ($upgrade_info as $app_version => $messages) {
					$changelog .= "New in MultiMerch " . $app_version . ":<BR>";

					if (count($messages) == 1) {
						$changelog .= "<p style='padding-left: 25px;'>" . array_shift($messages) . "</p>";
					} else {
						$changelog .= "<ol>";
						foreach ($messages as $message) {
							$changelog .= "<li>" . $message . "</li>";
						}
						$changelog .= "</ol>";
					}

					$changelog .= "<BR>";
				}

				$changelog .= "<button id='ms-changelog-is-read' class='btn btn-primary'>Thanks, got it!</button>";

				if($changelog) $this->session->data['ms_changelog'] = $changelog;
			}

			$this->session->data['ms_db_latest'] = $this->language->get('ms_db_success');
		} else {
			$this->session->data['ms_db_latest'] = $this->language->get('ms_db_latest');
		}

		$this->response->redirect($this->url->link('module/multimerch', 'token=' . $this->session->data['token'], 'SSL'));
	}

	public function jxConfirmChangelogIsRead() {
		$json = array();

		if(isset($this->session->data['ms_changelog']))
			unset($this->session->data['ms_changelog']);

		$json['success'] = $this->language->get('ms_success');

		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Sends API request to activate MultiMerch.
	 *
	 * Returns 'error' if license key is invalid or not specified.
	 * Returns 'error' if license is missing, not valid, expired, revoked in user's Personal Account at http://multimerch.com.
	 * Returns 'success' if MultiMerch is successfully activated.
	 *
	 * @return	string			JSON with possible messages: 'error', 'success'.
	 */
	public function jxActivate() {
		$json = array();

		if(empty($this->request->post['license_key'])) {
			$json['error'] = $this->language->get('ms_license_error_no_key');
		}

		if(empty($json)) {
			$this->MsLoader->MsHelper->createOcSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_license_key',
				'value' => $this->request->post['license_key']
			));

			$request = 'https://multimerch.com/?edd_action=activate_license&license=' . $this->request->post['license_key'] . '&url=' . preg_replace('/^(http|https):\/\//', '', $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG);

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $request);
			$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL;
			curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$response['data'] = curl_exec($curl);
			$response['code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);

			if ($response['code'] == 200) {
				$data = json_decode($response['data']);

				if(property_exists($data, 'success') && $data->success) {
					if(property_exists($data, 'license') && $data->license == 'valid') {
						$this->MsLoader->MsHelper->createOcSetting(array(
							'code' => 'msconf',
							'key' => 'msconf_license_activated',
							'value' => 1
						));

						$json['success'] = $this->language->get('ms_license_success_activated');
					} else {
						$json['error'] = sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_invalid'));
					}
				} else {
					if(property_exists($data, 'error')) {
						switch($data->error) {
							case 'missing':
								$this->ms_logger->error(sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_missing')));
								$json['error'] = sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_generic'));
								break;

							case 'license_not_activable':
								$this->ms_logger->error(sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_not_activable')));
								$json['error'] = sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_generic'));
								break;

							case 'revoked':
								$this->ms_logger->error(sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_revoked')));
								$json['error'] = sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_generic'));
								break;

							case 'no_activations_left':
								$this->ms_logger->error(sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_no_activations_left')));
								$json['error'] = sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_generic'));
								break;

							case 'expired':
								$this->ms_logger->error(sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_expired')));
								$json['error'] = sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_expired'));
								break;

							case 'key_mismatch':
								$this->ms_logger->error(sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_key_mismatch')));
								$json['error'] = sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_generic'));
								break;

							case 'invalid_item_id':
								$this->ms_logger->error(sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_item_id')));
								$json['error'] = sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_generic'));
								break;

							case 'item_name_mismatch':
								$this->ms_logger->error(sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_item_name')));
								$json['error'] = sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_generic'));
								break;

							case 'no_site_specified':
							case 'url_not_match':
								$this->ms_logger->error(sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_no_site')));
								$json['error'] = sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_generic'));
								break;

							default:
								$this->ms_logger->error(sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_unrecognized')));
								$json['error'] = sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_generic'));
								break;
						}

						// Also reset MultiMerch activation status
						$this->MsLoader->MsHelper->createOcSetting(array(
							'code' => 'msconf',
							'key' => 'msconf_license_activated',
							'value' => '0'
						));
					}
				}
			} elseif ((int)$response['code'] === 0) {
				$this->ms_logger->error("Error: Unable to connect to licensing server!");
				$json['error'] = sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_connection'));
			} else {
				$this->ms_logger->error("Error: API error occurred - {$response['code']}!");
				$json['error'] = sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_generic'));
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Revokes MultiMerch license.
	 *
	 * Called if license has not passed validation during updates check.
	 * Returns 'success' if MultiMerch deactivated.
	 *
	 * @return	string			JSON with possible messages: 'success'.
	 */
	public function jxResetLicense() {
		$json = array();

		// reset msconf_license_activated
		$this->MsLoader->MsHelper->createOcSetting(array(
			'code' => 'msconf',
			'key' => 'msconf_license_activated',
			'value' => '0'
		));

		$json['success'] = $this->language->get('ms_success');

		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Sends API request to get information about updates.
	 *
	 * Checks whether there are updates for current MultiMerch setup.
	 * Returns 'error' if no license key specified.
	 * Returns 'error_license' if MultiMerch is not activated or license key is invalid etc.
	 * Returns 'success' if there are either no updates or updates are available (then 'changelog' is added).
	 *
	 * @return	string			JSON with possible messages: 'error', 'error_license', 'success' + 'changelog'.
	 */
	public function jxCheckUpdates() {
		$json = array();

		$request = 'https://multimerch.com/?edd_action=get_version&license=' . $this->config->get('msconf_license_key') . '&url=' . preg_replace('/^(http|https):\/\//', '', $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $request);
		$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL;
		curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response['data'] = curl_exec($curl);
		$response['code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($response['code'] == 200) {
			$data = json_decode($response['data']);

			if(property_exists($data, 'stable_version')) {
				if($data->stable_version) {
					// MM version on current setup
					$app_version = $this->MsLoader->appVer;

					if (version_compare($data->stable_version, $app_version, '>')) {
						$json['success'] = sprintf($this->language->get('ms_update_success_available_update'), $app_version, $data->stable_version);

						if (property_exists($data, 'sections')) {
							$sections = unserialize($data->sections);
							if ($sections['changelog']) $json['changelog'] = $sections['changelog'];
						}
					} elseif (version_compare($data->stable_version, $app_version, '<=')) {
						$json['success'] = sprintf($this->language->get('ms_update_success_no_updates'), $app_version);
					}
				} else {
					$this->ms_logger->error(sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_invalid')));
					$json['error_license'] = sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_generic'));
				}
			} else {
				if(property_exists($data, 'error_code')) {
					switch($data->error_code) {
						case 'no_license_specified':
							$this->ms_logger->error(sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_invalid')));
							$json['error_license'] = $this->language->get('ms_api_error_license_generic');
							break;

						case 'license_expired':
							$this->ms_logger->error(sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_expired')));
							$json['error_license'] = $this->language->get('ms_api_error_license_expired');
							break;

						default:
							$this->ms_logger->error(sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_incorrect_response')));
							$json['error'] = $this->language->get('ms_api_error_license_generic');
							break;
					}
				} else {
					$json['error'] = $this->language->get('ms_api_error_incorrect_response');
				}
			}
		} else {
			$this->ms_logger->error(sprintf($this->language->get('ms_api_error_request'), $response['code']));
			$json['error'] = sprintf($this->language->get('ms_api_error'), $this->language->get('ms_api_error_license_generic'));
		}

		$this->response->setOutput(json_encode($json));
	}
}