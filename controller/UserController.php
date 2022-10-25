<?php

class UserController {
	
	public $model;
	private static $style = '<link rel="stylesheet" href="styles/index.css">';
	private static $script = '<script src="scripts/index.js"></script>';
	private static $navbar = false;

	public function showLogin() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		$_SESSION['lastURL'] = "login";
		return require_once("view/login.php");
	}

	public function showSignup() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		$_SESSION['lastURL'] = $_GET['url'];
		return require_once("view/signup.php");
	}

	public function showForgotPassword() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		$_SESSION['lastURL'] = $_GET['url'];
		return require_once("view/forgotpassword.php");
	}

	public function showResetPassword() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		$_SESSION['lastURL'] = $_GET['url'];
		return require_once("view/resetpassword.php");
	}

	public function showEmailChange() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		$_SESSION['lastURL'] = $_GET['url'];
		return require_once("view/emailchange.php");
	}
	
	public function checkRequest() {
		if(isset($_POST['request']) && $_POST['request'] === "loginAction" && $_SESSION['lastURL'] === $_GET['url'])
			self::loginAction();
		else if (isset($_POST['request']) && $_POST['request'] === "signupAction" && $_SESSION['lastURL'] === $_GET['url'])
			self::signupAction();
		else if (isset($_POST['request']) && $_POST['request'] === "forgotPasswordAction" && $_SESSION['lastURL'] === $_GET['url'])
			self::forgotPasswordAction();
		else if (isset($_POST['request']) && $_POST['request'] === "resetPasswordAction" && $_SESSION['lastURL'] === $_GET['url'])
			self::resetPasswordAction();
		else if (isset($_POST['request']) && $_POST['request'] === "resendVerification" && $_SESSION['lastURL'] === $_GET['url'])
			self::resendVerification();
		else if (isset($_POST['request']) && $_POST['request'] === "emailChangeAction" && $_SESSION['lastURL'] === $_GET['url'])
			self::emailChangeAction();
		else
			header("location: /login");
	}

	private function loginAction() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		if (isset($_SESSION['lastURL']) && $_SESSION['lastURL'] === $_GET['url']) {
			$res['status'] = false;
			$username = $_POST['username'];
			$password = $_POST['password'];
			if (empty($username))
				$res['username'] = "Username required!";
			if (empty($password))
				$res['password'] = "Password required!";
			if (empty($res['username']) && empty($res['password']))
				$res = $this->model->CheckUserLogin($username, $password);
			if (isset($res['status']) && !$res['status'] && isset($res['verify']) && !$res['verify']) {
				$popup =	'<div class="popup" style="display:block;">
								<div class="popup-content">
									<form class="form" id="resendVerifyForm">
										<p>Your account has not been verified! If you did not receive a link when you registered,
										press the button below to re-send the link. Or go back to <a class="form__link" href="login">Login</a> page
										</p>
										<div class="form_message"></div>
										<div class="button_container">
											<a href="login"><button type="button" class="form_button_verify">Back to Login</button></a>
											<button type="button" class="form_button_verify" id="resendVerificationBtn">Re-send verification link</button>
										</div>
									</form>
								</div>
							</div>';
			} else if (isset($res['status']) && $res['status']){
				header("Location: /gallery");
			}
		}
		return require_once("view/login.php");
	}

	private function signupAction() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		if (isset($_SESSION['lastURL']) && $_SESSION['lastURL'] === $_GET['url']) {
			$res['status'] = false;
			$username = $_POST['username'];
			$email = $_POST['email'];
			$password = $_POST['password'];
			$passwordConfirm = $_POST['passwordConfirm'];
			if (empty($username))
				$res['username'] = "Username required!";
			else if (!preg_match("/^[a-zA-Z0-9\-\_]+$/", $username))
				$res['username'] = "Username only accepts 'a-z', '0-9', '-' and '_'";
			else if (strlen($username) < 4)
				$res['username'] = "Username minumim length of 4!";
			else if (strlen($username) > 20)
				$res['username'] = "Username maximum length of 20!";
			if (empty($email))
				$res['email'] = "Email required!";
			if (empty($password))
				$res['password'] = "Password required!";
			// else if (strlen($password) < 8)
			//	$res = array("status" => false, "password" => "Password minimum length of 8!");
			// else if (strlen($password) > 50)
			//	$res = array("status" => false, "password" => "Password needs to be shorter than 50 characters!");
			// else if(!preg_match("/\d|[A-Z]/", $password))
			//	$res = array("status" => false, "password" => "Password needs to include atleast an uppercase letter or number!");
			else if ($password != $passwordConfirm) {
				$res['password'] = "Password did not match!";
				$res['passwordConfirm'] = "Password did not match!";
			}
			if (empty($passwordConfirm))
				$res['passwordConfirm'] = "Password confirmation required!";
			if (!filter_var($email, FILTER_VALIDATE_EMAIL))
				$res['email'] = "Invalid email address!";
			if (empty($res['username']) && empty($res['password']) && empty($res['passwordConfirm']) && empty($res['email']))
				$res = $this->model->SignupUser($username, $password, $email);
			if (isset($res['status']) && $res['status']) {
				$popup =	'<div class="popup" style="display: block">
									<div class="popup-content">
										<p>Thank you for signing up to Camagru!
											</br>An email has been sent to you for verification!
										</p>
										<a href="login" draggable="false"><button class="form_button" type="button">Login!</button></a>
									</div>
								</div>';
				return require_once("view/signup.php");
			}
		}
		return require_once("view/signup.php");
	}

	private function forgotPasswordAction() {
		$style = self::$style;
		$script = "";
		$navbar = self::$navbar;
		$res = [];
		$email = $_POST['email'];
		if (isset($_SESSION['lastURL']) && $_SESSION['lastURL'] === $_GET['url']) {
			if (empty($_POST['email']))
				$res = array("status" => false, "message" => "Email required!");
			else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
				$res = array("status" => false, "message" => "Invalid email address!");
			$res = $this->model->SendPasswordResetMail();
		}
		return require_once("view/forgotpassword.php");
	}

	private function resetPasswordAction() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		$res['status'] = false;
		if (isset($_SESSION['lastURL']) && $_SESSION['lastURL'] === $_GET['url']) {
			$password = $_POST['password'];
			$passwordConfirm = $_POST['passwordConfirm'];
			if (empty($password))
				$res['messagePassword'] = "Password required!";
			// else if (strlen($password) < 8)
			//	$res['messagePassword'] = "Password minimum length of 8!";
			// else if (strlen($password) > 50)
			//	$res['messagePassword'] = "Password needs to be shorter than 50 characters!";
			// else if(!preg_match("/\d|[A-Z]/", $password))
			//	$res['messagePassword'] = "Password needs to include atleast an uppercase letter or number!";
			else if ($password != $passwordConfirm) {
				$res['messagePassword'] = "Password did not match!";
				$res['messagePasswordConfirm'] = "Password did not match!";
			}
			if (empty($passwordConfirm))
				$res['messagePasswordConfirm'] = "Password confirmation required!";
			if (empty($res['messagePassword']) && empty($res['messagePasswordConfirm'])) {
				$res = $this->model->ResetPasswordModel($password);
				if(isset($res['status']) && $res['status']) {
					$popup =	'<div class="popup" style="display: block">
									<div class="popup-content">
										<p>Success! Your password has been reset!</p>
										<div class="form_message"></div>
										<div class="button_container">
											<a href="login"><button type="button" class="form_button_verify">Login</button></a>
										</div>
									</div>
								</div>';
				} else if (isset($res['status']) && $res['status'] === false && isset($res['modified'])) {
					$res['message'] = "Please Follow the link you received on the email!";
				} else if (isset($res['status']) && $res['status'] === false && isset($res['expired'])) {
					$res['message'] = "Password reset link has expired! Please use the forgot password link to resend the link!";
				}
				return require_once("view/resetpassword.php");
			}
			return require_once("view/resetpassword.php");
		}
	}

	private function resendVerification() {
		echo json_encode($this->model->ResendVerification());
	}

	private function emailChangeAction() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		$res = [];
		if (isset($_SESSION['lastURL']) && $_SESSION['lastURL'] === $_GET['url']) {
			if (empty($_POST['pin']))
				$res = array("status" => false, "message" => "PIN required!");
			else if(!preg_match("/^[0-9]+$/", $_POST['pin']))
				$res = array("status" => false, "message" => "PIN digits only!");
			else if(strlen($_POST['pin']) != 6)
				$res = array("status" => false, "message" => "PIN 6 digits long!");
			else
				$res = $this->model->EmailChangeModel();
			return require_once("view/emailchange.php");
		}
	}

	public function verifyUser() {
		$style = self::$style;
		$script = self::$script;
		$verifyRes = $this->model->VerifyUser();
		if(isset($verifyRes['status']) && $verifyRes['status']) {
			$popup =	'<div class="popup" style="display: block">
							<div class="popup-content">
								<p>Success! Your account is now verified!</p>
								<div class="form_message"></div>
								<div class="button_container" style="align-items:center;justify-content:center;">
									<a href="login"><button type="button" class="form_button_verify">Login</button></a>
								</div>
							</div>
						</div>';
			return require_once("view/login.php");
		} else if (isset($verifyRes['status']) && $verifyRes['status'] === false && isset($verifyRes['redirect'])) {
			header("Location: /404");
		} else if (isset($verifyRes['status']) && $verifyRes['status'] === false && isset($verifyRes['modified'])) {
			$popup =	'<div class="popup" style="display: block">
							<div class="popup-content">
								<p>Please Follow the link you received on the email!</p>
								<div class="form_message"></div>
								<div class="button_container" style="align-items:center;justify-content:center;">
									<a href="login"><button type="button" class="form_button_verify">Login</button></a>
								</div>
							</div>
						</div>';
			return require_once("view/login.php");
		}
		header("Location: /login");
	}

	public function showSuccess() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		return require_once("view/signup.php");
	}

	public function logoutAction() {
		session_destroy();
		header("Location: /login");
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