<?php
class ControllerStartupMultimerchSeoMicrodata extends Controller {

	/**
	 * Get microdata view by route.
	 *
	 * @return string Microdata view.
	 */
	public function index() {
		$microdata_view = '';

		if(isset($this->request->get['route']) && !empty($this->request->get['route'])){
			$route = $this->request->get['route'];
		}else{
			$route = "common/home";
		}

		if($route == "product/product" && isset($this->request->get['product_id']) && $this->request->get['product_id'] != 0){
			$product_data = $this->_getProductData($this->request->get['product_id']);
			if($product_data){
				$microdata_view = $this->load->view('multimerch/seo/microdata/product.tpl', $product_data);
			}
		}

		return $microdata_view;
	}

	/**
	 * Get product data by product id.
	 *
	 * @param int $product_id product id.
	 * @return array $data Product data for view.
	 */
	private function _getProductData($product_id) {
		$data = array();
		$this->load->model('catalog/product');
		$product_info = $this->model_catalog_product->getProduct($product_id);
		if($product_info){
			$data['ms_microdata_url'] = $this->url->link('product/product', 'product_id=' . $product_id);

			// product image
			$this->load->model('tool/image');
			$ms_microdata_original_image = (isset($product_info['image']) and !empty($product_info['image']))?$product_info['image']:$this->config->get('config_logo');
			$data['ms_microdata_popup'] = $this->model_tool_image->resize($ms_microdata_original_image, $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'));

			// product data
			$data['ms_microdata_model'] = $product_info['model'];
			$data['ms_microdata_sku'] = $product_info['sku'];
			$data['ms_microdata_manufacturer'] = $product_info['manufacturer'];
			$data['ms_microdata_description'] = (!empty(strip_tags(html_entity_decode($product_info['description'])))) ? $this->_clearData($product_info['description'],true) : '';
			$data['ms_microdata_name'] = $this->_clearData($product_info['name']);

			//offer data
			$data['ms_microdata_currency_code'] = $this->config->get('config_currency');
			$data['ms_microdata_price'] = $this->tax->calculate($product_info['special']? $product_info['special'] : $product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
			$data['ms_microdata_stock'] = (int)$product_info['quantity'];

			// product seller
			$data['ms_microdata_seller'] = array();
			$seller_id = $this->MsLoader->MsProduct->getSellerId($product_id);
			if ($seller_id){
				$seller = $this->MsLoader->MsSeller->getSeller($seller_id);
				if ($seller){
					$data['ms_microdata_seller']['type'] = 'Organization';
					$data['ms_microdata_seller']['nickname'] = $seller['ms.nickname'];
				}
			}

			// reviews and rating
			$data['ms_microdata_rating'] = array();
			if ($this->config->get('msconf_reviews_enable')) {
				$reviews = $this->MsLoader->MsReview->getReviews(array('product_id' => $product_id));
				$total_reviews = (!empty($reviews)) ? $reviews[0]['total_rows'] : 0;
				if ($total_reviews > 0) {
					$avg_rating = 0;
					foreach ($reviews as $key => $review) {
						$avg_rating += $review['rating'];
					}
					$avg_rating = round($avg_rating / $total_reviews, 1);
					$data['ms_microdata_rating'] = array(
						'ratingValue' => $avg_rating,
						'reviewCount' => $total_reviews
					);
				}
			}
		}

		return $data;
	}

	/**
	 * Clear text for microdata.
	 *
	 * @param string $text Microdata description or name text.
	 * @param bool $decode Flag for decode text.
	 * @return string Clear text for view.
	 */
	private function _clearData($text = '', $decode = false) {
		if(is_string($text)){
			if($decode) $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
			$text = str_replace("><","> <",$text);
			if($text) $text = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', ' ', strip_tags((string)$text));
			$text = str_replace(array(PHP_EOL, "\r\n", "\r", "\n", "\t", '  ', '    ', '    ', '&nbsp;'), ' ', $text);
			$text = str_replace('"', "'", $text);
			$text = str_replace(array("'","\\",'&quot;',"\""), " ", $text);
		}
		return $text;
	}
}
