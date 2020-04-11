<?php

namespace MultiMerch\Logger\Handler;

use MultiMerch\Logger\Handler\HandlerInterface;
use MultiMerch\Logger\Logger;
use MultiMerch\Module\MultiMerch;

/**
 * Writes to file specified in MultiMerch settings
 */
class FileHandler implements HandlerInterface
{
	protected $logFilename;
	protected $filePermission;

	/**
	 * @param int $level The minimum logging level at which this handler will be triggered
	 * @param int|null $filePermission Optional file permissions (default (0644) are only for owner read/write)
	 */
	public function __construct($level = Logger::LEVEL_DEBUG, $filePermission = null)
	{
		$this->filePermission = $filePermission;
		$this->logFilename = DIR_LOGS . \MsLoader::getInstance()->getRegistry()->get('config')->get('msconf_logging_filename');
	}

	/**
	 * Handles request, writes record into file
	 *
	 * @param String $record Record to be written into log file
	 * @return Boolean
	 */
	public function handle($record)
	{
		if ($this->filePermission !== null) {
			@chmod($this->logFilename, $this->filePermission);
		}

		$logFile = fopen($this->logFilename, 'a');
		fwrite($logFile, $record);
		fclose($logFile);

		return true;
	}
}