<?php

namespace MultiMerch\Core\Event;

class EventManager
{
	/** @var \Registry $registry	Registry. */
	private $registry;

	/** @var array $handlers	Events to handlers relation. Array keys are event names and values are handler names. */
	private $handlers = array();

	/**
	 * Sets registry.
	 *
	 * @param \Registry $registry
	 * @return $this
	 */
	public function setRegistry(\Registry $registry)
	{
		$this->registry = $registry;
		return $this;
	}

	/**
	 * Gets registry.
	 *
	 * @return mixed
	 */
	public function getRegistry()
	{
		return $this->registry;
	}

	/**
	 * Adds handler for a specified event.
	 *
	 * @param	string			$event		Event name.
	 * @param	string			$handler	Handler name.
	 * @param	bool|array		$data		Data to be passed to handlers.
	 */
	public function add($event, $handler, $data = FALSE)
	{
		$handler_class = __NAMESPACE__ . "\\Handlers\\" . $handler;
		$this->handlers[$event][] = new $handler_class($this->getRegistry(), $event, $data);
	}

	/**
	 * Signals that a specified event must be handled by added handlers.
	 *
	 * @param	string			$event		Event name.
	 */
	public function fire($event)
	{
		if (!isset($this->handlers[$event]))
			return;

		foreach ($this->handlers[$event] as $k => $handler) {
			if ($handler->handle())
				unset($this->handlers[$event][$k]);
		}
	}
}