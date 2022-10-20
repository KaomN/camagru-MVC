<?php

class SettingsController{
	
	public $model;
	private static $res = [];
	private static $style = '<link rel="stylesheet" href="styles/settings.css">';
	private static $script = '<script src="scripts/settings.js"></script>';
	private static $navbar = true;

	public function showSettings() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		return require_once('view/settings.php');
	}

	public function checkRequest() {
		if (isset($_POST['request']) && $_POST['request'] === "updateUsername")
			self::updateUsername();
		else if (isset($_POST['request']) && $_POST['request'] === "updateEmail")
			self::UpdateEmail();
		else if (isset($_POST['request']) && $_POST['request'] === "updatePassword")
			self::updatePassword();
		else if (isset($_POST['request']) && $_POST['request'] === "notificationOn")
			self::updateNotificationOn();
		else if (isset($_POST['request']) && $_POST['request'] === "notificationOff")
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
