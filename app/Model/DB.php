<?php

namespace Model;

use PDO;

class DB
{
	private static $connection;

	private function __construct() {}

	/**
	 *
	 * @return PDO
	 */
	final public static function getConnection()
	{
		$config = array(
			'dbHost' => '127.0.0.1',
			'dbName' => 'test',
			'dbUser' => 'root',
			'dbPass' => '',
		);

		if (empty(static::$connection)) {
			$db = new static();
			static::$connection = $db->setConnection($config['dbHost'], $config['dbName'], $config['dbUser'], $config['dbPass']);
		}

		return static::$connection;
	}

	/**
	 * @param string $dbHost
	 * @param string $dbName
	 * @param string $dbUser
	 * @param string $dbPass
	 * @return PDO
	 */
	private function setConnection($dbHost, $dbName, $dbUser, $dbPass)
	{
		static::$connection = new PDO('mysql:host=' . $dbHost . ';dbname=' . $dbName, $dbUser, $dbPass);
		static::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		return static::$connection;
	}

	private function __clone() {}

	private function __wakeup() {}

	private function __sleep() {}
}

