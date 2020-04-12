<?php
class ControllerSellerCatalog extends Controller {
	public  $data = array();

	public function __construct($registry) {
		parent::__construct($registry);
		$this->MsLoader->MsHelper->addStyle('multiseller');
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'),$this->language->load('product/product'));
	}

	public function jxGetMapSellers() {
		$json = array();
		$this->load->model('localisation/country');
		$results = $this->MsLoader->MsSeller->getSellers(
			array(
				'seller_status' => array(MsSeller::STATUS_ACTIVE)
			)
		);

		foreach ($results as $result) {
			$avatar = $this->MsLoader->MsFile->resizeImage($result['ms.avatar'] && file_exists(DIR_IMAGE . $result['ms.avatar']) ? $result['ms.avatar'] : 'ms_no_image.jpg', $this->config->get('msconf_seller_avatar_seller_list_image_width'), $this->config->get('msconf_seller_avatar_seller_list_image_height'));
			$seller_settings = $this->MsLoader->MsSetting->getSellerSettings(array('seller_id' => $result['seller_id']));
			$defaults = $this->MsLoader->MsSetting->getSellerDefaults();
			$settings = array_merge($defaults, $seller_settings);

			$country = $this->model_localisation_country->getCountry($settings['slr_country']);
			$settings['slr_country'] = (isset($country['name']) ? $country['name'] : '');

			$products = $this->MsLoader->MsProduct->getProducts(
				array(
					'seller_id' => $result['seller_id'],
					'language_id' => $this->config->get('config_language_id'),
					'product_status' => array(MsProduct::STATUS_ACTIVE),
					'oc_status' => 1
				)
			);

			$total_products = isset($products[0]['total_rows']) ? $products[0]['total_rows'] : 0;

			$json['sellers'][] = array(
				'seller_id' => $result['seller_id'],
				'thumb' => $avatar,
				'nickname' => $result['ms.nickname'],
				'description' => utf8_substr(strip_tags(html_entity_decode($result['ms.description'], ENT_QUOTES, 'UTF-8')), 0, 200) . '..',
				'href' => $this->url->link('seller/catalog-seller/profile', '&seller_id=' . $result['seller_id']),
				'products_href' => $this->url->link('seller/catalog-seller/products', 'seller_id=' . $result['seller_id']),
				'address' => trim($settings['slr_city'] . ', ' . $settings['slr_country'], ','),
				'position' => $settings['slr_google_geolocation'],
				'website' => $settings['slr_website'],
				'total_products' => $total_products
			);
		}

		$this->response->setOutput(json_encode($json));
	}
}
?>