<?php

	include_once('database.php');
	//Create Database if it does not exist
	try {
		$stmt = "CREATE DATABASE camagru;";
		$conn->exec($stmt);
		if (session_status() == PHP_SESSION_ACTIVE)
			header("Location: /logout");

	} catch (PDOException $error) {
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
	}
	// Create new users table
	try {
		$conn->query("USE " . $DB_NAME);
		$stmt = "CREATE TABLE IF NOT EXISTS `users`(
				`ID` INT NOT NULL AUTO_INCREMENT,
				`USERNAME` VARCHAR(20) BINARY NOT NULL,
				`PASSWD` VARCHAR(255) NOT NULL,
				`EMAIL` VARCHAR(100) NOT NULL,
				`VERIFIED` BOOLEAN NOT NULL DEFAULT FALSE,
				`TOKEN` varchar(70) DEFAULT NULL,
				`PASSWORDRESETTOKEN` varchar(70) DEFAULT 'NULL',
				`PASSWORDRESETEXPR` varchar(30) DEFAULT 'NULL',
				`EMAILCHANGETOKEN` varchar(70) DEFAULT 'NULL',
				`EMAILPINCODE` varchar(10) DEFAULT 'NULL',
				`EMAILEXPR` varchar(30) DEFAULT 'NULL',
				`EMAILREQUEST` varchar(100) DEFAULT 'NULL',
				`NOTIFICATION` BOOLEAN NOT NULL DEFAULT TRUE,
				PRIMARY KEY (`ID`)
				)";
		$conn->exec($stmt);
	} catch (PDOException $error) {
		echo "Failed to create table: " . $error->getMessage() . ", Aborting";
	}
	//Create image table
	try {
		$stmt = "CREATE TABLE IF NOT EXISTS `images`(
				`ID` INT NOT NULL AUTO_INCREMENT,
				`USERID` INT NOT NULL,
				`FILENAME` VARCHAR(255) NOT NULL,
				`DATE` DATETIME NOT NULL,
				PRIMARY KEY (`ID`)
				)";
		$conn->exec($stmt);
	} catch (PDOException $error) {
		echo "Failed to create table: " . $error->getMessage() . ", Aborting";
	}
	// Create likes table
	try {
		$stmt = "CREATE TABLE IF NOT EXISTS `likes`(
				`ID` INT NOT NULL AUTO_INCREMENT,
				`IMAGEID` INT NOT NULL,
				`USERID` INT NOT NULL,
				`LIKE`BOOLEAN NOT NULL DEFAULT FALSE,
				PRIMARY KEY (`ID`)
				)";
		$conn->exec($stmt);
	} catch (PDOException $error) {
		echo "Failed to create table: " . $error->getMessage() . ", Aborting";
	}
	// Create comments table
	try {
		$stmt = "CREATE TABLE IF NOT EXISTS `comments`(
				`ID` INT NOT NULL AUTO_INCREMENT,
				`IMAGEID` INT NOT NULL,
				`USERID` INT NOT NULL,
				`COMMENT` VARCHAR(255) NOT NULL,
				`DATE` DATETIME NOT NULL,
				PRIMARY KEY (`ID`)
				)";
		$conn->exec($stmt);
	} catch (PDOException $error) {
		echo "Failed to create table: " . $error->getMessage() . ", Aborting";
	}
?>