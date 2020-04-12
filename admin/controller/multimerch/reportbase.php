<?php

class ControllerMultimerchReportbase extends Controller
{
	public $data = array();

	public function __construct($registry)
	{
		parent::__construct($registry);
		$this->registry = $registry;

		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
		$this->data['token'] = $this->session->data['token'];
		$this->document->addStyle('view/stylesheet/multimerch/multiseller.css');
		$this->document->addStyle('view/javascript/multimerch/datatables/css/jquery.dataTables.css');
		$this->document->addScript('view/javascript/multimerch/datatables/js/jquery.dataTables.min.js');
		$this->document->addScript('view/javascript/multimerch/common.js');
		$this->document->addScript('view/javascript/multimerch/report/date_ranges.js');
		$this->document->addScript('view/javascript/multimerch/report/report_datatables.js');

		$this->document->addScript('view/javascript/multimerch/moment.min.js');
		$this->document->addScript('view/javascript/multimerch/daterangepicker/daterangepicker.js');
		$this->document->addStyle('view/javascript/multimerch/daterangepicker/daterangepicker.css');

	}
}