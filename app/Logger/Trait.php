<?php

trait Logger_Trait
{
	protected $log;

	public function setLog(Logger_AbstractLog $log)
	{
		$this->log = $log;
	}

	public function getLog()
	{
		if (!$this->log) {
			$this->log = new Logger_NullLog;
		}

		return $this->log;
	}

	protected function writeToLog($message, $newLine = true)
	{
		$this->getLog()->write(sprintf('[%s] %s', date('Y-m-d H:i:s'), $message), $newLine);
	}
} 