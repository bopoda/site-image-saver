<?php

class Logger_BufferedLog extends Logger_AbstractLog
{
	private $messages = array();

	public function write($message, $newLine = true)
	{
		$this->messages[] = $message . ($newLine ? PHP_EOL : '');
	}

	/**
	 * @return array
	 */
	public function getLog()
	{
		return $this->messages;
	}
}
