<?php
class ControllerMultimerchProductShipping extends Controller {

	public function __construct($registry) {
		parent::__construct($registry);

		// Validate seller shipping is enabled
		if ((int)$this->config->get('msconf_shipping_type') !== 2) {
			return;
		}
	}

	public function index() {
		$data = $this->load->language('multiseller/multiseller');

		$product_id = isset($this->request->get['product_id']) ? $this->request->get['product_id'] : 0;
		$product = MsLoader::getInstance()->MsProduct->getProduct($product_id);

		if (isset($product['shipping']) && (int)$product['shipping'] === 1) {
			// Product is not digital

			// Combined shipping enabled
			if ((int)$this->config->get('msconf_vendor_shipping_type') === 1) {
				$seller_shipping = $this->_getCombinedShipping(MsLoader::getInstance()->MsProduct->getSellerId($product_id));

				if (!empty($seller_shipping)) {
					$data['seller_shipping'] = $seller_shipping;
				}
			}

			// Per-product shipping enabled
			if ((int)$this->config->get('msconf_vendor_shipping_type') === 2) {
				$product_shipping = $this->_getProductShipping($product_id);

				if (!empty($product_shipping)) {
					$data['product_shipping'] = $product_shipping;
				}
			}

			// Both shipping types are enabled
			if ((int)$this->config->get('msconf_vendor_shipping_type') === 3) {

				// Firstly, check per-product shipping that overrides combined rules is available
				$product_shipping = $this->_getProductShipping($product_id, array('override' => 1));

				if (!empty($product_shipping)) {
					$data['product_shipping'] = $product_shipping;
				} else {
					$seller_shipping = $this->_getCombinedShipping(MsLoader::getInstance()->MsProduct->getSellerId($product_id));

					if (!empty($seller_shipping)) {
						$data['seller_shipping'] = $seller_shipping;
					}
				}
			}
		} else {
			// Product is digital
			$data['product_is_digital'] = 1;
		}

		list($template, $children) = $this->MsLoader->MsHelper->loadTemplate('product/mm_shipping');
		$this->response->setOutput($this->load->view($template, array_merge($data, $children)));
	}

	private function _getProductShipping($product_id, $data = array()) {
		$product_shipping = array();

		$params = array('language_id' => $this->config->get('config_language_id'));

		if (isset($data['override'])) {
			$params = array_merge($params, array('override' => $data['override']));
		}

		$product_shipping_data = $this->MsLoader->MsProduct->getProductShipping($product_id, $params);

		if (!empty($product_shipping_data)) {
			$product_shipping_data['processing_time'] = sprintf($this->language->get('mm_product_shipping_processing_days'), $product_shipping_data['processing_time'], ($product_shipping_data['processing_time'] == 1 ? 'day' : 'days'));

			foreach ($product_shipping_data['locations'] as &$location) {
				$location['cost'] = $this->MsLoader->MsHelper->trueCurrencyFormat($location['cost']);
				$location['additional_cost'] = $this->MsLoader->MsHelper->trueCurrencyFormat($location['additional_cost']);

				if ((int)$location['delivery_time_id'] !== 0) {
					$delivery_time_name = MsLoader::getInstance()->MsShippingMethod->getShippingDeliveryTimes(array(
						'delivery_time_id' => $location['delivery_time_id'],
						'language_id' => $this->config->get('config_language_id')
					));
					$location['delivery_time_name'] = isset($delivery_time_name[$location['delivery_time_id']][$this->config->get('config_language_id')]) ? $delivery_time_name[$location['delivery_time_id']][$this->config->get('config_language_id')] : '-';
				} else {
					$location['delivery_time_name'] = $this->language->get('mm_checkout_shipping_ew_location_delivery_time_name');
				}
			}

			$product_shipping = $product_shipping_data;
		}

		return $product_shipping;
	}

	private function _getCombinedShipping($seller_id) {
		$combined_shipping = array();

		if ((int)$seller_id !== 0) {
			// Find seller shipping methods that are applicable for product's weight
			$seller_shipping_data = MsLoader::getInstance()->MsShippingMethod->getSellerShipping($seller_id);

			if (!empty($seller_shipping_data)) {
				$seller_shipping_data['processing_time'] = sprintf($this->language->get('mm_product_shipping_processing_days'), $seller_shipping_data['processing_time'], ($seller_shipping_data['processing_time'] == 1 ? 'day' : 'days'));

				foreach ($seller_shipping_data['methods'] as &$method) {
					$method['cost_fixed'] = $this->MsLoader->MsHelper->trueCurrencyFormat($method['cost_fixed']);
					$method['cost_pwu'] = $this->MsLoader->MsHelper->trueCurrencyFormat($method['cost_pwu']);
					$method['weight_from'] = $this->MsLoader->MsHelper->trueCurrencyFormat($method['weight_from']);
					$method['weight_to'] = $this->MsLoader->MsHelper->trueCurrencyFormat($method['weight_to']);
				}

				$combined_shipping = $seller_shipping_data;
			}
		}

		return $combined_shipping;
	}
}