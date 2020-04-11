<?php

namespace MultiMerch\Logger\Handler;

/**
 * Handler interface
 */
interface HandlerInterface
{
	/**
	 * Handles a record
	 *
	 * @param  String $record
	 * @return Boolean
	 */
	public function handle($record);
}