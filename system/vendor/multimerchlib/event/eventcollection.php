<?php

namespace MultiMerch\Event;

use Countable;

class EventCollection implements Countable
{
	protected $events = array();

	/**
	 * count(): defined by Countable interface.
	 *
	 * @see    Countable::count()
	 * @return int
	 */
	public function count()
	{
		return count($this->events);
	}

	public function add($event)
	{
		$this->events[] = $event;

		return $this;
	}

	public function remove($eventToRemove)
	{
		foreach ($this->events as $k => $event) {
			if ($eventToRemove === $event) {
				unset($this->events[$k]);
			}
		}

		return $this;
	}

	public function getList()
	{
		return $this->events;
	}
}