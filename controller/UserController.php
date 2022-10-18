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
			if (isset($res['status']) && $res['status'] === false) {
				$popup = '<div class="popup" style="display:block;">
				<div class="popup-content">
					<form class="form" id="resendVerifyForm">
						<p>Your account has not been verified! If you did not receive a link when you registered,
						press the button below to re-send the link. Or go back to <a class="form__link" href="login">Login</a> page
						</p>
						<div class="form_message"></div>
						<div class="button_container">
							<button type="button" class="form_button_verify" id="resendVerificationBtn">Re-send verification link</button>
						</div>
					</form>
				</div>
			</div>';
			} else if (isset($res['status']) && $res['status']){
				header("Location: gallery");
			}
		}
		return require_once("view/login.php");
	}

	public function checkRequest() {
		if (isset($_POST['request']) && $_POST['request'] === "resendVerification")
			self::resendVerification();
		else
			header("location: /login");
	}

	private function resendVerification() {
		echo json_encode($this->model->ResendVerification());
	}

	public function notVerified() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		return require_once("view/login.php");
	}

	public function verifyUser() {
		$style = self::$style;
		$script = self::$script;
		$verifyRes = $this->model->VerifyUser();
		if(isset($verifyRes['status']) && $verifyRes['status']) {
			$popup = '<div class="popup" style="display: block">
			<div class="popup-content">
				<form class="form" id="resendVerifyForm">
					<p>Success! Your account is now verified!</p>
					<div class="form_message"></div>
					<div class="button_container">
						<button type="button" class="form_button_verify" id="backToLogin">Login</button>
					</div>
				</form>
			</div>
		</div>';
			return require_once("view/login.php");
		} else if (isset($verifyRes['status']) && $verifyRes['status'] === false && isset($verifyRes['redirect'])) {
			header("Location: /404");
		} else if (isset($verifyRes['status']) && $verifyRes['status'] === false && isset($verifyRes['modified'])) {
			$popup = '<div class="popup" style="display: block">
			<div class="popup-content">
				<form class="form" id="resendVerifyForm">
					<p>Please Follow the link you received on the email!</p>
					<div class="form_message"></div>
					<div class="button_container">
						<button type="button" class="form_button_verify" id="backToLogin">Login</button>
					</div>
				</form>
			</div>
		</div>';
			return require_once("view/login.php");
		}
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
		$script = "";
		$navbar = self::$navbar;
		if (isset($_POST['forgotPasswordSubmit'])) {
			$resRP = $this->model->SendResetPasswordMail();
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