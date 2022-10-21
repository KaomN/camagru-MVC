<?php

ini_set('display_errors', 1);
date_default_timezone_set('Europe/Helsinki');

class UserModel {
	public $db;

	public function CheckUserLogin($username, $password) {
		$stmt = $this->db->prepare("SELECT * FROM users WHERE USERNAME = ? LIMIT 1");
		$stmt->bindParam(1, $username);
		if (!$stmt->execute()) {
			return array("status" => false, "message" => "Server connection error! Please try again later");
		} else {
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($user != null)
			{
				if (password_verify($password, $user['PASSWD']))
				{
					if (strval($user['VERIFIED']) === "0") {
						$_SESSION['email'] = $user['EMAIL'];
						return array("status" => false, "verify" => false);
					}
					else
					{
						$_SESSION['id'] = $user['ID'];
						$_SESSION['username'] = $user['USERNAME'];
						$_SESSION['email'] = $user['EMAIL'];
						if (strval($user['NOTIFICATION']) === "1")
							$_SESSION['notification'] = true;
						else
							$_SESSION['notification'] = false;
						if (!file_exists("src/uploads"))
							mkdir("src/uploads", 0755, true);
						if (!file_exists("src/uploads/" . $_SESSION['username']))
							mkdir("src/uploads/" . $_SESSION['username'], 0755, true);
							return array("status" => true);
					}
					
				}
				else
					return array("status" => false, "message" => "Incorrect Username/Password.");
			}
			else
				return array("status" => false, "message" => "Incorrect Username/Password.");
		}
	}

