<?php

namespace MultiMerch\Logger;

/**
 * Logger interface
 */
interface LoggerInterface
{
	/**
	 * Logs informative events
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	 */
	public function info($message, $context = array());

	/**
	 * Logs warning messages
	 *
	 * @param string $message
	 * @param array  $context
	 * @return void
	 */
	public function warning($message, $context = array());

	/**
	 * Logs debug information.
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	 */
	public function debug($message, $context = array());

	/**
	 * Logs runtime errors
	 *
	 * @param string $message
	 * @param array $context
	 * @return void
	 */
	public function error($message, $context = array());

	/**
	 * Logs events with specified level
	 *
	 * @param mixed $level
	 * @param string $message
	 * @param array $context
	 * @return void
	 */
	public function log($level, $message, $context = array());
}