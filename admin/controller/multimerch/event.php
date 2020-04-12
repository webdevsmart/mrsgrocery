<?php

use \MultiMerch\Event\Event as MsEvent;

class ControllerMultimerchEvent extends ControllerMultimerchBase {
	public function getTableData() {
		$this->load->model('customer/customer');

		$colMap = array(
			'event_type' => 'event_type',
			'date_created' => 'date_created'
		);

		$sorts = array('event_type', 'date_created');
		$filters = $sorts;

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->ms_event_manager->getEvents(
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
			// Event description
			$event_description = $this->ms_event_manager->getEventDescription($result);

			$columns[] = array_merge(
				$result,
				array(
					'event_type' => $this->language->get('ms_event_type_' . $result['event_type']),
					'description' => $event_description,
					'date_created' => date($this->language->get('datetime_format'), strtotime($result['date_created'])),
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
		$this->document->setTitle($this->language->get('ms_event_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL')
			),
			array(
				'text' => $this->language->get('ms_event_breadcrumbs'),
				'href' => $this->url->link('multimerch/event', '', 'SSL')
			)
		));

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/event.tpl', $this->data));
	}
}
?>
