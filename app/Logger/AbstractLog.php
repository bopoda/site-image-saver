<?php

abstract class Logger_AbstractLog
{
	abstract public function write($message, $newLine = true);
}