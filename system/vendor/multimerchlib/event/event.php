<?php

namespace MultiMerch\Event;

class Event
{
	const PRODUCT_CREATED = 1;
	const PRODUCT_MODIFIED = 2;

	const SELLER_CREATED = 3;
	const SELLER_MODIFIED = 4;

	const CUSTOMER_CREATED = 5;
	const CUSTOMER_MODIFIED = 6;

	const ORDER_CREATED = 7;

	protected $data = array();

	public function __construct($data = array())
	{
		$this->data = $data;
	}

	public function getData()
	{
		return $this->data;
	}

	public function setData(array $data)
	{
		$this->data = $data;
		return $this;
	}
}