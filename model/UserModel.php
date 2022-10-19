<?php

ini_set('display_errors', 1);
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
						if ($user['NOTIFICATION'] === 1)
							$_SESSION['notification'] = true;
						else
							$_SESSION['notification'] = false;
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

	public function SignupUser($username, $password, $email, &$res) {
		// Token for email validation
		$token = bin2hex($email . " " . $username);
		$stmt = $this->db->prepare("SELECT * FROM users WHERE USERNAME = ? LIMIT 1;");
		$stmt->bindParam(1, $username);
		if (!$stmt->execute(array($username))) {
			$res['error'] = "Server connection error! Please try again later";
		} else {
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($user) {
				$res['username'] = "Username already taken!";
			} else {
				$stmt = $this->db->prepare("SELECT * FROM users WHERE EMAIL = ? LIMIT 1;");
				if (!$stmt->execute(array($email))) {
					$res['error'] = "Server connection error! Please try again later";
				} else {
					$emailCheck = $stmt->fetch(PDO::FETCH_ASSOC);
					if ($emailCheck) {
						$res['email'] = "Email already taken!";
					} else {
						$password = password_hash($password, PASSWORD_DEFAULT);
						$stmt = $this->db->prepare("INSERT INTO users (USERNAME, PASSWD, EMAIL, TOKEN) VALUE (?, ?, ?, ?);");
						$stmt->bindParam(1, $username);
						$stmt->bindParam(2, $password);
						$stmt->bindParam(3, $email);
						$stmt->bindParam(4, $token);
						if (!$stmt->execute()) {
							$res['error'] = "Server connection error! Please try again later";
						} else {
							$subject = 'Camagru account confirmation';
							$message = 'Please follow the link below to verify your account.' . "\n" . 'http://127.0.0.1:8080/verification/' . $token;
							$headers = 'From: no-reply@camagru-conguyen.com <Camagru conguyen>' . "\r\n";
							mail($email, $subject, $message, $headers);
							$res['status'] = true;
						}
					}
				}
			}
		}
	}
	// Works
	public function SendPasswordResetMail() {
		$to = $_POST['email'];
		if (empty($to))
			return array("status" => false, "message" => "Email required!");
		else if (!filter_var($to, FILTER_VALIDATE_EMAIL))
			return array("status" => false, "message" => "Invalid email address!");
		else {
			$stmt = $this->db->prepare("SELECT users.TOKEN as 'token'
										FROM users
										WHERE users.EMAIL = ?;");
			$stmt->bindParam(1, $to);
			if (!$stmt->execute())
				return array("status" => false, "message" => "Server connection error! Please try again later");
			else {
				$user = $stmt->fetch(PDO::FETCH_ASSOC);
				if (!$user)
					return array("status" => true, "message" => "An email has been sent to " . $to . "<br>Please follow instructions on the email to reset your password!");
				else {
					$currentTime = bin2hex(" " . strtotime(date("Y-m-d H:i:s")));
					$token = $user['token'] . $currentTime;
					$stmt->bindParam(2, $to);
					$subject = 'Camagru Password reset';
					$message = 'Please follow the link below to reset password on your account.' . "\n" . 'http://127.0.0.1:8080/resetpassword/' . $token;
					$headers = 'From: no-reply@camagru-conguyen.com <Camagru conguyen>' . "\r\n";
					mail($to, $subject, $message, $headers);
					return array("status" => true, "message" => "An email has been sent to " . $to . "<br>Please follow instructions on the email to reset your password!");
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
		if (!$stmt->execute())
			return array("status" => false, "message" => "Server connection error! Please try again later");
		else
		{
			$user = $stmt->fetch();
			if (!$user)
				return array("status" => false, "message" => "Failed to resend account confirmation link! Please try again later");
			else
			{
				$subject = 'Camagru account confirmation';
				$message = 'Please follow the link below to verify your account.' . "\n" . 'http://127.0.0.1:8080/verification/' . $user['token'];
				$headers = 'From: no-reply@camagru-conguyen.com <Camagru conguyen>' . "\r\n";
				mail($user['email'], $subject, $message, $headers);
				return array("status" => true, 'message' => 'Email confirmation link resent!');
			}
		}
	}
	// http://localhost/verification/7465737440656d61696c2e636f6d2074657374
	// Works
	public function VerifyUser() {
		$arr = explode("/", $_SESSION['url']);
		if(count($arr) != 3)
			return array("status" => false, "redirect" => true);
		if (strlen($arr[2]) % 2 != 0)
			return array("status" => false, "modified" => true , "message" => "Please follow the link you received on the email");
		$userInfo = explode(" ", hex2bin($arr[2]));
		if (count($userInfo) != 2) {
			return array("status" => false, "modified" => true, "message" => "Please follow the link you received on the email");
		} else {
			$stmt = $this->db->prepare("SELECT *
										FROM users
										WHERE users.EMAIL = ? AND users.USERNAME = ?;");
			$stmt->bindParam(1, $userInfo[0]);
			$stmt->bindParam(2, $userInfo[1]);
			if (!$stmt->execute()) {
				return array("status" => false, "message" => "Server connection error, Please try again later");
			} else {
				$user = $stmt->fetch(PDO::FETCH_ASSOC);
				if (!$user) {
					return array("status" => false, "modified" => true, "message" => "Please follow the link you received on the email");
				} else {
					$stmt = $this->db->prepare("UPDATE users
												SET users.VERIFIED = 1
												WHERE users.EMAIL = ?
												AND users.USERNAME = ?;");
					$stmt->bindParam(1, $userInfo[0]);
					$stmt->bindParam(2, $userInfo[1]);
					if (!$stmt->execute()) {
						return array("status" => false, "error" => "sql", "message" => "SQL error, Please try resubmitting.");
					} else {
						return array("status" => true, "message" => "Success! Your account is now verified!");
						$_SESSION['verified'] = true;
					}
				}
			}
		}
	}

	public function ResetPasswordModel($password) {
		$arr = explode("/", $_SESSION['url']);
		if(count($arr) != 3)
			return array("status" => false, "modified" => true);
		if (strlen($arr[2]) % 2 != 0)
			return array("status" => false, "modified" => true);
		$tokenData = explode(" ", hex2bin($arr[2]));
		$currentTime = strtotime(date("Y-m-d H:i:s"));
		$linkExpiration = intval($tokenData[2] + 86400);
		if (count($tokenData) != 3) {
			return array("status" => false, "modified" => true);
		} else {
			$token = bin2hex($tokenData[0] . " " . $tokenData[1]);
			$stmt = $this->db->prepare("SELECT users.PASSWORDRESETTOKEN as 'token'
										FROM users
										WHERE users.TOKEN = ?;");
			$stmt->bindParam(1, $token);
			if (!$stmt->execute()) {
				return array("status" => false, "message" => "Server connection error! Please try again later");
			} else {
				$passwordResetToken = $stmt->fetch(PDO::FETCH_ASSOC);
				if (!$passwordResetToken) {
					return array("status" => false,  "message" => "Server connection error! Please try again later");
				} else {
					if(strval($passwordResetToken['token']) === $arr[2]) {
						return  array("status" => false, "expired" => true);
					}
					if ($linkExpiration < $currentTime) {
						return  array("status" => false, "expired" => true);
					} else {
						$stmt = $this->db->prepare("SELECT *
													FROM users
													WHERE users.TOKEN = ?;");
						$stmt->bindParam(1, $token);
						if (!$stmt->execute())
							return array("status" => false, "message" => "Server connection error! Please try again later");
						else
						{
							$user = $stmt->fetch(PDO::FETCH_ASSOC);
							if (!$user) {
								return array("status" => false, "modified" => true);
							} else {
								$password = password_hash($password, PASSWORD_DEFAULT);
								$stmt = $this->db->prepare("UPDATE users
															SET users.PASSWD = ?, users.PASSWORDRESETTOKEN = ?
															WHERE users.TOKEN = ?;");
								$stmt->bindParam(1, $password);
								$stmt->bindParam(2, $arr[2]);
								$stmt->bindParam(3, $token);
								if (!$stmt->execute())
									return array("status" => false, "message" => "Server connection error! Please try again later");
								else
									return array("status" => true);
							}
						}
					}
				}
			}
		}
	}
}