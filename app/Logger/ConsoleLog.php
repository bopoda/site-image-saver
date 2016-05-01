<?php

class Logger_ConsoleLog extends Logger_AbstractLog
{
	public function write($message, $newLine = true)
	{
		if ($newLine) {
			$message .= PHP_EOL;
		}

		echo $message;
	}
}
