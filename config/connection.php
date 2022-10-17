<?php

class Connection {
	public static $connection = false;

	public static function connect($DB_DSN, $DB_NAME, $DB_USER, $DB_PASSWORD) {
		try {
			if(!self::$connection) {
				$con = new PDO("$DB_DSN;dbname=$DB_NAME", $DB_USER, $DB_PASSWORD);
				$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				self::$connection = $con;
				return self::$connection;
			}
		} catch (PDOException $error) {
			echo "Failed to connect to Database: " . $error->getMessage() . ", Aborting";
			exit();
		}
	}
}