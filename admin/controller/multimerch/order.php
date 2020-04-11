<?php

class ControllerMultimerchOrder extends ControllerMultimerchBase {
	public function getOrderTableData() {
		$colMap = array(
			'customer' => "CONCAT(o.firstname, ' ', o.lastname)",
			'order_status' => "(SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "')",
			'total' => "o.total"
		);

		$sorts = array('order_id', 'order_status', 'customer', 'date_added', 'date_modified', 'total');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsSuborder->getOrders(
			array(),
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				'filters' => $filterParams,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength']
			)
		);

		$total = isset($results[0]['total_rows']) ? $results[0]['total_rows'] : 0;

		$columns = array();
		foreach ($results as $result) {
			// actions
			$actions = "";
			$actions .= "<a class='btn btn-primary' href='" . $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'], 'SSL') . "' title='".$this->language->get('button_view')."'><i class='fa fa-eye''></i></a>";

			$suborders = '<table class="table sub-table">';
			foreach ($result['suborders'] as $suborder){
				$suborders .= '<tr>';
				$suborders .= '	<td><a target="_blank" href="' . $this->url->link('multimerch/seller/update', 'token=' . $this->session->data['token'] . '&seller_id=' . $suborder['seller_id'], 'SSL') .'">' . $suborder['nickname'] . '</a></td>';
				$suborders .= '	<td>' . $suborder['status_name'] . '</td>';
				$suborders .= '</tr>';
			}
			$suborders .= '</table>';

			$columns[] = array_merge(
				$result,
				array(
					'customer' => '<a target="_blank" href="' . $this->url->link('customer/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['customer_id'], 'SSL') .'">' . $result['firstname'] . ' ' . $result['lastname'] . '</a>',
					'order_status' => $result['order_status'],
					'suborders' => $suborders,
					'total' =>  $this->currency->format($result['total']),
					'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
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

	public function getSubOrderTableData() {
		$colMap = array(
			'order_id' => "o.order_id",
			'customer' => "CONCAT(o.firstname, ' ', o.lastname)",
			'seller' => "(SELECT nickname FROM " . DB_PREFIX . "ms_seller s WHERE s.seller_id = mso.seller_id)",
			'status' => "(SELECT name FROM " . DB_PREFIX . "ms_suborder_status_description msosd WHERE msosd.ms_suborder_status_id = mso.order_status_id AND msosd.language_id = '" . (int)$this->config->get('config_language_id') . "')"
		);

		$sorts = array(
			'order_id', 'seller', 'customer','status'
		);
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsSuborder->getSuborders(
			array(),
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				'filters' => $filterParams,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength']
			)
		);

		$total = isset($results[0]['total_rows']) ? $results[0]['total_rows'] : 0;

		$columns = array();
		foreach ($results as $result) {
			// actions
			$actions = "";
			$actions .= "<a class='btn btn-primary' href='" . $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'], 'SSL') . "' title='".$this->language->get('button_view')."'><i class='fa fa-eye''></i></a>";

			$suborder_total = $this->MsLoader->MsSuborder->getSuborderTotal($result['order_id'],array('seller_id' => $result['seller_id']));
			$suborder_total = $suborder_total['total'];

			$columns[] = array_merge(
				$result,
				array(
					'suborder_id' => $result['order_id'] . '-' .$result['suborder_id'],
					'customer' => '<a target="_blank" href="' . $this->url->link('customer/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $result['customer_id'], 'SSL') .'">' . $result['firstname'] . ' ' . $result['lastname'] . '</a>',
					'seller' => '<a target="_blank" href="' . $this->url->link('multimerch/seller/update', 'token=' . $this->session->data['token'] . '&seller_id=' . $result['seller_id'], 'SSL') .'">' . $result['seller'] . '</a>',
					'total' =>  $this->currency->format($suborder_total),
					'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
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

	public function index() {
		$this->validate(__FUNCTION__);

		$this->data['token'] = $this->session->data['token'];
		$this->document->setTitle($this->language->get('ms_order_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL')
			),
			array(
				'text' => $this->language->get('ms_order_breadcrumbs'),
				'href' => $this->url->link('multimerch/order', '', 'SSL')
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/order.tpl', $this->data));
	}
}
?>
