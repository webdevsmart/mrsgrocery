<?php
class MsMail extends Model {
	public function __construct($registry) {
		parent::__construct($registry);
		$this->errors = array();
	}

	private function _modelExists($model) {
		$file  = DIR_APPLICATION . 'model/' . $model . '.php';
		return file_exists($file);
	}

	private function _getOrderProducts($order_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "order_product
				WHERE order_id = " . (int)$order_id;
		
		$res = $this->db->query($sql);

		return $res->rows;
	}

	public function sendOrderMails($order_id) {
		print_r('<-----------msmail info--------------------->');
		$serviceLocator = $this->MsLoader->load('\MultiMerch\Module\MultiMerch')->getServiceLocator();
		

		$mailTransport = $serviceLocator->get('MailTransport');
		$mails = new \MultiMerch\Mail\Message\MessageCollection();
		$order_products = $this->_getOrderProducts($order_id);
		
		if (!$order_products) {	
			return false;
		}

		if ($this->_modelExists('checkout/order')) {
			// catalog
			$this->load->model('checkout/order');
			$this->load->model('account/order');
			$order_info = $this->model_checkout_order->getOrder($order_id);
		} else {
			// admin
			$this->load->model('sale/order');
			$order_info = $this->model_sale_order->getOrder($order_id);
		}
		$this->load->model('tool/upload');
		foreach ($order_products as $product) {
			$seller_id = $this->MsLoader->MsProduct->getSellerId($product['product_id']);
			if ($seller_id) {
				/** @see \MsOrderData::getOrderProducts */
				$orderSellerProducts = $this->MsLoader->MsOrderData->getOrderProducts(array('order_id' => $order_id, 'seller_id' => $seller_id));
				foreach ($orderSellerProducts as $oSkey => $oSp) {
					if ($this->_modelExists('account/order')) {
						$options = $this->model_account_order->getOrderOptions($order_id, $oSp['order_product_id']);
					} else {
						$options = $this->model_sale_order->getOrderOptions($order_id, $oSp['order_product_id']);
					}
					$option_data = array();
					foreach ($options as $option)
					{
						if ($option['type'] == 'file') {
							$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);
							if ($upload_info) {
								$option['value']	=  $upload_info['name'];
							}else{
								$option['value'] = '';
							}
						}
						$option_data[] = $option;
					}
					$orderSellerProducts[$oSkey]['order_options'] = $option_data;
				}

				$total = $this->MsLoader->MsOrderData->getOrderTotal($order_id, array('seller_id' => $seller_id));

				$order_info['comment'] = $this->MsLoader->MsOrderData->getOrderComment(array('order_id' => $order_id, 'seller_id' => $seller_id));
				$MailProductPurchased = $serviceLocator->get('MailProductPurchased', false)
					->setTo($this->MsLoader->MsSeller->getSellerEmail($seller_id))
					->setData(array(
						'addressee' => $this->MsLoader->MsSeller->getSellerName($seller_id),
						'order_products' => $orderSellerProducts,
						'total' => $total,
						'order_info' => $order_info,
					));
				$mails->add($MailProductPurchased);
			}
		}
		$seller_id = $this->MsLoader->MsProduct->getSellerId(3457);
		print_r('<-----------msmail info--------------------->');
		print_r($seller_id.'br');
		print_r('order_id:'.$order_id.'br');
		//print_r('mails:'.$mails);
		$order_info['comment'] = $this->MsLoader->MsOrderData->getOrderComment(array('order_id' => $order_id, 'seller_id' => $seller_id));
		print_r($order_info);

		$mailTransport->sendMails($mails);

		die();
	}
}
?>
