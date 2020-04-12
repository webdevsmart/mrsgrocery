<?php

namespace MultiMerch\Logger;

use MultiMerch\Logger\Handler\FileHandler;

/**
 * Observer,that who recieves news
 */
class Logger implements LoggerInterface
{
	/**
	 * Debug information
	 */
	const LEVEL_DEBUG = 1;

	/**
	 * Informative events
	 */
	const LEVEL_INFO = 2;

	/**
	 * Warning messages
	 */
	const LEVEL_WARNING = 3;

	/**
	 * Runtime errors
	 */
	const LEVEL_ERROR = 4;

	/**
	 * Default format for record to write into log file
	 */
	const LINE_FORMAT = "[%datetime%] %level_name%: %message%%context%\n";

	/**
	 * Default time format
	 */
	const DATETIME_FORMAT = "Y-m-d H:i:s";

	/**
	 * Logging levels
	 *
	 * @var array $levels
	 */
	protected static $levels = array(
		self::LEVEL_DEBUG     => 'DEBUG',
		self::LEVEL_INFO      => 'INFO',
		self::LEVEL_WARNING   => 'WARNING',
		self::LEVEL_ERROR     => 'ERROR'
	);

	public static function getLevelName($level)
	{
		return static::$levels[$level];
	}

	/**
	 * Creates a log record
	 *
	 * @param int $level
	 * @param string $message
	 * @param array $context
	 * @return Boolean
	 */
	protected function createRecord($level, $message, $context = array())
	{
		$record_array = array(
			'message' => $message,
			'context' => $context,
			'level_name' => static::getLevelName($level),
			'datetime' => date(static::DATETIME_FORMAT, time())
		);

		$record = $this->format($record_array);

		$handler = new FileHandler($level);
		$handler->handle($record);

		return true;
	}

	/**
	 * Formats data array to string
	 *
	 * @param array $data
	 * @return String
	 */
	protected function format($data) {
		$string = str_replace(array("%datetime%", "%level_name%", "%message%"), array($data['datetime'], $data['level_name'], $data['message']), static::LINE_FORMAT);

		if(!empty($data['context'])) {
			foreach ($data['context'] as $var => $val) {
				if (false !== strpos($string, '%context.'.$var.'%')) {
					$string = str_replace('%context.'.$var.'%', str_replace(["\r\n", "\r", "\n"], ' ', $val), $string);
					unset($data['context'][$var]);
				}
			}
		} else {
			unset($data['context']);
			$string = str_replace('%context%', '', $string);
		}

		// remove leftover %context.xxx% if any
		if (false !== strpos($string, '%')) {
			$string = preg_replace('/%(?:context)\..+?%/', '', $string);
		}

		return $string;
	}

	/**
	 * Adds a log record at the DEBUG level.
	 *
	 * This method allows for compatibility with common interfaces.
	 *
	 * @param string $message The log message
	 * @param array  $context The log context
	 */
	public function debug($message, $context = array())
	{
		if(\MsLoader::getInstance()->getRegistry()->get('config')->get('msconf_logging_level') == static::LEVEL_DEBUG)
			$this->createRecord(static::LEVEL_DEBUG, (string)$message, $context);
	}

	/**
	 * Adds a log record at the INFO level.
	 *
	 * This method allows for compatibility with common interfaces.
	 *
	 * @param string $message The log message
	 * @param array  $context The log context
	 */
	public function info($message, $context = array())
	{
		if(in_array(\MsLoader::getInstance()->getRegistry()->get('config')->get('msconf_logging_level'), array(static::LEVEL_INFO, static::LEVEL_DEBUG)))
			$this->createRecord(static::LEVEL_INFO, (string)$message, $context);
	}

	/**
	 * Adds a log record at the WARNING level.
	 *
	 * This method allows for compatibility with common interfaces.
	 *
	 * @param string $message The log message
	 * @param array  $context The log context
	 */
	public function warning($message, $context = array())
	{
		if(\MsLoader::getInstance()->getRegistry()->get('config')->get('msconf_logging_level') == static::LEVEL_WARNING)
			$this->createRecord(static::LEVEL_WARNING, (string)$message, $context);
	}

	/**
	 * Adds a log record at the ERROR level.
	 *
	 * This method allows for compatibility with common interfaces.
	 *
	 * @param string $message The log message
	 * @param array  $context The log context
	 */
	public function error($message, $context = array())
	{
		if(in_array(\MsLoader::getInstance()->getRegistry()->get('config')->get('msconf_logging_level'), array(static::LEVEL_ERROR, static::LEVEL_INFO, static::LEVEL_DEBUG)))
			$this->createRecord(static::LEVEL_ERROR, (string)$message, $context);
	}

	/**
	 * Adds a log record at an arbitrary level.
	 *
	 * This method allows for compatibility with common interfaces.
	 *
	 * @param mixed  $level   The log level
	 * @param string $message The log message
	 * @param array  $context The log context
	 */
	public function log($level, $message, $context = array())
	{
		$level = isset(static::$levels[$level]) ?: static::LEVEL_DEBUG;
		$this->createRecord($level, (string)$message, $context);
	}
}
