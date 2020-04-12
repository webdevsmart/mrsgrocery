<?php
class ModelMultimerchUpgrade extends Model {
	public function __construct($registry) {
		parent::__construct($registry);
		$this->load->model('localisation/language');
		$this->load->language('multiseller/multiseller');
	}

	public function getDbVersion() {
		$res = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "ms_db_schema'");
		if (!$res->num_rows) return '0.0.0.0';
		
		$res = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_db_schema` ORDER BY schema_change_id DESC LIMIT 1");

		if ($res->num_rows)
			return $res->row['major'] . '.' . $res->row['minor'] . '.' . $res->row['build'] . '.' . $res->row['revision'];
		else
			return '0.0.0.0';
	}
	
	public function isDbLatest() {
		return version_compare($this->MsLoader->dbVer, $this->getDbVersion()) > 0 ? false : true;
	}

	public function isFilesLatest() {
		return version_compare($this->MsLoader->dbVer, $this->getDbVersion()) < 0 ? false : true;
	}

	private function _createSchemaEntry($version) {
		$schema = explode(".", $version);
		$this->db->query("INSERT INTO `" . DB_PREFIX . "ms_db_schema` (major, minor, build, revision, date_applied) VALUES({$schema[0]},{$schema[1]},{$schema[2]},{$schema[3]}, NOW())");
	}
	
	public function upgradeDb() {
		/**	@var	array	$upgrade_info	Array of messages after certain MultiMerch update.
		 *
		 * Its structure:
		 * array(
		 * 	'app_version' => array('message 1', 'message 2', ...)
		 * )
		 * */
		$upgrade_info = array();

		$version = $this->getDbVersion();

		if (version_compare($version, '1.0.0.0') < 0) {
			$this->db->query("
			CREATE TABLE `" . DB_PREFIX . "ms_db_schema` (
				`schema_change_id` int(11) NOT NULL AUTO_INCREMENT,
				`major` TINYINT NOT NULL,
				`minor` TINYINT NOT NULL,
				`build` TINYINT NOT NULL,
				`revision` SMALLINT NOT NULL,
				`date_applied` DATETIME NOT NULL,
			PRIMARY KEY (`schema_change_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_suborder` (
			`suborder_id` int(11) NOT NULL AUTO_INCREMENT,
			`order_id` int(11) NOT NULL,
			`seller_id` int(11) NOT NULL,
			`order_status_id` int(11) NOT NULL,
			PRIMARY KEY (`suborder_id`)
			) DEFAULT CHARSET=utf8");

			$this->_createSchemaEntry('1.0.0.0');
		}

		if (version_compare($version, '1.0.1.0') < 0) {
			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ms_seller` ADD (
				`banner` VARCHAR(255) DEFAULT NULL)");

			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multiseller/addon');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multiseller/addon');

			$this->_createSchemaEntry('1.0.1.0');
		}

		if (version_compare($version, '1.0.2.0') < 0) {
			$this->db->query("
				CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_suborder_history` (
				`suborder_history_id` int(5) NOT NULL AUTO_INCREMENT,
				`suborder_id` int(5) NOT NULL,
				`order_status_id` int(5) NOT NULL,
				`comment` text NOT NULL DEFAULT '',
				`date_added` datetime NOT NULL,
				PRIMARY KEY (`suborder_history_id`)
				) DEFAULT CHARSET=utf8");

			$this->_createSchemaEntry('1.0.2.0');
		}

		if (version_compare($version, '1.0.2.1') < 0) {
			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multiseller/debug');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multiseller/debug');

			$this->_createSchemaEntry('1.0.2.1');
		}

		if (version_compare($version, '1.0.2.2') < 0) {
			$this->db->query("
				CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_setting` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`seller_id` int(11) unsigned DEFAULT NULL,
				`seller_group_id` int(11) unsigned DEFAULT NULL,
				`name` varchar(50) DEFAULT NULL,
				`value` varchar(250) DEFAULT NULL,
				`is_encoded` smallint(1) unsigned DEFAULT NULL,
				PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8;");

			$this->_createSchemaEntry('1.0.2.2');
		}

		if (version_compare($version, '1.0.3.1') < 0) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "ms_order_product_data` ADD `order_product_id` int(11) DEFAULT NULL AFTER `product_id`");

			$this->db->query("ALTER TABLE `" . DB_PREFIX . "ms_balance` ADD `order_product_id` int(11) DEFAULT NULL AFTER `product_id`");

			$this->_createSchemaEntry('1.0.3.1');
		}

		if (version_compare($version, '1.0.3.2') < 0) {
			$this->db->query("
				CREATE UNIQUE INDEX slr_id_name
				ON " . DB_PREFIX . "ms_setting (seller_id, name)");


			//replace data from ms_seller to ms_setting
			$getDataQuery = "SELECT seller_id, company, website FROM " . DB_PREFIX . "ms_seller WHERE 1 GROUP BY seller_id";
			$seller_data = $this->db->query($getDataQuery)->rows;
			foreach ($seller_data as $row) {
				$company = $row['company'];
				$website = $row['website'];
				$seller_id = $row['seller_id'];
//                $seller_group = $this->MsLoader->MsSellerGroup->getSellerGroupBySellerId($seller_id);

				$insertDataQuery =
					"INSERT INTO " . DB_PREFIX . "ms_setting
					SET seller_id = " . (int)$seller_id . ", name = 'slr_company', value = '" . $this->db->escape($company) . "'
					ON DUPLICATE KEY UPDATE
					value = '" . $this->db->escape($company) . "'";
				$this->db->query($insertDataQuery);

				$insertDataQuery =
					"INSERT INTO " . DB_PREFIX . "ms_setting
					SET seller_id = " . (int)$seller_id . ", name = 'slr_website', value = '" . $this->db->escape($website) . "'
					ON DUPLICATE KEY UPDATE
					value = '" . $this->db->escape($website) . "'";
				$this->db->query($insertDataQuery);
			}

			$this->_createSchemaEntry('1.0.3.2');
		}

		if (version_compare($version, '1.0.4.0') < 0) {
			/*ADD `total` decimal(15,4) NOT NULL AFTER `invoice_no`,*/
			$suborderSql = "ALTER TABLE " . DB_PREFIX . "ms_suborder
				ADD `invoice_no` int(11) NOT NULL DEFAULT '0' AFTER `seller_id`,
				ADD `invoice_prefix` varchar(26) NOT NULL DEFAULT '' AFTER `invoice_no`,
				ADD `date_added` datetime NOT NULL AFTER `order_status_id`,
				ADD `date_modified` datetime NOT NULL AFTER `date_added`";
			$this->db->query($suborderSql);

			$orderProductSql = "ALTER TABLE " . DB_PREFIX . "ms_order_product_data ADD `suborder_id` int(11) NOT NULL DEFAULT '0'";
			$this->db->query($orderProductSql);

			$sqlOrders = "SELECT * FROM " . DB_PREFIX . "order WHERE 1";
			$ordersData = $this->db->query($sqlOrders);
			foreach ($ordersData->rows as $row) {
				$order_id = $row['order_id'];
				$dateAdded = $row['date_added'];
				$dateModified = $row['date_modified'];
				$customerSql = "SELECT seller_id FROM " . DB_PREFIX . "ms_suborder WHERE order_id = " . (int)$order_id . " GROUP BY seller_id";
				$seller_ids = $this->db->query($customerSql)->rows;

				foreach ($seller_ids as $seller_id) {
					//$total = $this->MsLoader->MsOrderData->getOrderTotal($order_id, $seller_id);
					/*total = '" . $total . "'*/
					$sqlSubOrders = "UPDATE " . DB_PREFIX . "ms_suborder
					SET date_added = '" . $dateAdded . "',
					date_modified = '" . $dateModified . "'
					WHERE order_id = " . (int)$order_id . " AND seller_id = " . (int)$seller_id['seller_id'];
					$this->db->query($sqlSubOrders);
				}
			}

			$sqlSubOrders = "SELECT suborder_id, order_id, seller_id FROM " . DB_PREFIX . "ms_suborder WHERE 1";
			$subOrders = $this->db->query($sqlSubOrders)->rows;
			foreach ($subOrders as $subOrder) {
				$sqlOrderProduct = "UPDATE " . DB_PREFIX . "ms_order_product_data
					SET suborder_id = " . (int)$subOrder['suborder_id'] . " WHERE
					seller_id = " . (int)$subOrder['seller_id'] . " AND order_id = " . (int)$subOrder['order_id'];
				$this->db->query($sqlOrderProduct);
			}

			$this->_createSchemaEntry('1.0.4.0');
		}

		if (version_compare($version, '1.0.4.1') < 0) {
			$layout_id = $this->db->query("SELECT layout_id FROM " . DB_PREFIX . "layout WHERE `name` = 'Account'")->row['layout_id'];
			$sql = "INSERT INTO " . DB_PREFIX . "layout_module (`layout_id`, `code`, `position`, `sort_order`) VALUES($layout_id, 'account', 'column_left', 1);";
			$this->db->query($sql);

			$account = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `code`='account' AND `key`='account_status'")->row;
			if (empty($account)) {
				$sql = "INSERT INTO " . DB_PREFIX . "setting SET `store_id` = 0, `code` = 'account', `key` = 'account_status', `value` = 1, `serialized` = 0";
			} else {
				$sql = "UPDATE " . DB_PREFIX . "setting SET `store_id` = 0, `code` = 'account', `key` = 'account_status', `value` = 1, `serialized` = 0 WHERE `setting_id` = " . (int)$account['setting_id'];
			}

			$this->db->query($sql);

			$this->_createSchemaEntry('1.0.4.1');
		}

		if (version_compare($version, '2.0.0.0') < 0) {
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_badge` (
			`badge_id` int(11) NOT NULL AUTO_INCREMENT,
			`image` varchar(255) DEFAULT NULL,
			PRIMARY KEY (`badge_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_badge_description` (
			`badge_id` int(11) NOT NULL,
			`name` varchar(32) NOT NULL DEFAULT '',
			`description` text NOT NULL,
			`language_id` int(11) NOT NULL,
			PRIMARY KEY (`badge_id`, `language_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_badge_seller_group` (
			`badge_seller_group_id` INT(11) NOT NULL AUTO_INCREMENT,
			`badge_id` INT(11) NOT NULL,
			`seller_id` int(11) DEFAULT NULL,
			`seller_group_id` int(11) DEFAULT NULL,
			PRIMARY KEY (`badge_seller_group_id`)) default CHARSET=utf8");


			/* social links */
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_channel` (
			`channel_id` int(11) NOT NULL AUTO_INCREMENT,
			`image` varchar(255) DEFAULT NULL,
			PRIMARY KEY (`channel_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_channel_description` (
			`channel_id` int(11) NOT NULL,
			`language_id` int(11) NOT NULL,
			`name` VARCHAR(32) NOT NULL DEFAULT '',
			`description` TEXT NOT NULL DEFAULT '',
			PRIMARY KEY (`channel_id`, `language_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_seller_channel` (
			`seller_id` int(11) NOT NULL,
			`channel_id` int(11) NOT NULL,
			`channel_value` varchar(255) DEFAULT NULL,
			PRIMARY KEY (`seller_id`, `channel_id`)) default CHARSET=utf8");

			/* messaging */
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_conversation` (
			`conversation_id` int(11) NOT NULL AUTO_INCREMENT,
			`product_id` int(11) DEFAULT NULL,
			`order_id` int(11) DEFAULT NULL,
			`title` varchar(256) NOT NULL DEFAULT '',
			`date_created` DATETIME NOT NULL,
			PRIMARY KEY (`conversation_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_message` (
			`message_id` int(11) NOT NULL AUTO_INCREMENT,
			`conversation_id` int(11) NOT NULL,
			`from` int(11) DEFAULT NULL,
			`to` int(11) DEFAULT NULL,
			`message` text NOT NULL DEFAULT '',
			`read` tinyint(1) NOT NULL DEFAULT 0,
			`date_created` DATETIME NOT NULL,
			PRIMARY KEY (`message_id`)) default CHARSET=utf8");

			$this->_createSchemaEntry('2.0.0.0');
		}

		if (version_compare($version, '2.0.0.1') < 0) {
			//Questions
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX ."ms_question` (
			`question_id` int(11) NOT NULL AUTO_INCREMENT,
			`author_id` int(11) NOT	NULL,
			`product_id` int(11) NOT NULL,
			`text` text NOT NULL DEFAULT '',
			`date_created` DATETIME NOT NULL,
			PRIMARY KEY (`question_id`)) DEFAULT CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_answer` (
			`answer_id` int(11) NOT NULL AUTO_INCREMENT,
			`question_id` int(11) NOT NULL,
			`author_id` int(11) NOT NULL,
			`date_created` DATETIME NOT NULL,
			`rating` int(11) DEFAULT NULL,
			`text` text NOT NULL DEFAULT '',
			PRIMARY KEY (`answer_id`)) DEFAULT CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_user_vote` (
			`answer_id` int(11) NOT NULL,
			`user_id` int(11) NOT NULL,
			`type` tinyint(1) NOT NULL) DEFAULT CHARSET=utf8");

			/*Index part for questions */
			$this->db->query("
				CREATE UNIQUE INDEX user
				ON " . DB_PREFIX ."ms_user_vote (user_id, answer_id)");

			$this->db->query("
				CREATE INDEX question_id
				ON " . DB_PREFIX ."ms_answer (question_id)");

			$this->db->query("
				CREATE INDEX answer_id
				ON " . DB_PREFIX ."ms_answer (answer_id)");

			$this->db->query("
				CREATE INDEX product_id
				ON " . DB_PREFIX ."ms_question (product_id)");

			$this->_createSchemaEntry('2.0.0.1');
		}

		if(version_compare($version, '2.0.0.2') < 0) {
			// Ratings/reviews
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_review` (
			`review_id` int(11) NOT NULL AUTO_INCREMENT,
			`author_id` int(11) NOT NULL,
			`product_id` int(11) NOT NULL,
			`order_product_id` int(11) NOT NULL,
			`order_id` int(11) DEFAULT NULL,
			`rating` int(1) NOT NULL,
			`title` varchar(128) NOT NULL DEFAULT '',
			`comment` text NOT NULL DEFAULT '',
			`description_accurate` int(1) NOT NULL,
			`helpful` int(11) DEFAULT NULL,
			`unhelpful` int(11) DEFAULT NULL,
			`date_created` DATETIME NOT NULL,
			`date_updated` DATETIME DEFAULT NULL,
			`status` tinyint DEFAULT 0,
			PRIMARY KEY (`review_id`)) default CHARSET=utf8");

			$this->db->query("CREATE UNIQUE INDEX idx_ms_review_order_product ON `" . DB_PREFIX ."ms_review` (order_id, product_id, order_product_id)");

			// drop indexes
			$this->db->query("DROP INDEX `user` ON `" . DB_PREFIX ."ms_user_vote`");
			$this->db->query("DROP INDEX `question_id` ON `" . DB_PREFIX ."ms_answer`");
			$this->db->query("DROP INDEX `answer_id` ON `" . DB_PREFIX ."ms_answer`");
			$this->db->query("DROP INDEX `product_id` ON `" . DB_PREFIX ."ms_question`");

			$this->_createSchemaEntry('2.0.0.2');
		}

		if(version_compare($version, '2.0.0.3') < 0) {
			// Shipping methods
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_shipping_method` (
			`shipping_method_id` int(11) NOT NULL AUTO_INCREMENT,
			`logo` TEXT DEFAULT '',
			`status` tinyint(1) NOT NULL DEFAULT 0,
			PRIMARY KEY (`shipping_method_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_shipping_method_description` (
			`shipping_method_description_id` int(11) NOT NULL AUTO_INCREMENT,
			`shipping_method_id` int(11) NOT NULL,
			`name` VARCHAR(32) NOT NULL DEFAULT '',
			`description` TEXT DEFAULT '',
			`language_id` int(11) DEFAULT NULL,
			PRIMARY KEY (`shipping_method_description_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_shipping_delivery_time` (
			`delivery_time_id` int(11) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY (`delivery_time_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_shipping_delivery_time_description` (
			`delivery_time_desc_id` int(11) NOT NULL AUTO_INCREMENT,
			`delivery_time_id` int(11) NOT NULL,
			`name` TEXT DEFAULT '',
			`language_id` int(11) NOT NULL DEFAULT 1,
			PRIMARY KEY (`delivery_time_desc_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_product_shipping` (
			`product_shipping_id` int(11) NOT NULL AUTO_INCREMENT,
			`product_id` int(11) NOT NULL,
			`from_country` int(11) NOT NULL DEFAULT 0,
			`free_shipping` int(11) NOT NULL DEFAULT 0,
			`processing_time` int(11) NOT NULL DEFAULT 0,
			PRIMARY KEY (`product_shipping_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_product_shipping_location` (
			`product_shipping_location_id` int(11) NOT NULL AUTO_INCREMENT,
			`product_id` int(11) NOT NULL,
			`to_country` int(11) NOT NULL,
			`shipping_method_id` int(11) NOT NULL DEFAULT 0,
			`delivery_time_id` int(11) NOT NULL DEFAULT 1,
			`cost` DECIMAL(15,4) NOT NULL,
			`additional_cost` DECIMAL(15,4) NOT NULL,
			PRIMARY KEY (`product_shipping_location_id`)) default CHARSET=utf8");

			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ms_order_product_data` 
			ADD `shipping_location_id` int(11) DEFAULT NULL,
			ADD `shipping_cost` DECIMAL(15,4) DEFAULT NULL");

			// MM Order total module install
			$this->db->query("INSERT INTO " . DB_PREFIX . "extension SET `type` = 'total', `code` = 'mm_shipping_total'");

			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('mm_shipping_total', array(
				'mm_shipping_total_status' => 1,
				'mm_shipping_total_sort_order' => 1
			));

			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/shipping-method');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/shipping-method');

			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'total/mm_shipping_total');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'total/mm_shipping_total');

			$this->_createSchemaEntry('2.0.0.3');
		}

		if(version_compare($version, '2.0.0.4') < 0) {
			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ms_product_shipping_location`
			CHANGE `to_country` `to_geo_zone_id` int(11) NOT NULL;");

			$this->_createSchemaEntry('2.0.0.4');
		}

		if(version_compare($version, '2.0.0.5') < 0) {
			// Create new table for order product shipping data
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_order_product_shipping_data` (
			`order_product_shipping_id` int(11) NOT NULL AUTO_INCREMENT,
			`order_id` int(11) NOT NULL,
			`product_id` int(11) NOT NULL,
			`order_product_id` int(11) NOT NULL,
			`shipping_location_id` int(11) DEFAULT NULL,
			`shipping_cost` DECIMAL(15,4) DEFAULT NULL,
			PRIMARY KEY (`order_product_shipping_id`)) default CHARSET=utf8");

			// Get shipping data from oc_ms_order_product_data
			$sql = "SELECT
						order_id,
						product_id,
						order_product_id,
						shipping_location_id,
						shipping_cost
					FROM " . DB_PREFIX . "ms_order_product_data";
			$old_shipping_data = $this->db->query($sql);

			foreach ($old_shipping_data->rows as $row) {
				$this->db->query("
				INSERT INTO " . DB_PREFIX . "ms_order_product_shipping_data
				SET
					order_id = " . (int)$row['order_id'] . ",
					product_id = " . (int)$row['product_id'] . ",
					order_product_id = " . (int)$row['order_product_id'] . ",
					shipping_location_id = " . (is_null($row['shipping_location_id']) ? "NULL" : (int)$row['shipping_location_id']) . ",
					shipping_cost = " . (is_null($row['shipping_cost']) ? "NULL" : (float)$row['shipping_cost']));
			}

			// Drop shipping columns from ms_order_product_data
			$this->db->query("
			ALTER TABLE " . DB_PREFIX . "ms_order_product_data
			DROP COLUMN shipping_location_id,
			DROP COLUMN shipping_cost;");

			$this->_createSchemaEntry('2.0.0.5');
		}

		if(version_compare($version, '2.0.1.0') < 0) {
			// Copy everything from oc_ms_payment to oc_ms_pg_payment and oc_ms_pg_request. And then delete oc_ms_payment
			/* Payment gateways, requests and payments */
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_pg_payment` (
			`payment_id` int(11) NOT NULL AUTO_INCREMENT,
			`seller_id` int(11) NOT NULL,
			`payment_type` int(11) NOT NULL,
			`payment_code` VARCHAR(128) NOT NULL,
			`payment_status` int(11) NOT NULL,
			`amount` DECIMAL(15,4) NOT NULL,
			`currency_id` int(11) NOT NULL,
			`currency_code` VARCHAR(3) NOT NULL,
			`sender_data` TEXT NOT NULL,
			`receiver_data` TEXT NOT NULL,
			`description` TEXT NOT NULL,
			`date_created` DATETIME NOT NULL,
			PRIMARY KEY (`payment_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_pg_request` (
			`request_id` int(11) NOT NULL AUTO_INCREMENT,
			`payment_id` VARCHAR(128) DEFAULT NULL,
			`seller_id` int(11) NOT NULL,
			`product_id` int(11) DEFAULT NULL,
			`order_id` int(11) DEFAULT NULL,
			`request_type` int(11) NOT NULL,
			`request_status` int(11) NOT NULL,
			`description` TEXT NOT NULL,
			`amount` DECIMAL(15,4) NOT NULL,
			`currency_id` int(11) NOT NULL,
			`currency_code` VARCHAR(3) NOT NULL,
			`date_created` DATETIME NOT NULL,
			`date_modified` DATETIME DEFAULT NULL,
			PRIMARY KEY (`request_id`)) default CHARSET=utf8");

			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/payment-gateway');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/payment-gateway');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/payment-request');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/payment-request');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/payment');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/payment');

			/**
			 * Backward compatibility for payments
			*/
			$old_payments = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_payment`");

			// Old payment types => new payment types
			$payment_types = array(
				'1' => MsPgPayment::TYPE_PAID_REQUESTS, // Signup fee => Paid requests
				'2' => MsPgPayment::TYPE_PAID_REQUESTS, // Product listing fee => Paid requests
				'3' => MsPgPayment::TYPE_PAID_REQUESTS, // Payout => Paid requests
				'4' => MsPgPayment::TYPE_PAID_REQUESTS, // Payout request => Paid requests
				'5' => MsPgPayment::TYPE_PAID_REQUESTS, // Recurring => Paid requests
				'6' => MsPgPayment::TYPE_SALE  			// Sales => Sale
			);

			// Payment method => new payment code
			$payment_codes = array(
				'1' => NULL, // Balance
				'2' => 'ms_pg_paypal', // PayPal
				'3' => 'ms_pp_adaptive' // PayPal Adaptive payments
			);

			foreach ($old_payments->rows as $old_payment_data) {
				// If record is balance related, skip it
				if(is_null($payment_codes[$old_payment_data['payment_method']])) continue;

				// If payment type is Payout, set seller_id = 0 that means payment was created by admin.
				// Else seller_id means id of a target participant of payment
				$payment_seller_id = ($old_payment_data['payment_type'] == 3 ? 0 :(int)$old_payment_data['seller_id']);

				// Payment method => description
				$receiver_data = array(
					'2' => array($old_payment_data['seller_id'] => array('pp_address' => $old_payment_data['payment_data'])),
					'3' => array('pp_address' => $old_payment_data['payment_data'])
				);

				// Create new record in oc_ms_pg_payment
				$this->db->query("
				INSERT INTO " . DB_PREFIX . "ms_pg_payment
				SET
					seller_id = '" . (int)$payment_seller_id . "',
					payment_type = " . (int)$payment_types[$old_payment_data['payment_type']] . ",
					payment_code = '" . $this->db->escape($payment_codes[$old_payment_data['payment_method']]) . "',
					payment_status = " . (int)$old_payment_data['payment_status'] . ",
					amount = " . (float)$old_payment_data['amount'] . ",
					currency_id = " . (int)$old_payment_data['currency_id'] . ",
					currency_code = '" . $this->db->escape($old_payment_data['currency_code']) . "',
					sender_data = '" . json_encode(array()) . "',
					receiver_data = '" . json_encode($receiver_data[$old_payment_data['payment_method']]) . "',
					description = '" . json_encode(array()) . "',
					date_created = '" . $this->db->escape($old_payment_data['date_created']) . "'
				");

				$payment_id = $this->db->getLastId();

				// Create new record in oc_ms_pg_request
				$this->db->query("
				INSERT INTO " . DB_PREFIX . "ms_pg_request
				SET
					payment_id = '" . (int)$payment_id . "',
					seller_id = " . (int)$old_payment_data['seller_id'] . ",
					product_id = " . (isset($old_payment_data['product_id']) ? (int)$old_payment_data['product_id'] : 'NULL') . ",
					order_id = " . (isset($old_payment_data['order_id']) ? (int)$old_payment_data['order_id'] : 'NULL') . ",
					request_type = " . (int)$old_payment_data['payment_type'] . ",
					request_status = " . (int)$old_payment_data['payment_status'] . ",
					description = '" . $this->db->escape($old_payment_data['description']) . "',
					amount = " . (float)$old_payment_data['amount'] . ",
					currency_id = " . (int)$old_payment_data['currency_id'] . ",
					currency_code = '" . $this->db->escape($old_payment_data['currency_code']) . "',
					date_created = '" . $this->db->escape($old_payment_data['date_created']) . "',
					date_modified = " . (is_null($old_payment_data['date_paid']) ? 'NULL' : ("'" . $this->db->escape($old_payment_data['date_paid']) . "'")) . "
				");

				$request_id = $this->db->getLastId();

				// Payment method => description
				$description = array(
					$request_id => $old_payment_data['description']
				);

				// Update Payment record
				$this->db->query("UPDATE " . DB_PREFIX . "ms_pg_payment SET description = '" . json_encode($description) . "'	WHERE payment_id = '" . $payment_id . "'");
			}

			// Drop oc_ms_payment
			$this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "ms_payment");

			/**
			 * Update paypal information for each seller
			 */
			$sellers = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_seller`");
			foreach ($sellers as $seller_data) {
				if(isset($seller_data['paypal']) && $seller_data['paypal']) {
					$pp_setting_name = 'slr_pg_paypal_pp_address';

					$this->db->query("
					INSERT INTO `" . DB_PREFIX . "ms_setting`
					SET
						seller_id = " . $seller_data['seller_id'] . ",
						seller_group_id = " . $seller_data['seller_group'] . ",
						name = '" . $pp_setting_name . "', 
						value = '" . $this->db->escape($seller_data['paypal']) . "',
						is_encoded = NULL
					ON DUPLICATE KEY UPDATE
						value = '" . $this->db->escape($seller_data['paypal']) . "'
					");
				}
			}

			// Drop column `paypal` from oc_ms_seller
			$this->db->query("ALTER TABLE " . DB_PREFIX . "ms_seller DROP COLUMN paypal;");

			$this->_createSchemaEntry('2.0.1.0');
		}

		if(version_compare($version, '2.1.0.0') < 0) {
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_seller_shipping` (
			`seller_shipping_id` int(11) NOT NULL AUTO_INCREMENT,
			`seller_id` int(11) NOT NULL,
			`from_geo_zone_id` int(11) NOT NULL,
			`processing_time` int(11) NOT NULL DEFAULT 0,
			PRIMARY KEY (`seller_shipping_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_seller_shipping_location` (
			`seller_shipping_location_id` int(11) NOT NULL AUTO_INCREMENT,
			`seller_shipping_id` int(11) NOT NULL,
			`shipping_method_id` int(11) NOT NULL,
			`delivery_time_id` int(11) NOT NULL,
			`to_geo_zone_id` int(11) NOT NULL,
			`weight_from` DECIMAL(15,4) NOT NULL,
			`weight_to` DECIMAL(15,4) NOT NULL,
			`weight_class_id` int(11) NOT NULL,
			`cost` DECIMAL(15,4) NOT NULL,
			PRIMARY KEY (`seller_shipping_location_id`)) default CHARSET=utf8");

			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ms_order_product_shipping_data`
			CHANGE `shipping_location_id` `fixed_shipping_method_id` int(11) NOT NULL");

			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ms_order_product_shipping_data`
			ADD `combined_shipping_method_id` int(11) DEFAULT NULL AFTER `fixed_shipping_method_id`");

			$this->_createSchemaEntry('2.1.0.0');
		}

		if(version_compare($version, '2.1.0.1') < 0) {
			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ms_product_shipping`
			ADD `override` int(11) DEFAULT 0");

			$this->_createSchemaEntry('2.1.0.1');
		}

		if(version_compare($version, '2.2.0.0') < 0) {
			// Category and product based commissions
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_category_commission` (
			`category_commission_id` int(11) NOT NULL AUTO_INCREMENT,
			`category_id` int(11) NOT NULL,
			`commission_id` int(11) DEFAULT NULL,
			PRIMARY KEY (`category_commission_id`)) default CHARSET=utf8");

			$this->db->query("CREATE UNIQUE INDEX cat_id ON " . DB_PREFIX ."ms_category_commission (category_id)");

			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ms_product`
			ADD `commission_id` int(11) DEFAULT NULL");

			// Multi-language seller description
			$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_seller_description` (
			`seller_description_id` int(11) NOT NULL AUTO_INCREMENT,
			`seller_id` int(11) NOT NULL,
			`language_id` int(11) NOT NULL,
			`description` text DEFAULT '',
			PRIMARY KEY (`seller_description_id`)) default CHARSET=utf8");

			// Add seller descriptions from ms_seller to ms_seller_description
			$getDataQuery = "SELECT seller_id, description FROM " . DB_PREFIX . "ms_seller";
			$seller_data = $this->db->query($getDataQuery)->rows;

			$this->load->model('localisation/language');
			$languages = $this->model_localisation_language->getLanguages();

			foreach ($seller_data as $row) {
				foreach ($languages as $code => $language) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "ms_seller_description
					SET seller_id = " . (int)$row['seller_id'] . ",
						language_id = " . (int)$language['language_id'] . ",
						description = '" . $this->db->escape($row['description']) . "'");
				}
			}

			// Refactor seller's and seller's group settings structure
			$this->db->query("ALTER TABLE " . DB_PREFIX . "ms_setting DROP COLUMN seller_group_id;");
			$this->db->query("RENAME TABLE `" . DB_PREFIX . "ms_setting` TO `" . DB_PREFIX . "ms_seller_setting`");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_seller_group_setting` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`seller_group_id` int(11) unsigned DEFAULT NULL,
			`name` varchar(50) DEFAULT NULL,
			`value` varchar(250) DEFAULT NULL,
			`is_encoded` smallint(1) unsigned DEFAULT NULL,
			PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8;");

			$this->db->query("CREATE UNIQUE INDEX slr_gr_id_name ON " . DB_PREFIX ."ms_seller_group_setting (seller_group_id, name)");

			// Set default fee priority
			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_fee_priority',
				'value' => 2
			));

			$this->_createSchemaEntry('2.2.0.0');
		}

		if(version_compare($version, '2.2.0.1') < 0) {
			// cleanup duplicate descriptions
			$getDataQuery = "SELECT * FROM " . DB_PREFIX . "ms_seller_description ORDER BY seller_description_id DESC";
			$description_data = $this->db->query($getDataQuery)->rows;

			$seller_descriptions = array();
			$removeids = array();

			foreach ($description_data as $d) {
				$seller_id = $d['seller_id'];
				$language_id = $d['language_id'];
				$description = $d['description'];
				if (!isset($seller_descriptions[$seller_id][$language_id])) {
					$seller_descriptions[$seller_id][$language_id] = $description;
				} else {
					$removeids[] = $d['seller_description_id'];
				}
			}

			foreach ($removeids as $id) {
				$sql = "DELETE FROM " . DB_PREFIX . "ms_seller_description WHERE seller_description_id = ".(int)$id;
				$this->db->query($sql);
			}

			$this->db->query("ALTER TABLE `" . DB_PREFIX . "ms_seller_description` ADD UNIQUE `seller_language_id`(`seller_id`, `language_id`)");
			$this->_createSchemaEntry('2.2.0.1');
		}

		if(version_compare($version, '2.2.0.2') < 0) {
			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ms_seller_shipping`
			CHANGE `from_geo_zone_id` `from_country_id` int(11) NOT NULL;");

			$this->_createSchemaEntry('2.2.0.2');
		}

		if(version_compare($version, '2.2.0.3') < 0) {
			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ms_seller_shipping_location`
			CHANGE `cost` `cost_fixed` DECIMAL(15,4) NOT NULL;");

			$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "ms_seller_shipping_location`
			ADD (`cost_pwu` DECIMAL(15,4) NOT NULL DEFAULT 0);");

			$this->_createSchemaEntry('2.2.0.3');
		}

		if(version_compare($version, '2.2.0.4') < 0) {
			if($this->config->get('config_weight_class_id')) {
				$weights_to_convert = $this->db->query("SELECT seller_shipping_location_id, weight_from, weight_to, weight_class_id FROM `" . DB_PREFIX . "ms_seller_shipping_location`;");

				if($weights_to_convert->num_rows) {
					foreach ($weights_to_convert->rows as $row) {
						$weight_from_converted = $this->weight->convert($row['weight_from'], $row['weight_class_id'], $this->config->get('config_weight_class_id'));
						$weight_to_converted = $this->weight->convert($row['weight_to'], $row['weight_class_id'], $this->config->get('config_weight_class_id'));

						$this->db->query("UPDATE `" . DB_PREFIX . "ms_seller_shipping_location`
							SET weight_from = " . (float)$weight_from_converted . ",
								weight_to = " . (float)$weight_to_converted . ",
								weight_class_id = " . (int)$this->config->get('config_weight_class_id') ."
							WHERE seller_shipping_location_id = " . (int)$row['seller_shipping_location_id']);
					}
				}
			}

			$this->_createSchemaEntry('2.2.0.4');
		}

		if(version_compare($version, '2.3.0.0') < 0) {
			$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ms_attribute`");
			$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ms_attribute_description`");
			$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ms_attribute_value`");
			$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ms_attribute_value_description`");
			$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ms_attribute_attribute`");
			$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "ms_product_attribute`");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_attribute` (
			`attribute_id` int(11) NOT NULL AUTO_INCREMENT,
			`seller_id` int(11) DEFAULT 0,
			`attribute_status` int(11) NOT NULL,
			PRIMARY KEY (`attribute_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_attribute_group` (
			`attribute_group_id` int(11) NOT NULL AUTO_INCREMENT,
			`seller_id` int(11) DEFAULT 0,
			`attribute_group_status` int(11) NOT NULL,
			PRIMARY KEY (`attribute_group_id`)) default CHARSET=utf8");

			$this->_createSchemaEntry('2.3.0.0');
		}

		if(version_compare($version, '2.3.0.1') < 0) {
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_option` (
			`option_id` int(11) NOT NULL AUTO_INCREMENT,
			`seller_id` int(11) DEFAULT 0,
			`option_status` int(11) NOT NULL,
			PRIMARY KEY (`option_id`)) default CHARSET=utf8");

			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/option');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/option');

			$this->_createSchemaEntry('2.3.0.1');
		}

		if(version_compare($version, '2.3.0.2') < 0) {
			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_allow_seller_attributes',
				'value' => 0
			));

			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_allow_seller_options',
				'value' => 0
			));

			$this->_createSchemaEntry('2.3.0.2');
		}

		if(version_compare($version, '2.3.0.3') < 0) {
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_customer_ppakey` (
			`customer_id` int(11) NOT NULL,
            `preapprovalkey` varchar(255) NOT NULL,
            `active` smallint(1) NOT NULL DEFAULT '0',
             PRIMARY KEY (`customer_id`)
			) DEFAULT CHARSET=utf8;");

			$this->_createSchemaEntry('2.3.0.3');
		}

		if(version_compare($version, '2.3.0.4') < 0) {
			$this->db->query("
			CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "ms_conversation_to_order (
  			`order_id` int(11) NOT NULL,
  			`suborder_id` int(11) NOT NULL,
  			`conversation_id` int(11) NOT NULL,
 			PRIMARY KEY (`order_id`,`suborder_id`,`conversation_id`))
			default CHARSET=utf8");

			$this->db->query("
			ALTER TABLE " . DB_PREFIX . "ms_conversation DROP COLUMN order_id;");

			$this->db->query("
			ALTER TABLE " . DB_PREFIX . "ms_conversation
			ADD conversation_from INT(11) DEFAULT NULL");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "ms_conversation_participants (
  			`conversation_id` int(11) NOT NULL,
  			`customer_id` int(11) NOT NULL DEFAULT '0',
  			`user_id` int(11) NOT NULL DEFAULT '0',
 			PRIMARY KEY (`conversation_id`,`customer_id`,`user_id`))
			default CHARSET=utf8");

			$conversation_data = $this->db->query("
			SELECT conv.*,
			(SELECT msm.from  FROM `" . DB_PREFIX . "ms_message` as msm WHERE msm.conversation_id = conv.conversation_id ORDER BY message_id ASC LIMIT 1) as data_conversation_from
			FROM " . DB_PREFIX . "ms_conversation conv
			");

			if ($conversation_data->num_rows){
				foreach ($conversation_data->rows as $data){
					$this->db->query("UPDATE " . DB_PREFIX . "ms_conversation SET
					conversation_from = '" . (int)$data["data_conversation_from"] . "'
					WHERE conversation_id = '" .  (int)$data["conversation_id"] . "'");
				}
			}

			$this->_createSchemaEntry('2.3.0.4');
		}

		if(version_compare($version, '2.3.0.5') < 0) {
			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/conversation');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/conversation');

			$this->db->query("
			CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "ms_conversation_to_product (
  			`product_id` int(11) NOT NULL,
  			`conversation_id` int(11) NOT NULL,
 			PRIMARY KEY (`product_id`,`conversation_id`))
			default CHARSET=utf8");

			$conversation_data = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_conversation conv WHERE product_id IS NOT NULL");
			if ($conversation_data->num_rows){
				foreach ($conversation_data->rows as $data){
					$this->db->query("INSERT INTO " . DB_PREFIX . "ms_conversation_to_product SET
					product_id = '" . (int)$data["product_id"] . "',
					conversation_id = '" . (int)$data["conversation_id"] . "'
					");
				}
			}

			$this->db->query("
			ALTER TABLE " . DB_PREFIX . "ms_conversation DROP COLUMN product_id;");

			$this->db->query("
			ALTER TABLE " . DB_PREFIX . "ms_message DROP COLUMN `to`;");

			$this->db->query("
			ALTER TABLE " . DB_PREFIX . "ms_message
			ADD from_admin INT(1) DEFAULT 0");

			$this->_createSchemaEntry('2.3.0.5');
		}

		if(version_compare($version, '2.3.0.6') < 0) {
			$this->db->query("
			CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "ms_message_upload (
  			`message_id` int(11) NOT NULL,
  			`upload_id` int(11) NOT NULL,
			PRIMARY KEY (`message_id`, `upload_id`))
			default CHARSET=utf8");

			$this->_createSchemaEntry('2.3.0.6');
		}

		if(version_compare($version, '2.4.0.0') < 0) {
			/* Seller categories */
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_category` (
			`category_id` int(11) NOT NULL AUTO_INCREMENT,
			`parent_id` int(11) NOT NULL DEFAULT 0,
			`seller_id` int(11) NOT NULL DEFAULT 0,
			`image` VARCHAR(255) DEFAULT NULL,
			`sort_order` int(11) NOT NULL DEFAULT 0,
			`category_status` int(11) NOT NULL,
			PRIMARY KEY (`category_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_category_description` (
			`category_id` int(11) NOT NULL,
			`language_id` int(11) NOT NULL,
			`name` VARCHAR(255) NOT NULL DEFAULT '',
			`description` TEXT NOT NULL DEFAULT '',
			`meta_title` VARCHAR(255) NOT NULL DEFAULT '',
			`meta_description` VARCHAR(255) NOT NULL DEFAULT '',
			`meta_keyword` VARCHAR(255) NOT NULL DEFAULT '',
			PRIMARY KEY (`category_id`, `language_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_category_filter` (
			`category_id` int(11) NOT NULL,
			`oc_filter_id` int(11) NOT NULL,
			PRIMARY KEY (`category_id`, `oc_filter_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_category_to_store` (
			`category_id` int(11) NOT NULL,
			`store_id` int(11) NOT NULL,
			PRIMARY KEY (`category_id`, `store_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_category_path` (
			`category_id` int(11) NOT NULL,
			`path_id` int(11) NOT NULL,
			`level` int(11) NOT NULL,
			PRIMARY KEY (`category_id`, `path_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_product_to_category` (
			`product_id` int(11) NOT NULL,
			`ms_category_id` int(11) NOT NULL,
			PRIMARY KEY (`product_id`, `ms_category_id`)) default CHARSET=utf8");

			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/category');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/category');

			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_allow_seller_categories',
				'value' => 0
			));

			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_product_category_type',
				'value' => 1
			));

			$this->_createSchemaEntry('2.4.0.0');
		}

		if(version_compare($version, '2.4.0.1') < 0) {
			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_msg_allowed_file_types',
				'value' => 'png,jpg,jpeg,zip,rar,pdf'
			));

			$this->_createSchemaEntry('2.4.0.1');
		}

		if(version_compare($version, '2.4.0.2') < 0) {
			// Create additional tables for reviews
			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_review_attachment` (
			`review_attachment_id` int(11) NOT NULL AUTO_INCREMENT,
			`review_id` int(11) NOT NULL,
			`attachment` text NOT NULL,
			PRIMARY KEY (`review_attachment_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_review_comment` (
			`comment_id` int(11) NOT NULL AUTO_INCREMENT,
			`review_id` int(11) NOT NULL,
			`author_id` int(11) NOT NULL,
			`text` text NOT NULL,
			`rating` int(11) NOT NULL,
			`date_created` DATETIME NOT NULL,
			`date_updated` DATETIME DEFAULT NULL,
			PRIMARY KEY (`comment_id`)) default CHARSET=utf8");

			$this->db->query("ALTER TABLE " . DB_PREFIX . "ms_review DROP COLUMN `description_accurate`;");

			$this->_createSchemaEntry('2.4.0.2');
		}

		if(version_compare($version, '2.4.0.3') < 0) {
			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/review');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/review');

			$this->_createSchemaEntry('2.4.0.3');
		}

		if(version_compare($version, '2.4.0.4') < 0) {
			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_logging_level',
				'value' => \MultiMerch\Logger\Logger::LEVEL_ERROR
			));

			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_logging_filename',
				'value' => 'ms_logging.log'
			));

			$this->_createSchemaEntry('2.4.0.4');
		}

		if(version_compare($version, '2.4.0.5') < 0) {
			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_logging_filename',
				'value' => 'ms_logging.' . uniqid() . '.log'
			));

			$this->_createSchemaEntry('2.4.0.5');
		}

		if(version_compare($version, '2.4.0.6') < 0) {
			$allowed_option_types = array(
				'choose' => array('select', 'radio', 'checkbox'),
				'input' => array('text', 'textarea'),
				'file' => array('file'),
				'date' => array('date', 'time', 'datetime')
			);

			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_allowed_seller_option_types',
				'value' => $allowed_option_types
			));

			$this->_createSchemaEntry('2.4.0.6');
		}

		if(version_compare($version, '2.4.0.7') < 0) {
			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_allow_questions',
				'value' => 0
			));

			$this->db->query("CREATE UNIQUE INDEX idx_shipping_delivery_time_description_to_language ON `" . DB_PREFIX ."ms_shipping_delivery_time_description` (delivery_time_desc_id, language_id)");

			$this->_createSchemaEntry('2.4.0.7');

		}

		if(version_compare($version, '2.4.1.0') < 0) {
			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/event');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/event');

			$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ms_event` (
			`event_id` int(11) NOT NULL AUTO_INCREMENT,
			`admin_id` int(11) unsigned DEFAULT NULL,
			`customer_id` int(11) unsigned DEFAULT NULL,
			`seller_id` int(11) unsigned DEFAULT NULL,
			`event_type` tinyint NOT NULL,
			`data` text NOT NULL,
			`body` text DEFAULT NULL,
			`date_created` DATETIME NOT NULL,
			PRIMARY KEY (`event_id`)) default CHARSET=utf8");

			$this->_createSchemaEntry('2.4.1.0');
		}

		if(version_compare($version, '2.5.0.0') < 0) {
            $this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_import_config` (
              `config_id` int(11) NOT NULL AUTO_INCREMENT,
              `customer_id` int(11) NOT NULL,
              `config_name` varchar(255) DEFAULT NULL,
              `import_type` varchar(100) DEFAULT NULL,
              `attachment_code` varchar(255) DEFAULT NULL,
              `mapping` text,
              `start_row` int(11) DEFAULT NULL,
              `finish_row` int(11) DEFAULT NULL,
              `update_key_id` int(11) DEFAULT NULL,
              `file_encoding` int(11) DEFAULT NULL,
              `date_added` datetime NOT NULL,
              `date_modified` datetime NOT NULL,
              PRIMARY KEY (`config_id`)
			) default CHARSET=utf8");

            //test config for import
            //$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "ms_import_config` VALUES (27, 0, 'TestImport1', 'product', '1a203d50ff8790d7254e854259b64046a834290f', 'a:13:{i:0;s:1:\"2\";i:1;s:2:\"15\";i:2;s:1:\"3\";i:4;s:3:\"101\";i:5;s:3:\"102\";i:6;s:1:\"6\";i:8;s:3:\"103\";i:9;s:3:\"104\";i:12;s:3:\"105\";i:17;s:2:\"11\";i:18;s:2:\"12\";i:19;s:1:\"4\";i:22;s:2:\"14\";}', 2, 2, 2, 1, '2017-04-24 12:46:53', '2017-04-25 17:37:45')");
            //$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "upload` VALUES (130, 'BookAndAuthor21.04.2017.csv', 'BookAndAuthor21.04.2017.csv.tQgtdNnwZAP9E2JTVot3gIREmxvBCHgc', '1a203d50ff8790d7254e854259b64046a834290f', '2017-04-21 16:02:25')");

            $this->_createSchemaEntry('2.5.0.0');
        }

		if(version_compare($version, '2.5.0.1') < 0) {

			if (!$this->checkIfExist(DB_PREFIX .'ms_import_config', 'cell_container')) {
				$this->db->query("
				ALTER TABLE " . DB_PREFIX . "ms_import_config
				ADD cell_container varchar(10) DEFAULT NULL");
			}

			if (!$this->checkIfExist(DB_PREFIX .'ms_import_config', 'cell_separator')) {
				$this->db->query("
				ALTER TABLE " . DB_PREFIX . "ms_import_config
				ADD cell_separator varchar(10) DEFAULT NULL");
			}

			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_msg_allowed_file_types',
				'value' => 'png,jpg,jpeg,zip,rar,pdf,csv'
			));
			$this->_createSchemaEntry('2.5.0.1');
		}

		if(version_compare($version, '2.5.0.2') < 0) {
			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/import');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/import');

			$this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_import_history` (
              `import_id` int(11) NOT NULL AUTO_INCREMENT,
			  `seller_id` int(11) NOT NULL,
			  `name` varchar(255) NOT NULL,
			  `filename` varchar(255) NOT NULL,
			  `date_added` datetime NOT NULL,
			  `type` varchar(100) NOT NULL,
			  `processed` int(11) NOT NULL,
			  `added` int(11) NOT NULL,
			  `updated` int(11) NOT NULL,
			  `errors` int(11) NOT NULL,
			  `product_ids` text DEFAULT NULL,
			  PRIMARY KEY (`import_id`)
			) default CHARSET=utf8");
			$this->_createSchemaEntry('2.5.0.2');
		}

		if(version_compare($version, '2.5.0.3') < 0) {
			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/question');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/question');

			$this->_createSchemaEntry('2.5.0.3');
		}

		if(version_compare($version, '2.6.0.0') < 0) {
			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/report');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/report');

			$reports = glob(DIR_APPLICATION . 'controller/multimerch/report/*.php');
			foreach ($reports as $report) {
				$route = basename($report, '.php');
				$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/report/' . $route);
				$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/report/' . $route);
			}

			// Seller layouts
			$this->db->query("INSERT INTO " . DB_PREFIX . "layout SET name = '[MultiMerch] Sellers Reports'");
			$layout_id = $this->db->getLastId();
			$this->db->query("INSERT INTO " . DB_PREFIX . "layout_module (`layout_id`, `code`, `position`, `sort_order`) VALUES($layout_id, 'account', 'column_right', 1);");
			$this->db->query("INSERT INTO " . DB_PREFIX . "layout_route SET layout_id = '" . (int)$layout_id . "', route = 'seller/report/%'");

			$this->_createSchemaEntry('2.6.0.0');
		}

		if(version_compare($version, '2.6.0.1') < 0) {
			// Delete all empty entries from `ms_seller_channel`
			$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_seller_channel` WHERE `channel_value` IS NULL OR `channel_value` = ''");

			$this->_createSchemaEntry('2.6.0.1');
		}

		if(version_compare($version, '2.7.0.0') < 0) {
			$this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_custom_field_group` (
			`custom_field_group_id` int(11) NOT NULL AUTO_INCREMENT,
			`admin_id` int(11) NOT NULL DEFAULT 0,
			`status` tinyint NOT NULL DEFAULT 0,
			`sort_order` int(11) NOT NULL DEFAULT 0,
			PRIMARY KEY (`custom_field_group_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_custom_field_group_description` (
			`custom_field_group_id` int(11) NOT NULL,
			`language_id` int(11) NOT NULL,
			`name` text NOT NULL,
			PRIMARY KEY (`custom_field_group_id`, `language_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_custom_field_group_to_location` (
			`custom_field_group_id` int(11) NOT NULL,
			`location_id` int(11) NOT NULL DEFAULT 1,
			PRIMARY KEY (`custom_field_group_id`, `location_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_custom_field` (
			`custom_field_id` int(11) NOT NULL AUTO_INCREMENT,
			`custom_field_group_id` int(11) NOT NULL,
			`admin_id` int(11) NOT NULL DEFAULT 0,
			`type` varchar(100) NOT NULL DEFAULT '',
			`required` tinyint NOT NULL DEFAULT 0,
			`status` tinyint NOT NULL DEFAULT 0,
			`sort_order` int(11) NOT NULL DEFAULT 0,
			PRIMARY KEY (`custom_field_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_custom_field_description` (
			`custom_field_id` int(11) NOT NULL,
			`language_id` int(11) NOT NULL,
			`name` text NOT NULL,
			PRIMARY KEY (`custom_field_id`, `language_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_custom_field_value` (
			`custom_field_value_id` int(11) NOT NULL AUTO_INCREMENT,
			`custom_field_id` int(11) NOT NULL,
			`sort_order` int(11) NOT NULL DEFAULT 0,
			PRIMARY KEY (`custom_field_value_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_custom_field_value_description` (
			`custom_field_value_id` int(11) NOT NULL,
			`custom_field_id` int(11) NOT NULL,
			`language_id` int(11) NOT NULL,
			`name` text NOT NULL,
			PRIMARY KEY (`custom_field_value_id`, `language_id`)) default CHARSET=utf8");

			$this->load->model('user/user_group');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/custom-field');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/custom-field');

			$this->_createSchemaEntry('2.7.0.0');
		}

		if(version_compare($version, '2.7.0.1') < 0) {
			$this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_product_custom_field` (
			`product_id` int(11) NOT NULL,
			`custom_field_id` int(11) NOT NULL,
			`value` text NOT NULL DEFAULT '',
			PRIMARY KEY (`product_id`, `custom_field_id`)) default CHARSET=utf8");

			$this->_createSchemaEntry('2.7.0.1');
		}

		if(version_compare($version, '2.7.0.2') < 0) {
			$this->db->query("ALTER TABLE " . DB_PREFIX . "ms_custom_field ADD `validation` text NOT NULL DEFAULT '' AFTER `required`");

			$this->_createSchemaEntry('2.7.0.2');
		}

		if(version_compare($version, '2.7.0.3') < 0) {
			$this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_custom_field_group_note` (
			`custom_field_group_id` int(11) NOT NULL,
			`language_id` int(11) NOT NULL,
			`note` text NOT NULL DEFAULT '',
			PRIMARY KEY (`custom_field_group_id`, `language_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_custom_field_note` (
			`custom_field_id` int(11) NOT NULL,
			`language_id` int(11) NOT NULL,
			`note` text NOT NULL DEFAULT '',
			PRIMARY KEY (`custom_field_id`, `language_id`)) default CHARSET=utf8");

			$this->_createSchemaEntry('2.7.0.3');
		}

		/****************************************** RELEASE 8.11 ******************************************************/

		if(version_compare($version, '2.8.0.0') < 0) {
			$this->load->model('user/user_group');

			/**********************************************************************************************************/
			/*                               		  	   Licensing											  	  */
			/**********************************************************************************************************/

			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_license_key',
				'value' => ''
			));

			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_license_activated',
				'value' => 0
			));


			/**********************************************************************************************************/
			/*                               		Order system improvements									 	  */
			/**********************************************************************************************************/

			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/order');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/order');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/suborder-status');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/suborder-status');

			/**
			 * Create new `ms_suborder_status` table.
			 */
			$this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_suborder_status` (
			`ms_suborder_status_id` int(11) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY (`ms_suborder_status_id`)) default CHARSET=utf8");

			/**
			 * Create new `ms_suborder_status_description` table.
			 */
			$this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_suborder_status_description` (
			`ms_suborder_status_id` int(11) NOT NULL,
			`language_id` int(11) NOT NULL,
			`name` varchar(256) NOT NULL DEFAULT '',
			PRIMARY KEY (`ms_suborder_status_id`, `language_id`)) default CHARSET=utf8");

			/**
			 * Create suborder statuses.
			 *
			 * In case of update, we only copy OpenCart order statuses.
			 */
			$oc_order_statuses = $this->db->query("SELECT order_status_id, language_id, `name` FROM `" . DB_PREFIX . "order_status`");

			foreach ($oc_order_statuses->rows as $row) {
				$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "ms_suborder_status` SET ms_suborder_status_id = " . (int)$row['order_status_id']);

				$this->db->query("
					INSERT INTO `" . DB_PREFIX . "ms_suborder_status_description`
					SET ms_suborder_status_id = " . (int)$row['order_status_id'] . ",
						language_id = " . (int)$row['language_id'] . ",
						`name` = '" . $this->db->escape($row['name']) . "'
				");
			}

			/**
			 * Create new default suborder status setting `msconf_suborder_default_status`.
			 *
			 * Default status is taken from OpenCart's setting `config_order_status_id`. It can be changed by admin in
			 * Multimerch > System > Settings > Orders tab.
			 */
			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_suborder_default_status',
				'value' => $this->config->get('config_order_status_id')
			));

			/**
			 * Update old default suborder statuses in `ms_suborder` and `ms_suborder_history` tables.
			 *
			 * Old default suborder status = 0, we now update it to the value of OpenCart's setting `config_order_status_id`.
			 */
			$this->db->query("
				UPDATE `" . DB_PREFIX . "ms_suborder`
				SET `order_status_id` = '" . $this->config->get('config_order_status_id') . "'
				WHERE `order_status_id` = 0
			");

			$this->db->query("
				UPDATE `" . DB_PREFIX . "ms_suborder_history`
				SET `order_status_id` = '" . $this->config->get('config_order_status_id') . "'
				WHERE `order_status_id` = 0
			");

			/**
			 * Create order/suborder statuses to states relation.
			 *
			 * In case of update, order and suborder states and their statuses are the same.
			 * Firstly, we check OpenCart order_status_ids are not duplicated in different states.
			 */
			$state_pending = (array)$this->config->get('config_order_status_id');
			$state_completed = array_diff($this->config->get('config_complete_status') ?: array(), $state_pending);
			$state_processing = array_diff($this->config->get('config_processing_status') ?: array(), array_merge($state_pending, $state_completed));

			/**
			 * Create `msconf_order_state` setting that represents OpenCart order_states - order_status_ids linkings.
			 *
			 * Its structure:
			 * 		array(
			 * 			'order_state_id' => array('order_status_id', ...),
			 * 			...
			 * 		)
			 */
			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_order_state',
				'value' => array(
					MsOrderData::STATE_PENDING => (array)$state_pending,
					MsSuborder::STATE_PROCESSING => (array)$state_processing,
					MsSuborder::STATE_COMPLETED => (array)$state_completed,
				)
			));

			/**
			 * Create `msconf_suborder_state` setting that represents MultiMerch suborder_states - suborder_status_ids linkings.
			 *
			 * Its structure:
			 * 		array(
			 * 			'suborder_state_id' => array('suborder_status_id', ...),
			 * 			...
			 * 		)
			 */
			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_suborder_state',
				'value' => array(
					MsSuborder::STATE_PENDING => (array)$state_pending,
					MsSuborder::STATE_PROCESSING => (array)$state_processing,
					MsSuborder::STATE_COMPLETED => (array)$state_completed
				)
			));

			/**
			 * Re-create settings `msconf_credit_order_statuses` and `msconf_debit_order_statuses` responsible for
			 * credit-debit suborder statuses.
			 *
			 * Settings' structure:
			 * 		array(
			 * 			'oc' => array('oc_status_id', ...),
			 * 			'ms' => array('ms_suborder_status_id', ...)
			 * 		)
			 */
			$old_credit_order_statuses = (array)$this->config->get('msconf_credit_order_statuses');
			$old_debit_order_statuses = (array)$this->config->get('msconf_debit_order_statuses');

			// Re-create msconf_credit_order_statuses to include ms suborder statuses
			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_credit_order_statuses',
				'value' => array(
					'oc' => $old_credit_order_statuses,
					'ms' => array()
				)
			));

			// Re-create msconf_debit_order_statuses to include ms suborder statuses
			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_debit_order_statuses',
				'value' => array(
					'oc' => $old_debit_order_statuses,
					'ms' => array()
				)
			));


			/**********************************************************************************************************/
			/*                               	 		Payout system										  		  */
			/**********************************************************************************************************/

			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/payout');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/payout');

			$this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_payout` (
			`payout_id` int(11) NOT NULL AUTO_INCREMENT,
			`name` text NOT NULL DEFAULT '',
			`date_created` datetime NOT NULL,
			`date_payout_period` datetime NOT NULL,
			PRIMARY KEY (`payout_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_payout_to_invoice` (
			`payout_id` int(11) NOT NULL,
			`invoice_id` int(11) NOT NULL,
			PRIMARY KEY (`payout_id`, `invoice_id`)) default CHARSET=utf8");

			/**
			 * Get all old invoices and assign them to one Payout instance.
			 */
			$old_invoices_data = $this->db->query("
				SELECT
					request_id,
					date_created
				FROM `" . DB_PREFIX . "ms_pg_request`
				WHERE request_type = '" . (int)MsPgRequest::TYPE_PAYOUT . "'
				ORDER BY date_created DESC
			");

			if($old_invoices_data->num_rows) {
				$last_payout_date = isset($old_invoices_data->rows[0]['date_created']) ? $old_invoices_data->rows[0]['date_created'] : FALSE;

				if($last_payout_date) {
					/**
					 * Create Payout instance.
					 */
					$payout_id = 1;
					$this->db->query("
						INSERT INTO `" . DB_PREFIX . "ms_payout`
						SET `payout_id` = '" . (int)$payout_id . "',
							`name` = '" . $this->language->get('ms_payout_payout') . " " . $this->language->get('ms_id') . "1 (" . $this->db->escape(date($this->language->get('date_format_short'), strtotime($last_payout_date))) . ")',
							`date_created` = '" . $this->db->escape($last_payout_date) . "',
							`date_payout_period` = '" . $this->db->escape($last_payout_date) . "'
					");

					foreach ($old_invoices_data->rows as $row) {
						$this->db->query("
							INSERT INTO `" . DB_PREFIX . "ms_payout_to_invoice`
							SET `payout_id` = '" . (int)$payout_id . "',
								`invoice_id` = '" . (int)$row['request_id'] . "'
						");
					}
				}
			}


			/**********************************************************************************************************/
			/*                               	Items deletion improvements									  		  */
			/**********************************************************************************************************/

			/**
			 * Recreate default seller group if it has been deleted.
			 * This group has special meaning and thus can't be deleted anymore.
			 */
			$this->db->query("
				INSERT INTO `" . DB_PREFIX . "ms_seller_group`
				SET seller_group_id = '1',
					commission_id = '1',
					product_period = '0',
					product_quantity = '0'
				ON DUPLICATE KEY UPDATE seller_group_id = seller_group_id
			");

			$languages = $this->model_localisation_language->getLanguages();

			foreach ($languages as $code => $language) {
				$exists = $this->db->query("SELECT 1 FROM `" . DB_PREFIX . "ms_seller_group_description` WHERE seller_group_id = '1' AND language_id = '" . (int)$language['language_id'] . "'");

				if (!$exists->num_rows) {
					$this->db->query("
						INSERT INTO `" . DB_PREFIX . "ms_seller_group_description`
						SET seller_group_id = '1',
							name = 'Default',
							description = 'Default seller group',
							language_id = '" . (int)$language['language_id'] . "'
						ON DUPLICATE KEY UPDATE seller_group_id = seller_group_id
					");
				}
			}


			/**********************************************************************************************************/
			/*                             	Additional category levels for import							  		  */
			/**********************************************************************************************************/

			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_import_category_type',
				'value' => 0
			));


			/**********************************************************************************************************/
			/*                             		   Reviews system refactoring							  		      */
			/**********************************************************************************************************/

			if (!$this->checkIfExist(DB_PREFIX . 'ms_review', 'seller_id')) {
				$this->db->query("ALTER TABLE " . DB_PREFIX . "ms_review ADD seller_id int(11) DEFAULT NULL");
			}

			$this->db->query("
				UPDATE `" . DB_PREFIX . "ms_review` msr
				SET seller_id = (SELECT seller_id FROM " . DB_PREFIX . "ms_product WHERE product_status = 1 AND product_approved = 1 AND product_id = msr.product_id)
			");


			/**********************************************************************************************************/
			/*                             		  What's new in MultiMerch 8.11						  		      	  */
			/**********************************************************************************************************/

			$upgrade_info['8.11'][] = "You can now activate your license key and receive information about MultiMerch updates in <a target='_blank' href='" . $this->url->link('module/multimerch', 'token=' . $this->session->data['token'] . '#tab-updates') . "'>MultiMerch > System > Settings > Updates</a>.";
			$upgrade_info['8.11'][] = "You can now specify a different set of order statuses for sellers in <a target='_blank' href='" . $this->url->link('multimerch/suborder-status', 'token=' . $this->session->data['token']) . "'>MultiMerch > Marketplace > Seller order statuses</a> and keep them separate from OpenCart order statuses. For backward compatibility with existing orders, we've copied your existing OpenCart statuses to new seller statuses.";
			$upgrade_info['8.11'][] = "You can now assign different statuses to different logical order states in <a target='_blank' href='" . $this->url->link('module/multimerch', 'token=' . $this->session->data['token'] . '#tab-order') . "'>MultiMerch > System > Settings > Orders</a>. MultiMerch will start using these settings in one of the upcoming updates.";
			$upgrade_info['8.11'][] = "You can now configure MultiMerch to generate seller balance transactions based not only on OpenCart (customer) order statuses, but also on seller order statuses in <a target='_blank' href='" . $this->url->link('module/multimerch', 'token=' . $this->session->data['token'] . '#tab-order') . "'>MultiMerch > System > Settings > Orders</a>.";
			$upgrade_info['8.11'][] = "MultiMerch will now group seller payouts into a new \"payout\" entity for each payout you initiate. For backward compatiblity with past payouts, we've automatically placed all past payout invoices into a default Payout #1 group.";

			$this->_createSchemaEntry('2.8.0.0');
		}

		/****************************************** RELEASE 8.12 ******************************************************/

		if(version_compare($version, '2.9.0.0') < 0) {
			$this->load->model('user/user_group');

			/**********************************************************************************************************/
			/*                             		  	   Discount coupons											  	  */
			/**********************************************************************************************************/

			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'multimerch/coupon');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'multimerch/coupon');

			$this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_coupon` (
			`coupon_id` int(11) NOT NULL AUTO_INCREMENT,
			`seller_id` int(11) DEFAULT NULL,
			`name` varchar(255) NOT NULL DEFAULT '',
			`description` text NOT NULL DEFAULT '',
			`code` varchar(255) NOT NULL DEFAULT '',
			`type` tinyint NOT NULL DEFAULT 1,
			`value` decimal(15,4) NOT NULL DEFAULT 0,
			`date_start` datetime DEFAULT NULL,
			`date_end` datetime DEFAULT NULL,
			`max_uses` int(11) DEFAULT NULL,
			`max_uses_customer` int(11) DEFAULT NULL,
			`total_uses` int(11) NOT NULL DEFAULT 0,
			`min_order_total` decimal(15,4) NOT NULL DEFAULT 0,
			`login_required` tinyint NOT NULL DEFAULT 1,
			`status` tinyint NOT NULL DEFAULT 1,
			`date_created` datetime NOT NULL,
			PRIMARY KEY (`coupon_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_coupon_history` (
			`coupon_history_id` int(11) NOT NULL AUTO_INCREMENT,
			`coupon_id` int(11) NOT NULL,
			`order_id` int(11) NOT NULL,
			`suborder_id` int(11) NOT NULL,
			`customer_id` int(11) NOT NULL,
			`amount` decimal(15,4) NOT NULL,
			`date_created` datetime NOT NULL,
			PRIMARY KEY (`coupon_history_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_coupon_customer` (
			`coupon_customer_id` int(11) NOT NULL AUTO_INCREMENT,
			`coupon_id` int(11) NOT NULL,
			`customer_id` int(11) NOT NULL,
			`exclude` tinyint DEFAULT 0,
			PRIMARY KEY (`coupon_customer_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_coupon_product` (
			`coupon_product_id` int(11) NOT NULL AUTO_INCREMENT,
			`coupon_id` int(11) NOT NULL,
			`product_id` int(11) NOT NULL,
			`exclude` tinyint DEFAULT 0,
			PRIMARY KEY (`coupon_product_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_coupon_oc_category` (
			`coupon_oc_category_id` int(11) NOT NULL AUTO_INCREMENT,
			`coupon_id` int(11) NOT NULL,
			`oc_category_id` int(11) NOT NULL,
			`exclude` tinyint DEFAULT 0,
			PRIMARY KEY (`coupon_oc_category_id`)) default CHARSET=utf8");

			$this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . "ms_coupon_ms_category` (
			`coupon_ms_category_id` int(11) NOT NULL AUTO_INCREMENT,
			`coupon_id` int(11) NOT NULL,
			`ms_category_id` int(11) NOT NULL,
			`exclude` tinyint DEFAULT 0,
			PRIMARY KEY (`coupon_ms_category_id`)) default CHARSET=utf8");

			/**
			 * Create MultiMerch's coupon code in default OpenCart's totals system
			 */
			$this->db->query("INSERT INTO `" . DB_PREFIX . "extension` SET `type` = 'total', `code` = 'ms_coupon'");

			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_allow_seller_coupons',
				'value' => 1
			));

			/**
			 * Convert MultiMerch's approved categories status into active
			 */
			$this->db->query("UPDATE `" . DB_PREFIX . "ms_category` SET category_status = 1 WHERE category_status = 3");

			$upgrade_info['8.12'][] = "Sellers can now create and manage their own discount campaigns through MultiMerch Coupons";

			$this->_createSchemaEntry('2.9.0.0');
		}

		if(version_compare($version, '2.9.0.1') < 0)  {
			$this->load->model('setting/setting');
			$this->load->model('user/user_group');

			$this->model_setting_setting->editSetting('ms_coupon', array(
				'ms_coupon_status' => 1,
				'ms_coupon_sort_order' => 4
			));

			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'total/ms_coupon');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'total/ms_coupon');

			$this->_createSchemaEntry('2.9.0.1');
		}

		/****************************************** RELEASE 8.13 ******************************************************/

		if(version_compare($version, '2.10.0.0') < 0) {
			$sellers = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_seller`");
			foreach ($sellers->rows as $seller){
				if (isset($seller['product_validation'])){
					$this->db->query("
						INSERT IGNORE INTO `" . DB_PREFIX . "ms_seller_setting`
						SET `seller_id` = '" . (int)$seller['seller_id'] . "',
							`name` = 'slr_product_validation',
							`value` = '" . (int)$seller['product_validation'] . "'
					");
				}
			}

			if ($this->checkIfExist(DB_PREFIX . 'ms_seller', 'product_validation')) {
				$this->db->query("ALTER TABLE " . DB_PREFIX . "ms_seller DROP COLUMN product_validation;");
			}

			$this->MsLoader->MsHelper->deleteOCSetting('msconf', 'msconf_product_category_type');

			$upgrade_msg = "Starting with version 8.13, MultiMerch does not require the following files or folders anymore – please remove them from your system manually:";
			$upgrade_msg .= "<ul>";
			$upgrade_msg .= "<li>system/vendor/multimerchlib/module/autogenerated/</li>";
			$upgrade_msg .= "<li>system/vendor/multimerchlib/module/cli/</li>";
			$upgrade_msg .= "<li>system/vendor/multimerchlib/module/extended/</li>";
			$upgrade_msg .= "<li>system/vendor/multimerchlib/module/config/autoload_classmap.php</li>";
			$upgrade_msg .= "<li>system/vendor/multimerchlib/module/init_multimerch.php</li>";
			$upgrade_msg .= "<li>catalog/controller/seller/account-return.php</li>";
			$upgrade_msg .= "<li>catalog/view/theme/default/template/multiseller/account-return-form.tpl</li>";
			$upgrade_msg .= "<li>catalog/view/theme/default/template/multiseller/account-return-info.tpl</li>";
			$upgrade_msg .= "<li>catalog/view/theme/default/template/multiseller/account-return.tpl</li>";
			$upgrade_msg .= "</ul>";

			$upgrade_info['8.13'][] = $upgrade_msg;

			$this->_createSchemaEntry('2.10.0.0');
		}

		/****************************************** RELEASE 8.14 ******************************************************/

		if(version_compare($version, '2.11.0.0') < 0) {
			// Create Google API key setting
			$this->MsLoader->MsHelper->createOCSetting(array(
				'code' => 'msconf',
				'key' => 'msconf_google_api_key',
				'value' => ''
			));

			$this->load->model('user/user_group');

			// Add permissions to MultiMerch reports for all admin user groups
			$user_groups = $this->model_user_user_group->getUserGroups();
			foreach ($user_groups as $user_group) {
				$this->model_user_user_group->addPermission($user_group['user_group_id'], 'access', 'multimerch/report');
				$this->model_user_user_group->addPermission($user_group['user_group_id'], 'modify', 'multimerch/report');
			}

			$this->_createSchemaEntry('2.11.0.0');
		}

		if(version_compare($version, '2.11.1.0') < 0) {
			$deleted_orders = $this->db->query("SELECT DISTINCT mss.order_id FROM `" . DB_PREFIX . "ms_suborder` mss LEFT JOIN (SELECT order_id, order_status_id FROM `" . DB_PREFIX . "order`) o ON (mss.order_id = o.order_id) WHERE o.order_status_id IS NULL");

			foreach ($deleted_orders->rows as $row) {
				$order_id = $row['order_id'];

				// Delete MultiMerch conversations and mesages related to deleted order
				$ms_order_conversations = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_conversation_to_order` WHERE order_id = '" . (int)$order_id . "'");
				foreach ($ms_order_conversations->rows as $row) {
					$this->MsLoader->MsConversation->deleteConversation($row['conversation_id']);
				}

				// Delete MultiMerch order comments
				$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_order_comment` WHERE order_id = '" . (int)$order_id . "'");

				// Delete MultiMerch order product data
				$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_order_product_data` WHERE order_id = '" . (int)$order_id . "'");

				// Delete MultiMerch order product shipping data
				$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_order_product_shipping_data` WHERE order_id = '" . (int)$order_id . "'");

				// Delete MultiMerch invoices for order
				$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_pg_request` WHERE order_id = '" . (int)$order_id . "' AND request_status = '" . (int)MsPgRequest::STATUS_UNPAID . "'");

				// Delete MultiMerch suborders
				$ms_order_suborders = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ms_suborder` WHERE order_id = '" . (int)$order_id . "'");
				foreach ($ms_order_suborders->rows as $row) {
					$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_suborder` WHERE suborder_id = '" . (int)$row['suborder_id'] . "'");
					$this->db->query("DELETE FROM `" . DB_PREFIX . "ms_suborder_history` WHERE suborder_id = '" . (int)$row['suborder_id'] . "'");
				}
			}

			$this->_createSchemaEntry('2.11.1.0');
		}

		return $upgrade_info;
	}

	public function checkIfExist($table, $field){
		$query = $this->db->query("SHOW columns FROM `" . $table . "` WHERE Field = '".  $this->db->escape($field) ."'");
		if ($query->num_rows) {
			return true;
		}
		else {
			return false;
		}
	}

}