<?php
class ControllerMultimerchCartCoupon extends Controller {
	private $data = array();

	public function __construct($registry) {
		parent::__construct($registry);
		$this->registry = $registry;
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));

		$this->load->model('total/ms_coupon');

		// Validate MultiMerch coupons are enabled
		if ($this->config->get('msconf_allow_seller_coupons')) {
			return;
		}
	}

	public function index() {
		$cart_data = $this->model_total_ms_coupon->getCartData();

		foreach ($cart_data as $seller_id => $data) {
			$seller_coupons = $this->MsLoader->MsCoupon->getCoupons(array(
				'seller_id' => $seller_id,
				'status' => MsCoupon::STATUS_ACTIVE
			));

			foreach ($seller_coupons as $seller_coupon) {
				$coupon_validation = $this->model_total_ms_coupon->validateCouponUsage($seller_coupon, $data);

				if ($coupon_validation['is_available']) {
					$this->data['sellers'][$seller_id] = array(
						'seller_id' => $seller_id,
						'nickname' => $this->MsLoader->MsSeller->getSellerNickname($seller_id),
						'coupon' => isset($this->session->data['ms_coupons'][$seller_id]) ? $this->session->data['ms_coupons'][$seller_id] : '',
					);
				}
			}
		}

		return $this->load->view('multimerch/checkout/cart_coupon', $this->data);
	}

	public function apply() {
		$json = array();

		$coupons = array();

		//unset($this->session->data['ms_coupons']);
		if (!empty($this->request->post['coupons'])) {
			$coupons = $this->request->post['coupons'];
		} else {
			$json['error'] = $this->language->get('ms_cart_coupon_error_empty');
		}

		$cart_data = $this->model_total_ms_coupon->getCartData();

		$success_msg = $error_msg = '';
		foreach ($coupons as $seller_id => $code) {
			$coupon_info = $this->MsLoader->MsCoupon->getCoupons(array('seller_id' => $seller_id, 'code' => $code));
			$seller_nickname = $this->MsLoader->MsSeller->getSellerNickname($seller_id);

			if ($coupon_info && isset($cart_data[$seller_id])) {
				$coupon_validation = $this->model_total_ms_coupon->validateCouponUsage($coupon_info, $cart_data[$seller_id]);

				if ($coupon_validation['is_available']) {
					$this->session->data['ms_coupons'][$coupon_info['seller_id']] = $code;

					$success_msg .= sprintf($this->language->get('ms_cart_coupon_success_applied'), $seller_nickname) . "<BR>";
				} else {
					$error_msg .= sprintf($this->language->get('ms_cart_coupon_error_apply'), $seller_nickname) . "<BR>";
				}
			} else {
				$error_msg .= sprintf($this->language->get('ms_cart_coupon_error_apply'), $seller_nickname) . "<BR>";
			}

			if ($success_msg && !$error_msg) {
				$this->session->data['success'] = $success_msg;
				$json['redirect'] = $this->url->link('checkout/cart');
			} elseif ($error_msg) {
				$json['error'] = $error_msg;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}