<?php
	$DB_NAME = "camagru";
	// // localhost works in WSL 2
	$DB_DSN = "mysql:host=localhost";
	// // has to be 127.0.0.1 in School macs
	// //$DB_DSN = "mysql:host=127.0.0.1";
	$DB_DSN_LIGHT = "localhost";
	$DB_USER = "root";
	$DB_PASSWORD = "password";

	try {
		$conn = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $error) {
		echo "Failed to connect to Database: " . $error->getMessage() . ", Aborting";
	}
?>