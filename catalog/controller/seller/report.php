<?php

class ControllerSellerReport extends Controller {
	public $data = array();

	public function __construct($registry) {
		parent::__construct($registry);

		$this->document->addStyle('catalog/view/javascript/multimerch/datatables/css/jquery.dataTables.css');
		$this->document->addScript('catalog/view/javascript/multimerch/datatables/js/jquery.dataTables.min.js');
		$this->document->addScript('catalog/view/javascript/multimerch/common.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

		$this->document->addScript('catalog/view/javascript/multimerch/moment.min.js');
		$this->document->addScript('catalog/view/javascript/multimerch/daterangepicker/daterangepicker.js');
		$this->document->addStyle('catalog/view/javascript/multimerch/daterangepicker/daterangepicker.css');

		$this->document->addScript('catalog/view/javascript/multimerch/report/date_ranges.js');
		$this->document->addScript('catalog/view/javascript/multimerch/report/report_datatables.js');

		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'),$this->load->language('account/account'));

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', 'SSL');
			$this->response->redirect($this->url->link('account/login', '', 'SSL'));
		} else if (!$this->MsLoader->MsSeller->isSeller() || $this->MsLoader->MsSeller->getStatus() != MsSeller::STATUS_ACTIVE) {
			$this->response->redirect($this->url->link('seller/account-profile', '', 'SSL'));
		}
	}
}
?>
