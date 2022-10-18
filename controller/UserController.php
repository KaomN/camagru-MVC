<?php

class UserController {
	
	public $model;
	private static $style = '<link rel="stylesheet" href="styles/index.css">';
	private static $script = '<script src="scripts/index.js"></script>';
	private static $res = [];
	private static $navbar = false;

	public function loginAction() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		if (isset($_POST['loginSubmit'])) {
			$username = $_POST['username'];
			$password = $_POST['password'];
			// $values = array("username" => false, "password" => "");
			if (empty($username)) {
				self::$res['username'] = "Username required!";
			}
			if (empty($password)) {
				self::$res['password'] = "Password required!";
			}
			if (empty(self::$res['username']) && empty(self::$res['password'])) {
				$this->model->CheckUserLogin($username, $password, self::$res);
			}
			$res = self::$res;
			if (isset($res['status']) && $res['status'] === false)
				header("Location: login/notverified");
			else if (isset($res['status']) && $res['status'])
				header("Location: gallery");
		}
		return require_once("view/login.php");
	}

	public function notVerified() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		return require_once("view/login.php");
	}

	public function signupAction() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		if (isset($_POST['signupSubmit'])) {
			$username = $_POST['username'];
			$email = $_POST['email'];
			$password = $_POST['password'];
			$passwordConfirm = $_POST['passwordConfirm'];
			if (empty($username))
				self::$res['username'] = "Username required!";
			else if (!preg_match("/^[a-zA-Z0-9\-\_]+$/", $username))
				self::$res['username'] = "Username only accepts 'a-z', '0-9', '-' and '_'";
			else if (strlen($username) < 4)
				self::$res['username'] = "Username maximum length of 20!";
			else if (strlen($username) > 20)
				self::$res['username'] = "Username minumim length of 4!";
			if (empty($email))
				self::$res['email'] = "Email required!";
			if (empty($password))
				self::$res['password'] = "Password required!";
			// else if (strlen($password) < 8)
			// 	self::$res['password'] = "Password minimum length of 8!";
			// else if (strlen($password) > 255)
			// self::$res['password'] = "Password needs to be shorter than 255 characters!";
			// else if(!preg_match("/\d|[A-Z]/", $password))
			// 	self::$res['password'] = "Password needs to include atleast an uppercase letter or number!";
			else if ($password != $passwordConfirm) {
				self::$res['password'] = "Password did not match!";
				self::$res['passwordConfirm'] = "Password did not match!";
			}
			if (empty($passwordConfirm))
				self::$res['passwordConfirm'] = "Password confirmation required!";
			if (!filter_var($email, FILTER_VALIDATE_EMAIL))
				self::$res['email'] = "Invalid email address!";
			if (empty(self::$res['username']) && empty(self::$res['password']) && empty(self::$res['passwordConfirm']) && empty(self::$res['email'])) 
				$this->model->SignupUser($username, $password, $email, self::$res);
			if (isset(self::$res['status']) && self::$res['status']) {
				header( "Location: {$_SERVER['REQUEST_URI']}". "/success", true, 303 );
			}
			$res = self::$res;
		}
		return require_once("view/signup.php");
	}

	public function showSuccess() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		return require_once("view/signup.php");
	}

	public function forgotPasswordAction() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		if (isset($_POST['submitForgotPassword'])) {
			$username = $_POST['username'];
			$password = $_POST['password'];
			$passwordConfirm = $_POST['passwordConfirm'];
		}
		return require_once("view/forgotpassword.php");
	}

	public function logoutAction() {
		session_start();
		session_destroy();
		header("Location: login");
	}

	public function membersOnly() {
		$style = '<link rel="stylesheet" href="styles/membersonly.css">';
		$script = "";
		$navbar = self::$navbar;
		return require_once("view/membersonly.php");
	}

	public function notFound() {
		$style = self::$style;
		$script = "";
		$navbar = true;
		return require_once("view/404.php");
	}
}