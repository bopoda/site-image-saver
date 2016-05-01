<?php

class Cli
{
	private $argv;

	public function __construct(array $argv)
	{
		$this->argv = $argv;
	}

	public function getParameterValue($param)
	{
		if (is_array($this->argv)) {
			foreach ($this->argv as $arg) {
				if (preg_match('/^--([a-zA-Z0-9]+)=?(.*)$/', $arg, $matches)) {
					if ($matches[1] === $param) {
						return $matches[2];
					}
				}
			}
		}

		return NULL;
	}
}