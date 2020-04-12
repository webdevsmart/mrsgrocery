<?php

namespace MultiMerch\Core\Event\Handlers;

class MarketplaceActivity implements \MultiMerch\Core\Event\Handler
{
	private $registry;
	private $event;
	private $data = array();

	public function __construct(\Registry $registry, $event, $data)
	{
		$this->registry = $registry;
		$this->event = $event;
		$this->data = $data;
	}

	/**
	 * @return mixed
	 */
	public function handle()
	{
		// TODO: Implement handle() method.
	}
}