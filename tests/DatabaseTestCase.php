<?php

abstract class DatabaseTestCase extends PHPUnit_Extensions_Database_TestCase
{
	static private $pdo;
	private $connection;

	/**
	 * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
	 */
	protected function getConnection()
	{
		if ($this->connection === null) {
			if (self::$pdo == null) {
				self::$pdo = new PDO(
					'mysql:dbname=test;host=127.0.0.1',
					'root',
					''
				);
			}
			$this->connection = $this->createDefaultDBConnection(self::$pdo, 'test');
		}

		return $this->connection;
	}
}