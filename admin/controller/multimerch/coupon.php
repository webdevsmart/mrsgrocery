<?php

class ControllerMultimerchCoupon extends ControllerMultimerchBase {

	public function index() {
		$this->validate(__FUNCTION__);

		$this->document->addScript('view/javascript/multimerch/coupon.js');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$this->data['token'] = $this->session->data['token'];

		$this->data['heading'] = $this->language->get('ms_coupon_heading');
		$this->document->setTitle($this->language->get('ms_coupon_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_coupon_breadcrumbs'),
				'href' => $this->url->link('multimerch/coupon', '', 'SSL'),
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/coupon', $this->data));
	}


	/* ============================================== COUPONS ======================================================= */


	public function getCouponTableData() {
		$colMap = array(
			'date_created' => 'msc.date_created',
			'name' => 'msc.`name`',
			'code' => 'msc.`code`',
			'seller' => 'mss.nickname',
			'type' => 'msc.`type`',
			'value' => 'msc.`value`',
			'total_uses' => 'msc.`total_uses`',
			'max_uses' => 'msc.`max_uses`',
			'date_start' => 'msc.`date_start`',
			'date_end' => 'msc.`date_end`',
			'status' => 'msc.`status`'
		);

		$sorts = array('date_created', 'name', 'code', 'seller', 'value', 'total_uses', 'date_start', 'date_end', 'status');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsCoupon->getCoupons(
			array(),
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				'filters' => $filterParams,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength']
			)
		);

		$total = isset($results[0]) ? $results[0]['total_rows'] : 0;

		$columns = array();
		foreach ($results as $result) {
			// status
			$status = "";
			if(isset($result['status'])) {
				$status .= "<p style='color: ";

				if($result['status'] == MsCoupon::STATUS_ACTIVE) $status .= "green";
				if($result['status'] == MsCoupon::STATUS_DISABLED) $status .= "red";

				$status .= "'>" . $this->language->get('ms_custom_field_status_' . $result['status']) . "</p>";
			}

			// actions
			$actions = "";
			$actions .= "<button type='button' class='btn btn-danger ms-delete' title='".$this->language->get('button_delete')."' data-id='" . $result['coupon_id'] . "' data-referrer='coupon'><i class='fa fa-trash-o''></i></button>";

			$columns[] = array_merge(
				$result,
				array(
					'checkbox' => "<input type='checkbox' name='selected[]' value='{$result['coupon_id']}' />",
					'date_created' => date($this->language->get('date_format_short'), strtotime($result['date_created'])),
					'name' => $result['name'],
					'code' => $result['code'],
					'seller' => $result['seller'],
					'value' => (int)$result['type'] === (int)MsCoupon::TYPE_DISCOUNT_PERCENT ? round($result['value'], 2) . "%" : $this->currency->format($result['value'], $this->config->get('config_currency')),
					'total_uses' => $result['total_uses'] . "/" . $result['max_uses'],
					'date_start' => date($this->language->get('date_format_short'), strtotime($result['date_start'])),
					'date_end' => $result['date_end'] ? date($this->language->get('date_format_short'), strtotime($result['date_end'])) : '-',
					'status' => $status,
					'actions' => $actions
				)
			);
		}

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}

	public function jxDeleteCoupon() {
		$json = array();

		if(!isset($this->request->get['coupon_id']) && !isset($this->request->post['selected'])) {
			$json['error'] = $this->language->get('ms_coupon_error_deleting');
		}

		if(!isset($json['error'])) {
			$coupon_ids = isset($this->request->get['coupon_id']) ?
				array($this->request->get['coupon_id']) :
				(isset($this->request->post['selected']) ? $this->request->post['selected'] : array());

			foreach ($coupon_ids as $coupon_id) {
				$this->MsLoader->MsCoupon->deleteCoupon($coupon_id);
			}

			$this->session->data['success'] = $this->language->get('ms_coupon_success_deleted');
			$json['redirect'] = $this->url->link('multimerch/coupon', 'token=' . $this->session->data['token'], true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}