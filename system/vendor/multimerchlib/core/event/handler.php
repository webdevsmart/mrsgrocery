<?php

namespace MultiMerch\Core\Event;

/**
 * Interface Handler
 * @package MultiMerch\Core\Event
 */
interface Handler
{
	/**
	 * Handles actions on event firing.
	 *
	 * @return mixed
	 */
	public function handle();
}