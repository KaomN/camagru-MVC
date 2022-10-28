<?php

	$DB_NAME = "camagru";
	$DB_DSN = "mysql:host=localhost";
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