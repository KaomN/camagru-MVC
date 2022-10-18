<?php

class SettingsController{
	
	public $model;
	private static $res = [];
	private static $style = '<link rel="stylesheet" href="styles/settings.css">';
	private static $script = '<script src="scripts/settings.js"></script>';
	private static $navbar = true;

	public function indexAction() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		if (isset($_POST['request']) && $_POST['request'] === 'changeUsername') {
			$this->model->ChangeUsername(self::$res);
		}
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
		$this->model->UpdateUsername(self::$res);
		echo(json_encode(array("status" => self::$res['status'], "message" => self::$res['message'])));
	}

	public function updatePassword() {
		$this->model->UpdatePassword(self::$res);
		echo(json_encode(array("status" => self::$res['status'], "error" => self::$res['error'], "message" => self::$res['message'])));
	}

	public function updateNotificationOn() {
		$this->model->UpdateNotificationOn(self::$res);
		echo(json_encode(array("status" => self::$res['status'], "message" => self::$res['message'])));
	}

	public function updateNotificationOff() {
		$this->model->UpdateNotificationOff(self::$res);
		echo(json_encode(array("status" => self::$res['status'], "message" => self::$res['message'])));
	}
	public function updateEmail() {
		$this->model->updateEmail(self::$res);
		echo(json_encode(array("status" => self::$res['status'], "message" => self::$res['message'])));
	}
}