	public function SignupUser($username, $password, $email) {
		// Token for email validation
		$token = md5($email . " " . $username);
		$stmt = $this->db->prepare("SELECT * FROM users WHERE USERNAME = ? LIMIT 1;");
		$stmt->bindParam(1, $username);
		if (!$stmt->execute(array($username))) {
			return array("status" => false, "error" => "Server connection error! Please try again later");
		} else {
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($user) {
				return array("status" => false, "username" => "Username already taken!");
			} else {
				$stmt = $this->db->prepare("SELECT * FROM users WHERE EMAIL = ? LIMIT 1;");
				if (!$stmt->execute(array($email))) {
					return array("status" => false, "error" => "Server connection error! Please try again later");
				} else {
					$emailCheck = $stmt->fetch(PDO::FETCH_ASSOC);
					if ($emailCheck) {
						return array("status" => false, "email" => "Email already taken!");
					} else {
						$password = password_hash($password, PASSWORD_DEFAULT);
						$stmt = $this->db->prepare("INSERT INTO users (USERNAME, PASSWD, EMAIL, TOKEN) VALUE (?, ?, ?, ?);");
						$stmt->bindParam(1, $username);
						$stmt->bindParam(2, $password);
						$stmt->bindParam(3, $email);
						$stmt->bindParam(4, $token);
						if (!$stmt->execute()) {
							return array("status" => false, "error" => "Server connection error! Please try again later");
						} else {
							$subject = 'Camagru account confirmation';
							$message = 'Please follow the link below to verify your account.' . "\n" . 'http://127.0.0.1:8080/verification/' . $token;
							$headers = 'From: no-reply@camagru-conguyen.com <Camagru conguyen>' . "\r\n";
							//mail($email, $subject, $message, $headers);
							return array("status" => true);
						}
					}
				}
			}
		}
	}
	// Works
	public function SendPasswordResetMail() {
		$to = $_POST['email'];
		if (empty($to)) {
			return array("status" => false, "message" => "Email required!");
		} else if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
			return array("status" => false, "message" => "Invalid email address!");
		} else {
			$stmt = $this->db->prepare("SELECT users.TOKEN as 'token'
										FROM users
										WHERE users.EMAIL = ?;");
			$stmt->bindParam(1, $to);
			if (!$stmt->execute()) {
				return array("status" => false, "message" => "Server connection error! Please try again later");
			} else {
				$user = $stmt->fetch(PDO::FETCH_ASSOC);
				if (!$user) {
					return array("status" => true, "message" => "An email has been sent to " . $to . "<br>Please follow instructions on the email to reset your password!");
				}
				else {
					$currentTime = date("Y-m-d H:i:s.u");
					$md5Time = (md5($currentTime));
					$md5 = md5($user['token']) . $md5Time;
					$stmt = $this->db->prepare("UPDATE users
												SET users.PASSWORDRESETTOKEN = ?, users.PASSWORDRESETEXPR = ?
												WHERE users.EMAIL = ?;");
					$stmt->bindParam(1, $md5);
					$stmt->bindParam(2, $currentTime);
					$stmt->bindParam(3, $to);
					if (!$stmt->execute()) {
						return array("status" => false, "message" => "Server connection error! Please try again later");
					} else {
						$subject = 'Camagru Password reset';
						$message = 'Please follow tF link below to reset password on your account.' . "\n" . 'http://127.0.0.1:8080/resetpassword/' . $md5;
						$headers = 'From: no-reply@camagru-conguyen.com <Camagru conguyen>' . "\r\n";
						//mail($to, $subject, $message, $headers);
						return array("status" => true, "message" => "An email has been sent to " . $to . "<br>Please follow instructions on the email to reset your password!");
					}
				}
			}
		}
	}
	// Works
	public function ResendVerification() {
		$stmt = $this->db->prepare("SELECT users.EMAIL as 'email', users.TOKEN as 'token'
									FROM users
									WHERE users.EMAIL = ?;");
		$stmt->bindParam(1, $_SESSION['email']);
		if (!$stmt->execute()) {
			return array("status" => false, "message" => "Server connection error! Please try again later");
		}
		else {
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			if (!$user)
				return array("status" => false, "message" => "Failed to resend account confirmation link! Please try again later");
			else
			{
				$subject = 'Camagru account confirmation';
				$message = 'Please follow the link below to verify your account.' . "\n" . 'http://127.0.0.1:8080/verification/' . $user['token'];
				$headers = 'From: no-reply@camagru-conguyen.com <Camagru conguyen>' . "\r\n";
				//mail($user['email'], $subject, $message, $headers);
				return array("status" => true, 'message' => 'Email confirmation link resent!');
			}
		}
	}
	// Works
	public function VerifyUser() {
		$arr = explode("/", $_SESSION['url']);
		if(count($arr) != 3) {
			return array("status" => false, "redirect" => true);
		} else {
			$stmt = $this->db->prepare("SELECT *
										FROM users
										WHERE users.TOKEN = ?;");
			$stmt->bindParam(1, $arr[2]);
			if (!$stmt->execute()) {
				return array("status" => false, "message" => "Server connection error, Please try again later");
			} else {
				$user = $stmt->fetch(PDO::FETCH_ASSOC);
				if (!$user) {
					return array("status" => false, "modified" => true, "message" => "Please follow the link you received on the email");
				} else if (strval($user['VERIFIED']) === "1") {
					return array("status" => false, "message" => "Account already verified!");
				} else {
					$stmt = $this->db->prepare("UPDATE users
												SET users.VERIFIED = 1
												WHERE users.TOKEN = ?;");
					$stmt->bindParam(1, $arr[2]);
					if (!$stmt->execute()) {
						return array("status" => false, "error" => "sql", "message" => "Server error, Please try resubmitting.");
					} else {
						return array("status" => true, "message" => "Success! Your account is now verified!");
						$_SESSION['verified'] = true;
					}
				}
			}
		}
	}
	// Testing Needed
	public function ResetPasswordModel($password) {
		$arr = explode("/", $_SESSION['url']);
		if(count($arr) != 3) {
			return array("status" => false, "modified" => true);
		}
		$token = $arr[2];
		$stmt = $this->db->prepare("SELECT users.PASSWORDRESETTOKEN as 'token', users.PASSWORDRESETEXPR as 'expires'
									FROM users
									WHERE users.PASSWORDRESETTOKEN = ?;");
		$stmt->bindParam(1, $token);
		if (!$stmt->execute()) {
			return array("status" => false, "message" => "Server connection error! Please try again later");
		} else {
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			$linkExpiration = strtotime(date("Y-m-d H:i:s.u")) + 86400;
			if (!$data) {
				return array("status" => false, "expired" => true);
			} else if ($linkExpiration < strtotime($data['expires'])) {
				return  array("status" => false, "expired" => true);
			} else {
				$password = password_hash($password, PASSWORD_DEFAULT);
				$stmt = $this->db->prepare("UPDATE users
											SET users.PASSWD = ?, users.PASSWORDRESETTOKEN = 'NULL', users.PASSWORDRESETEXPR = 'NULL'
											WHERE users.PASSWORDRESETTOKEN = ?;");
				$stmt->bindParam(1, $password);
				$stmt->bindParam(2, $token);
				if (!$stmt->execute())
					return array("status" => false, "message" => "Server connection error! Please try again later");
				else
					return array("status" => true);
			}
		}
	}
	// Testing needed
	public function EmailChangeModel() {
		$arr = explode("/", $_SESSION['url']);
		if(count($arr) != 3) {
			return array("status" => false, "message" => "Please follow the link you received on the email");
		}
		$pin = $_POST['pin'];
		$token = $arr[2];
		$stmt = $this->db->prepare("SELECT users.EMAILCHANGETOKEN as 'token', users.EMAILEXPR as 'expires', users.EMAILPINCODE as 'pin', users.EMAILREQUEST as 'email'
									FROM users
									WHERE users.EMAILCHANGETOKEN = ?;");
		$stmt->bindParam(1, $token);
		if (!$stmt->execute()) {
			return array("status" => false, "message" => "Server connection error! Please try again later");
		} else {
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			$linkExpiration = strtotime(date("Y-m-d H:i:s.u")) + 86400;
			if (!$data) {
				return array("status" => false, "message" => "Link has expired! Please send new email change request!");
			} else if ($linkExpiration < strtotime($data['expires'])) {
				return  array("status" => false, "message" => "Link has expired! Please send new email change request!");
			} else if (intval($data['pin']) !== intval($pin)) {
				return  array("status" => false, "message" => "Incorrect PIN!");
			} else {
				$newToken = md5($data['email'] . " " . $_SESSION['username']);
				$stmt = $this->db->prepare("UPDATE users
											SET users.EMAIL = ?, users.TOKEN = ?, users.EMAILCHANGETOKEN = 'NULL', users.EMAILPINCODE = 'NULL', users.EMAILEXPR = 'NULL', users.EMAILREQUEST = 'NULL'
											WHERE users.EMAILCHANGETOKEN = ?;");
				$stmt->bindParam(1, $data['email']);
				$stmt->bindParam(2, $newToken);
				$stmt->bindParam(3, $token);
				if (!$stmt->execute()) {
					return array("status" => false, "message" => "Server connection error! Please try again later");
				} else {
					$_SESSION['email'] = $data['email'];
					return array("status" => true, "message" => "Success! Your email address has been changed!");
				}

			}
		}
	}
}