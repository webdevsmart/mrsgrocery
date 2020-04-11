<?php
final class MsSeller extends Model {
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 2;
	const STATUS_DISABLED = 3;
	const STATUS_DELETED = 4;
	const STATUS_UNPAID = 5;
	const STATUS_INCOMPLETE = 6;
		
	const MS_SELLER_VALIDATION_NONE = 1;
	const MS_SELLER_VALIDATION_ACTIVATION = 2;
	const MS_SELLER_VALIDATION_APPROVAL = 3;

	private $isSeller = FALSE; 
	private $nickname;
	private $description;
	private $company;
	private $country_id;
	private $avatar;
	private $seller_status;

  	public function __construct($registry) {
  		parent::__construct($registry);

  		//$this->log->write('creating seller object: ' . $this->session->data['customer_id']);
		if (isset($this->session->data['customer_id'])) {
			//TODO 
			//$seller_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$this->session->data['customer_id'] . "' AND seller_status = '1'");
			$seller_query = $this->db->query("
				SELECT s.*, md.description as md_description FROM " . DB_PREFIX . "ms_seller s
				LEFT JOIN `" . DB_PREFIX . "ms_seller_description` md
					ON (s.seller_id = md.seller_id AND md.language_id = '" . (int)$this->config->get('config_language_id') . "')
				WHERE s.seller_id = '" . (int)$this->session->data['customer_id'] . "'
				");
			
			if ($seller_query->num_rows) {
				$this->isSeller = TRUE;
				$this->nickname = $seller_query->row['nickname'];
				$this->description = $seller_query->row['md_description'];
				//$this->company = $seller_query->row['company'];
				//$this->country_id = $seller_query->row['country_id'];
				$this->avatar = $seller_query->row['avatar'];
				$this->seller_status = $seller_query->row['seller_status'];
			}
  		}
	}

	private function _dupeSlug($slug) {
		$similarity_query = $this->db->query("SELECT * FROM ". DB_PREFIX . "url_alias WHERE keyword LIKE '" . $this->db->escape($slug) . "%'");
		return ($similarity_query->num_rows > 0 AND $slug) ? $slug . $similarity_query->num_rows : $slug;
	}

  	public function isCustomerSeller($customer_id) {
		$sql = "SELECT COUNT(*) as 'total'
				FROM `" . DB_PREFIX . "ms_seller`
				WHERE seller_id = " . (int)$customer_id;
		
		$res = $this->db->query($sql);
		
		if ($res->row['total'] == 0)
			return FALSE;
		else
			return TRUE;	  		
  	}

	// @todo: think of removing this method and use getSellerFullName instead
	public function getSellerName($seller_id) {
		$sql = "SELECT firstname as 'firstname'
				FROM `" . DB_PREFIX . "customer`
				WHERE customer_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['firstname'];
	}

	public function getSellerFullName($seller_id) {
		$sql = "SELECT CONCAT(firstname, ' ', lastname) as `name`,
				FROM `" . DB_PREFIX . "customer`
				WHERE customer_id = " . (int)$seller_id;

		$res = $this->db->query($sql);

		// @todo: unify language var for deleted customer for admin and front.
		return $res->num_rows && isset($res->row['name']) ? $res->row['name'] : $this->language->get('ms_questions_customer_deleted');
	}

	public function getSellerDescriptions($seller_id) {
		$seller_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "ms_seller_description WHERE seller_id = '" . (int)$seller_id . "'");

		foreach ($query->rows as $result) {
			$seller_description_data[$result['language_id']] = array(
				'description'      => $result['description']
			);
		}

		return $seller_description_data;
	}

	public function getSellerNickname($seller_id) {
		$sql = "SELECT nickname
				FROM `" . DB_PREFIX . "ms_seller`
				WHERE seller_id = " . (int)$seller_id;

		$res = $this->db->query($sql);

		return ($res->rows && isset($res->row['nickname'])) ? $res->row['nickname'] : '';
	}
	
	public function getSellerEmail($seller_id) {
		$sql = "SELECT email as 'email' 
				FROM `" . DB_PREFIX . "customer`
				WHERE customer_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['email'];
	}

