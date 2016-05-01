<?php

class Logger_NullLog extends Logger_AbstractLog
{
	public function write($message, $newLine = true)
	{
		return;
	}
}