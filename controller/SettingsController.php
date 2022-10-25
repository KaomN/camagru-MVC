<?php

class SettingsController{
	
	public $model;
	private static $style = '<link rel="stylesheet" href="styles/settings.css">';
	private static $script = '<script src="scripts/settings.js"></script>';
	private static $navbar = true;

	public function showSettings() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		$_SESSION['lastURL'] = $_GET['url'];
		return require_once('view/settings.php');
	}

	public function checkRequest() {
		if (isset($_POST['request']) && $_POST['request'] === "updateUsername" && isset($_SESSION['id']) && $_SESSION['lastURL'] === $_GET['url'])
			self::updateUsername();
		else if (isset($_POST['request']) && $_POST['request'] === "updateEmail" && isset($_SESSION['id']) && $_SESSION['lastURL'] === $_GET['url'])
			self::UpdateEmail();
		else if (isset($_POST['request']) && $_POST['request'] === "updatePassword" && isset($_SESSION['id']) && $_SESSION['lastURL'] === $_GET['url'])
			self::updatePassword();
		else if (isset($_POST['request']) && $_POST['request'] === "notificationOn" && isset($_SESSION['id']) && $_SESSION['lastURL'] === $_GET['url'])
			self::updateNotificationOn();
		else if (isset($_POST['request']) && $_POST['request'] === "notificationOff" && isset($_SESSION['id']) && $_SESSION['lastURL'] === $_GET['url'])
			self::updateNotificationoff();
		else
			header("location: /settings");
	}

	public function updateUsername() {
		echo json_encode($this->model->UpdateUsername());
	}

	public function updatePassword() {
		echo json_encode($this->model->UpdatePassword());
	}

	public function updateNotificationOn() {
		echo json_encode($this->model->UpdateNotificationOn());
	}

	public function updateNotificationOff() {
		echo json_encode($this->model->UpdateNotificationOff());
	}
	public function updateEmail() {
		echo json_encode($this->model->updateEmail());
	}
}