	public function updateSellerPositions(){
		//set empty value google geolocation for all sellers
		$this->db->query("INSERT IGNORE INTO `" . DB_PREFIX . "ms_seller_setting` (seller_id, name, value)  
		SELECT DISTINCT seller_id, 'slr_google_geolocation', '' FROM `" . DB_PREFIX . "ms_seller`
		");

		//get all sellers without google geolocation
  		$sellers = $this->db->query("SELECT mss.seller_id, c.name as country_name,
			(SELECT value FROM  `" . DB_PREFIX . "ms_seller_setting` WHERE seller_id = mss.seller_id AND name='slr_city') as city
 			FROM `" . DB_PREFIX . "ms_seller` mss
 			LEFT JOIN `" . DB_PREFIX . "ms_seller_setting` msss ON(msss.seller_id = mss.seller_id AND msss.name = 'slr_google_geolocation')
 			LEFT JOIN `" . DB_PREFIX . "country` c ON(c.country_id = (SELECT value FROM  `" . DB_PREFIX . "ms_seller_setting` WHERE seller_id = mss.seller_id AND name='slr_country'))
 			WHERE msss.value = ''
 			");

		//set google geolocation for all sellers with country
		foreach ($sellers->rows as $seller){
			if (isset($seller['country_name']) AND $seller['country_name']){
				$geo_address = trim((!empty($seller['city']) ? $seller['city'] . ', ' : '') . $seller['country_name']);
				$position = $this->getSellerGoogleGeoLocation($geo_address);
				if ($position){
					$this->db->query("UPDATE " . DB_PREFIX . "ms_seller_setting
							SET	value =  '" .  $this->db->escape($position) . "'
							WHERE seller_id = '" . (int)$seller['seller_id']. "'
							AND name = 'slr_google_geolocation'
							");
				}
			}
		}

		return true;
	}

	public function getSellerGoogleGeoLocation($address){
		$result = false;
		if ($this->config->get('msconf_google_api_key')){
			$user_api_key = $this->config->get('msconf_google_api_key');
			$url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . str_replace(" ", "+", trim($address) ) . '&key=' . $user_api_key;
			if( $curl = curl_init() ) {
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$json_response = json_decode( curl_exec($curl) );
				curl_close($curl);
				if ( isset($json_response->status) AND $json_response->status == 'OK' ) {
					$lat_coord = $json_response->results[0]->geometry->location->lat;
					$lng_coord = $json_response->results[0]->geometry->location->lng;
					$result = '{"lat": '. $lat_coord . ', "lng": ' . $lng_coord . '}';
				}
			}
		}
		return $result;
	}
		
	public function createSeller($data) {
		$avatar = isset($data['avatar_name']) ? $this->MsLoader->MsFile->moveImage($data['avatar_name']) : '';
		$banner = isset($data['banner_name']) ? $this->MsLoader->MsFile->moveImage($data['banner_name']) : '';

		if (isset($data['commission']))
			$commission_id = $this->MsLoader->MsCommission->createCommission($data['commission']);
		
		$sql = "INSERT INTO " . DB_PREFIX . "ms_seller
				SET seller_id = " . (int)$data['seller_id'] . ",
					seller_status = " . (isset($data['status']) ? (int)$data['status'] : self::STATUS_INACTIVE) . ",
					seller_approved = " . (isset($data['approved']) ? (int)$data['approved'] : 0) . ",
					seller_group = " .  (isset($data['seller_group']) ? (int)$data['seller_group'] : $this->config->get('msconf_default_seller_group_id'))  .  ",
					nickname = '" . $this->db->escape($data['nickname']) . "',
					commission_id = " . (isset($commission_id) ? $commission_id : 'NULL') . ",
					avatar = '" . $this->db->escape($avatar) . "',
					banner = '" . $this->db->escape($banner) . "',
					date_created = NOW()";

		$this->db->query($sql);
		$seller_id = $this->db->getLastId();

		if (isset($data['description'])){
			foreach ($data['description'] as $language_id => $value){
				$this->db->query("INSERT INTO " . DB_PREFIX . "ms_seller_description SET
				seller_id = '" . (int)$seller_id . "',
				language_id = '" . (int)$language_id . "',
				description = '" . $this->db->escape($value['description']) . "'");
			}
		}

		//settings block
		if(!empty($data['settings'])) {
			if (!empty($data['settings']['slr_website'])) {
				$data['settings']['slr_website'] = $this->MsLoader->MsHelper->addHttp($data['settings']['slr_website']);
			}

			if (isset($data['settings']['slr_country']) AND $data['settings']['slr_country']){
				$this->load->model('localisation/country');
				$country = $this->model_localisation_country->getCountry($data['settings']['slr_country']);
				if (isset($country['name']) AND $country['name']){
					$geo_address = trim((!empty($data['settings']['slr_city']) ? $data['settings']['slr_city'] . ', ' : '') . $country['name']);
					$position = $this->getSellerGoogleGeoLocation($geo_address);
					if ($position){
						$data['settings']['slr_google_geolocation'] = $position;
					}
				}
			}

			$this->MsLoader->MsSetting->createSellerSetting($data);
		}
		//end settings block

		// badges
		if (isset($data['badges'])) {
			foreach ($data['badges'] as $k => $badge_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "ms_badge_seller_group (badge_id, seller_id) VALUES (" . (int)$badge_id.",".(int)$seller_id . ")");
			}
		}

		if (!isset($data['keyword']) OR !$data['keyword']){
			$data['keyword'] = $this->MsLoader->MsHelper->slugify($data['nickname']);
		}

		$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'seller_id=" . (int)$seller_id . "', keyword = '" . $this->db->escape($this->_dupeSlug($data['keyword'])) . "'");

		$this->cache->delete('multimerch_seo_url');
	}
	
	public function nicknameTaken($nickname) {
		$sql = "SELECT nickname
				FROM `" . DB_PREFIX . "ms_seller` p
				WHERE p.nickname = '" . $this->db->escape($nickname) . "'";
		
		$res = $this->db->query($sql);

		return $res->num_rows;
	}
	
	public function editSeller($data) {
		$seller_id = (int)$data['seller_id'];

		$old_avatar = $this->getSellerAvatar($seller_id);
		
		if (!isset($data['avatar_name']) || ($old_avatar['avatar'] != $data['avatar_name'])) {
			$this->MsLoader->MsFile->deleteImage($old_avatar['avatar']);
		}
		
		if (isset($data['avatar_name'])) {
			if ($old_avatar['avatar'] != $data['avatar_name']) {			
				$avatar = $this->MsLoader->MsFile->moveImage($data['avatar_name']);
			} else {
				$avatar = $old_avatar['avatar'];
			}
		} else {
			$avatar = '';
		}

		$old_banner = $this->getSellerBanner($seller_id);

		if (!isset($data['banner_name']) || ($old_banner['banner'] != $data['banner_name'])) {
			$this->MsLoader->MsFile->deleteImage($old_banner['banner']);
		}

		if (isset($data['banner_name'])) {
			if ($old_banner['banner'] != $data['banner_name']) {
				$banner = $this->MsLoader->MsFile->moveImage($data['banner_name']);
			} else {
				$banner = $old_banner['banner'];
			}
		} else {
			$banner = '';
		}

		$sql = "UPDATE " . DB_PREFIX . "ms_seller SET
					nickname = '" . $this->db->escape($data['nickname'])  . "',"
					. (isset($data['status']) ? "seller_status=  " .  (int)$data['status'] . "," : '')
					. (isset($data['seller_group']) ? "seller_group=  " .  (int)$data['seller_group'] . "," : '')
					. (isset($data['approved']) ? "seller_approved=  " .  (int)$data['approved'] . "," : '')
					. "banner = '" . $this->db->escape($banner) . "',
					avatar = '" . $this->db->escape($avatar) . "'
				WHERE seller_id = " . (int)$seller_id;
		
		$this->db->query($sql);

		if (isset($data['description'])){
			foreach ($data['description'] as $language_id => $value){
				foreach ($data['description'] as $language_id => $value) {
					$sql = "INSERT INTO " . DB_PREFIX . "ms_seller_description SET
						seller_id = " . (int)$seller_id . ",
						description = '" . $this->db->escape($value['description']) . "',
						language_id = '" . (int)$language_id . "'
					ON DUPLICATE KEY UPDATE
						description = '" . $this->db->escape($value['description']) . "'";

					$this->db->query($sql);
				}
			}
		}
	}
		
	public function getSellerAvatar($seller_id) {
		$query = $this->db->query("SELECT avatar as avatar FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$seller_id . "'");
		
		return $query->row;
	}
    
	public function getSellerBanner($seller_id) {
		$query = $this->db->query("SELECT banner as banner FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$seller_id . "'");

		return $query->row;
	}
		
  	public function getNickname() {
  		return $this->nickname;
  	}

  	public function getCompany() {
  		return $this->company;
  	}
  	
  	public function getCountryId() {
  		return $this->country_id;
  	}

  	public function getDescription() {
  		return $this->description;
  	}
  	
  	public function getStatus() {
  		return $this->seller_status;
  	}
  	
  	public function isSeller() {
  		return $this->isSeller;
  	}
	
	public function getSalt($seller_id) {
		$sql = "SELECT salt
				FROM `" . DB_PREFIX . "customer`
				WHERE customer_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);
		
		return $res->row['salt'];		
	}
	

	public function adminEditSeller($data) {
		$seller_id = (int)$data['seller_id'];

		// badges
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_badge_seller_group WHERE seller_id = " . (int)$seller_id);
		if (isset($data['badges'])) {
			foreach ($data['badges'] as $k => $badge_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "ms_badge_seller_group (badge_id, seller_id) VALUES (" . (int)$badge_id.",".(int)$seller_id . ")");
			}
		}

        //settings block
        if(!empty($data['settings'])) {
			if (!empty($data['settings']['slr_website'])) {
				$data['settings']['slr_website'] = $this->MsLoader->MsHelper->addHttp($data['settings']['slr_website']);
			}

			if (
				($data['settings']['slr_country'] AND $data['settings']['slr_country'] != $data['settings']['slr_country_old']) OR
				($data['settings']['slr_city'] AND $data['settings']['slr_city'] != $data['settings']['slr_city_old'])
			){
				$this->load->model('localisation/country');
				$country = $this->model_localisation_country->getCountry($data['settings']['slr_country']);
				if (isset($country['name']) AND $country['name']){
					$geo_address = trim((!empty($data['settings']['slr_city']) ? $data['settings']['slr_city'] . ', ' : '') . $country['name']);
					$position = $this->getSellerGoogleGeoLocation($geo_address);
					if ($position){
						$data['settings']['slr_google_geolocation'] = $position;
					}
				}
			}

            $this->MsLoader->MsSetting->createSellerSetting($data);
        }
        //end settings block

		// commissions
		if (!$data['commission_id']) {
			$commission_id = $this->MsLoader->MsCommission->createCommission($data['commission']);
		} else {
			$commission_id = $this->MsLoader->MsCommission->editCommission($data['commission_id'], $data['commission']);
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'seller_id=" . (int)$seller_id. "'");

		if (isset($data['keyword']) AND $data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'seller_id=" . (int)$seller_id . "', keyword = '" . $this->db->escape($this->_dupeSlug($data['keyword'])) . "'");
		}

		$sql = "UPDATE " . DB_PREFIX . "ms_seller SET
					seller_status = '" .  (int)$data['status'] .  "',
					seller_approved = '" .  (int)$data['approved'] .  "',
					commission_id = " . (!is_null($commission_id) ? (int)$commission_id : 'NULL' ) . ",
					seller_group = '" .  (int)$data['seller_group'] .  "'
				WHERE seller_id = " . (int)$seller_id;
		
		$this->db->query($sql);

		if (isset($data['description'])){
			foreach ($data['description'] as $language_id => $value){
				foreach ($data['description'] as $language_id => $value) {
					$sql = "INSERT INTO " . DB_PREFIX . "ms_seller_description SET
						seller_id = " . (int)$seller_id . ",
						description = '" . $this->db->escape($value['description']) . "',
						language_id = '" . (int)$language_id . "'
					ON DUPLICATE KEY UPDATE
						description = '" . $this->db->escape($value['description']) . "'";

					$this->db->query($sql);
				}
			}
		}

		$this->cache->delete('multimerch_seo_url');
	}
	
	/********************************************************/
	
	
	public function getTotalSellers($data = array()) {
		$sql = "
			SELECT COUNT(*) as total
			FROM " . DB_PREFIX . "ms_seller ms
			WHERE 1 = 1 "
			. (isset($data['seller_status']) ? " AND seller_status IN  (" .  $this->db->escape(implode(',', $data['seller_status'])) . ")" : '');

		$res = $this->db->query($sql);

		return $res->row['total'];
	}
	
	public function getSeller($seller_id, $data = array()) {
		$sql = "SELECT	CONCAT(c.firstname, ' ', c.lastname) as name,
						c.email as 'c.email',
						ms.seller_id as 'seller_id',
						ms.nickname as 'ms.nickname',
						ms.seller_status as 'ms.seller_status',
						ms.seller_approved as 'ms.seller_approved',
						ms.date_created as 'ms.date_created',
						ms.avatar as 'ms.avatar',
						ms.banner as 'banner',
						md.description as 'ms.description',
						ms.commission_id as 'ms.commission_id',
						ms.seller_group as 'ms.seller_group',
						(SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE `query` = 'seller_id=" . (int)$seller_id . "' LIMIT 1) AS keyword
				FROM `" . DB_PREFIX . "customer` c
				INNER JOIN `" . DB_PREFIX . "ms_seller` ms
					ON (c.customer_id = ms.seller_id)
				LEFT JOIN `" . DB_PREFIX . "ms_product` mp
					ON (c.customer_id = mp.seller_id)
				LEFT JOIN `" . DB_PREFIX . "ms_seller_description` md
					ON (c.customer_id = md.seller_id AND md.language_id = '" . (int)$this->config->get('config_language_id') . "')
				WHERE ms.seller_id = " .  (int)$seller_id
				. (isset($data['product_id']) ? " AND mp.product_id =  " .  (int)$data['product_id'] : '')
				. (isset($data['seller_status']) ? " AND seller_status IN  (" .  $this->db->escape(implode(',', $data['seller_status'])) . ")" : '')
				. " GROUP BY ms.seller_id
				LIMIT 1";
				
		$res = $this->db->query($sql);
		if (!isset($res->row['seller_id']) || !$res->row['seller_id']) {
			return FALSE;
		} else {
			$res->row['descriptions'] = $this->getSellerDescriptions($res->row['seller_id']);
			$res->row['product_validation'] = $this->MsLoader->MsSetting->calculateSellerSettingValue($res->row['seller_id'], 'slr_product_validation', 'slr_gr_product_validation');

			return $res->row;
		}
	}
	
	public function getSellers($data = array(), $sort = array(), $cols = array()) {
		$hFilters = $wFilters = '';
		if(isset($sort['filters'])) {
			$cols = array_merge($cols, array("`c.name`" => 1, "`ms.date_created`" => 1));
			foreach($sort['filters'] as $k => $v) {
				if (!isset($cols[$k])) {
					$wFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				} else {
					$hFilters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
				}
			}
		}
		
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS"
					// additional columns
					. (isset($cols['total_products']) ? "
						(SELECT COUNT(*) FROM " . DB_PREFIX . "product p
						LEFT JOIN " . DB_PREFIX . "ms_product mp USING (product_id)
						LEFT JOIN " . DB_PREFIX . "ms_seller USING (seller_id)
						WHERE seller_id = ms.seller_id) as total_products,
					" : "")
					
					. (isset($cols['current_balance']) ? "
						(SELECT COALESCE(
							(SELECT balance FROM " . DB_PREFIX . "ms_balance
								WHERE seller_id = ms.seller_id  
								ORDER BY balance_id DESC
								LIMIT 1
							),
							0
						)) as current_balance,
					" : "")

					. (isset($cols['total_sales']) ? "
						(SELECT count(*) as total FROM `" . DB_PREFIX . "ms_suborder` mss
						LEFT JOIN (SELECT order_id, order_status_id FROM `" . DB_PREFIX . "order`) o
							ON (mss.order_id = o.order_id)
						WHERE mss.seller_id = ms.seller_id AND o.order_status_id <> 0) as total_sales,
					" : "")

					// default columns
					." CONCAT(c.firstname, ' ', c.lastname) as 'c.name',
					c.email as 'c.email',
					ms.seller_id as 'seller_id',
					ms.nickname as 'ms.nickname',
					ms.seller_status as 'ms.seller_status',
					ms.seller_approved as 'ms.seller_approved',
					ms.date_created as 'ms.date_created',
					ms.avatar as 'ms.avatar',
					ms.banner as 'banner',
					md.description as 'ms.description'
				FROM `" . DB_PREFIX . "customer` c
				INNER JOIN `" . DB_PREFIX . "ms_seller` ms
					ON (c.customer_id = ms.seller_id)
				LEFT JOIN `" . DB_PREFIX . "ms_seller_description` md
					ON (c.customer_id = md.seller_id AND md.language_id = '" . (int)$this->config->get('config_language_id') . "')
				WHERE 1 = 1 "
				. (isset($data['seller_id']) ? " AND ms.seller_id =  " .  (int)$data['seller_id'] : '')
				. (isset($data['seller_group_id']) ? " AND ms.seller_group =  " .  (int)$data['seller_group_id'] : '')
				. (isset($data['seller_status']) ? " AND seller_status IN  (" .  $this->db->escape(implode(',', $data['seller_status'])) . ")" : '')
				
				. $wFilters
				
				. " GROUP BY ms.seller_id HAVING 1 = 1 "
				
				. $hFilters
				
				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
				. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);
		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->rows) $res->rows[0]['total_rows'] = $total->row['total'];
		
		return $res->rows;
	}
	
	public function getCustomers($sort = array()) {
		$sql = "SELECT  CONCAT(c.firstname, ' ', c.lastname) as 'c.name',
						c.email as 'c.email',
						c.customer_id as 'c.customer_id',
						ms.seller_id as 'seller_id'
				FROM `" . DB_PREFIX . "customer` c
				LEFT JOIN `" . DB_PREFIX . "ms_seller` ms
					ON (c.customer_id = ms.seller_id)
				WHERE ms.seller_id IS NULL"
				. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
    			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);
		
		return $res->rows;
	}
	
	public function getTotalEarnings($seller_id, $data = array()) {
		// note: update getSellers() if updating this
		$sql = "SELECT COALESCE(SUM(amount),0)
					   - (SELECT COALESCE(ABS(SUM(amount)),0)
						  FROM `" . DB_PREFIX . "ms_balance`
					 	  WHERE seller_id = " . (int)$seller_id . "
						  AND balance_type = ". MsBalance::MS_BALANCE_TYPE_REFUND
						  . (isset($data['period_start']) ? " AND DATEDIFF(date_created, '{$data['period_start']}') >= 0" : "")
				. ") as total
				FROM `" . DB_PREFIX . "ms_balance`
				WHERE seller_id = " . (int)$seller_id . "
				AND balance_type IN (". implode(',', array(MsBalance::MS_BALANCE_TYPE_SALE, MsBalance::MS_BALANCE_TYPE_SHIPPING)) . ")"
				. (isset($data['period_start']) ? " AND DATEDIFF(date_created, '{$data['period_start']}') >= 0" : "");

		$res = $this->db->query($sql);
		return $res->row['total'];
	}
	
	public function changeStatus($seller_id, $seller_status) {
		$sql = "UPDATE " . DB_PREFIX . "ms_seller
				SET	seller_status =  " .  (int)$seller_status . "
				WHERE seller_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);
	}
	
	public function changeApproval($seller_id, $approved) {
		$sql = "UPDATE " . DB_PREFIX . "ms_seller
				SET	approved =  " .  (int)$approved . "
				WHERE seller_id = " . (int)$seller_id;
		
		$res = $this->db->query($sql);
	}
	
	public function deleteSeller($seller_id) {
		// Change status of all related products to DELETED
		$products = $this->MsLoader->MsProduct->getProducts(array('seller_id' => $seller_id));
		foreach ($products as $product) {
			$this->MsLoader->MsProduct->changeStatus($product['product_id'], MsProduct::STATUS_DELETED);

			// Delete all fixed-shipping settings for these products
			$this->db->query("DELETE FROM " . DB_PREFIX . "ms_product_shipping WHERE product_id = '" . (int)$product['product_id'] . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "ms_product_shipping_location WHERE product_id = '" . (int)$product['product_id'] . "'");
		}

		// Delete all seller's settings
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_seller_setting WHERE seller_id = '" . (int)$seller_id . "'");

		// Delete seller's social channels information
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_seller_channel WHERE seller_id = '" . (int)$seller_id . "'");

		// Delete seller's description
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_seller_description WHERE seller_id = '" . (int)$seller_id . "'");

		// Delete seller's combined-shipping settings
		$seller_shipping = $this->db->query("SELECT seller_shipping_id FROM " . DB_PREFIX . "ms_seller_shipping WHERE seller_id = '" . (int)$seller_id . "'");
		if($seller_shipping->num_rows) {
			foreach ($seller_shipping->rows as $row) {
				if(isset($row['seller_shipping_id'])) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "ms_seller_shipping_location WHERE seller_shipping_id = '" . (int)$row['seller_shipping_id'] . "'");
				}
			}
		}
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_seller_shipping WHERE seller_id = '" . (int)$seller_id . "'");

		// Delete seller's badges
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_badge_seller_group WHERE seller_id = " . (int)$seller_id);

		// Delete seller's balance records
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_balance WHERE seller_id = '" . (int)$seller_id . "'");

		// Delete seller's payment requests and payments
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_pg_payment WHERE seller_id = '" . (int)$seller_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_pg_request WHERE seller_id = '" . (int)$seller_id . "'");

		// Delete seller's MsAttribute groups
		$seller_attribute_groups = $this->MsLoader->MsAttribute->getAttributeGroups(array('seller_ids' => $seller_id));
		foreach ($seller_attribute_groups as $seller_attribute_group) {
			$this->MsLoader->MsAttribute->deleteAttributeGroup($seller_attribute_group['attribute_group_id']);
		}

		// Delete seller's MsAttributes (just in case)
		$seller_attributes = $this->MsLoader->MsAttribute->getAttributes(array('seller_ids' => $seller_id));
		foreach ($seller_attributes as $seller_attribute) {
			$this->MsLoader->MsAttribute->deleteAttribute($seller_attribute['attribute_id']);
		}

		// Delete seller's MsCategories
		$seller_categories = $this->MsLoader->MsCategory->getCategories(array('seller_ids' => $seller_id));
		foreach ($seller_categories as $seller_category) {
			$this->MsLoader->MsCategory->deleteCategory($seller_category['category_id']);
		}

		// Delete seller's SEO url
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE `query` = 'seller_id=".(int)$seller_id."'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "ms_seller WHERE seller_id = '" . (int)$seller_id . "'");

		$this->cache->delete('multimerch_seo_url');
	}

	public function getSellerMsCategories($seller_id) {
		$sql = "SELECT
					msc.category_id,
					msc.parent_id,
					mscd.name,
					COUNT(DISTINCT msp2c.product_id) AS total
				FROM " . DB_PREFIX . "ms_category msc
				LEFT JOIN " . DB_PREFIX . "ms_category_description mscd
					USING (category_id)
				LEFT JOIN " . DB_PREFIX . "ms_product_to_category msp2c
					ON (msc.category_id = msp2c.ms_category_id)
				LEFT JOIN " . DB_PREFIX . "ms_product msp
					ON (msp2c.product_id = msp.product_id)
				WHERE msp.seller_id = '" . (int)$seller_id . "'
					AND mscd.language_id = '" . (int)$this->config->get('config_language_id') . "'
					AND msp.product_status = '" . MsProduct::STATUS_ACTIVE . "'
				GROUP BY msp2c.ms_category_id";

		$res = $this->db->query($sql);

		foreach ($res->rows as &$row) {
			$row['path'] = $this->MsLoader->MsCategory->getMsCategoryPath($row['category_id']);
		}

		return $res->rows;
	}

	public function getSellerCustomers($seller_id, $data = array()) {
		$result = $this->db->query("
			SELECT DISTINCT
				o.customer_id,
				CONCAT_WS(' ', c.firstname, c.lastname) as `customer_name`
			FROM `" . DB_PREFIX . "ms_suborder` mss
			LEFT JOIN (SELECT order_id, customer_id FROM `" . DB_PREFIX . "order`) o
				ON (o.order_id = mss.order_id)
			LEFT JOIN (SELECT customer_id, firstname, lastname FROM `" . DB_PREFIX . "customer`) c
				ON (c.customer_id = o.customer_id)
			WHERE mss.seller_id = " . (int)$seller_id

			. (isset($data['name']) ? " AND `customer_name` = '" . $this->db->escape($data['name']) . "'" : "")
		);

		return $result->rows;
	}
}
