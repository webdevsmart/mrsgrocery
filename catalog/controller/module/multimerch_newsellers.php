<?php
class ControllerModuleMultiMerchNewsellers extends ControllerSellerCatalog {
	public function index($setting) {
		$this->load->model('localisation/country');
		$this->data = array_merge($this->data, $this->load->language('module/multimerch_newsellers'));
		$this->data['heading_title'] = $this->language->get('ms_newsellers_sellers');
		if (isset($setting['limit']) && (int)$setting['limit'] > 0)
			$this->data['limit'] = (int)$setting['limit'];
		else
			$this->data['limit'] = 3;

		if (!isset($setting['width']) || (int)$setting['width'] <= 0)
			$setting['width'] = $this->config->get('config_image_category_width');

		if (!isset($setting['height']) || (int)$setting['height'] <= 0)
			$setting['height'] = $this->config->get('config_image_category_height');

		$this->data['sellers_href'] = $this->url->link('seller/catalog-seller');
		$this->data['sellers'] = array();

		$results = $this->MsLoader->MsSeller->getSellers(
			array(
				'seller_status' => array(MsSeller::STATUS_ACTIVE)
			),
			array(
				'order_by'               => 'ms.date_created',
				'order_way'              => 'DESC',
				'offset'              => 0,
				'limit'              => $this->data['limit']
			)
		);

		foreach ($results as $result) {
			$banner = '';
			if ($this->config->get('msconf_enable_seller_banner')) {
				if ($result['banner'] && file_exists(DIR_IMAGE . $result['banner'])) {
					$banner = $this->MsLoader->MsFile->resizeImage($result['banner'], $this->config->get('msconf_product_seller_banner_width'), $this->config->get('msconf_product_seller_banner_height'), 'w');
				}
			}
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
				),
				array(
					'order_by'	=> 'pd.name',
					'order_way'	=> 'ASC',
					'offset'	=> 0,
					'limit'		=> 3
				)
			);

			$total_products = isset($products[0]['total_rows']) ? $products[0]['total_rows'] : 0;

			foreach($products as $key=>$product) {
				$image = $this->MsLoader->MsFile->resizeImage($product['p.image'] && file_exists(DIR_IMAGE . $product['p.image']) ? $product['p.image'] : 'no_image.png', 100, 100);
				$product['href'] = $this->url->link('product/product', 'product_id=' . $product['product_id']);
				$product['p.image'] = $image;

				$products[$key] = $product;
			}

			$this->data['sellers'][] = array(
				'seller_id'  => $result['seller_id'],
				'nickname'        => $result['ms.nickname'],
				'href'        => $this->url->link('seller/catalog-seller/profile','seller_id=' . $result['seller_id']),
				'products_href' => $this->url->link('seller/catalog-seller/products', 'seller_id=' . $result['seller_id']),
				'thumb' => !empty($result['ms.avatar']) && file_exists(DIR_IMAGE . $result['ms.avatar']) ? $this->MsLoader->MsFile->resizeImage($result['ms.avatar'], $setting['width'], $setting['height']) : $this->MsLoader->MsFile->resizeImage('ms_no_image.jpg', $setting['width'], $setting['height']),
				'banner' => $banner,
				'settings' => $settings,
				'products' => $products,
				'total_products' => $total_products
			);
		}

		return $this->load->view('module/multimerch_newsellers.tpl', $this->data);
	}
}