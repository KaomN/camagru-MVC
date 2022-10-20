<?php

class SettingsModel {
	public $db;

	public function UpdateUsername(&$res) {
		if (strlen($_POST['username']) === 0) {
			return array("status" => false, "message" => "Username required!");
		} else if (!preg_match("/^[a-zA-Z0-9\-\_]+$/", $_POST['username'])) {
			return array("status" => false, "message" => "Username can only contain 'a-z', '0-9', '-' and '_'");
		} else if (strlen($_POST['username']) > 10) {
			return array("status" => false, "message" => "Username maximum length of 20!");
		} else if (strlen($_POST['username']) < 4) {
			return array("status" => false, "message" => "Username minumim length of 4!");
		} else {
			$stmt = $this->db->prepare("SELECT * FROM users WHERE users.USERNAME = ?;");
			$stmt->bindParam(1, $_POST['username']);
			if (!$stmt->execute()) {
				return array("status" => false, "message" => "Server error! Please try again later!");
			} else {
				$response = $stmt->fetch(PDO::FETCH_ASSOC);
				if (!$response) {
					$oldUsername = $_SESSION['username'];
					$token = bin2hex($_SESSION['email'] . $_POST['username']); 
					$stmt = $this->db->prepare("UPDATE users SET users.USERNAME = ?, users.Token = ? WHERE users.ID = ?;");
					$stmt->bindParam(1, $_POST['username']);
					$stmt->bindParam(2, $token);
					$stmt->bindParam(3, $_SESSION['id']);
					if (!$stmt->execute()) {
						return array("status" => false, "message" => "Server error! Please try again later!");
					} else {
						return array("status" => true, "message" => "Success, username updated!");
						$_SESSION['username'] = $_POST['username'];
						$oldDir = "src/uploads/" . $_SESSION['username'];
						$newDir = "src/uploads/" . $_POST['username'];
						if (rename($oldDir, $newDir)) {
							$_SESSION['username'] = $_POST['username'];
							return array("status" => true, "message" => "Success, username updated!");
						} else {
							$token = bin2hex($_SESSION['email'] . $oldUsername); 
							$stmt = $dbconn->prepare("UPDATE users SET users.USERNAME = ?, users.TOKEN = ? WHERE users.ID = ?;");
							$stmt->bindParam(1, $oldUsername);
							$stmt->bindParam(2, $token);
							$stmt->bindParam(3, $_SESSION['id']);
							$stmt->execute();
							return array("status" => false, "message" => "Server error! Please try again later!");
						}
					}
				}
				else
					return array("status" => false, "message" => "Username already taken!");
			}
		}
	}
	public function UpdatePassword() {
		if (strlen($_POST['currentPassword']) === 0)
			return array("status" => false, "error" => "empty", "message" => "Password required!");
		else if (strlen($_POST['newPassword']) === 0)
			return array("status" => false, "error" => "emptyNp", "message" => "Password required!");
		else if (strlen($_POST['confirmPassword']) === 0)
			return array("status" => false, "error" => "emptyCp", "message" => "Password confirmation required!");
		else if ($_POST['newPassword'] != $_POST['confirmPassword'])
			return array("status" => false, "error" => "match", "message" => "Password did not match with Confirmation!");
		// else if(!preg_match("/\d|[A-Z]/", $_POST['newPassword']))
		// 	return array("status" => false, "error" => "complex", "message" => "Password needs to include atleast an uppercase letter or number!");
		// else if(strlen($_POST['newPassword']) < 8)
		// 	return array("status" => false, "error" => "short", "message" => "Password minimum length of 8!");
		else if(strlen($_POST['newPassword']) > 255)
			return array("status" => false, "error" => "long", "message" => "Password needs to be shorter than 255 characters!");
		else {
			$stmt = $this->db->prepare("SELECT users.PASSWD as 'password' FROM users WHERE users.ID = ?;");
			$stmt->bindParam(1, $_SESSION['id']);
			if (!$stmt->execute()) {
				return array("status" => false, "error" => "sql", "message" => "Server error! Please try again later!");
			} else {
				$response = $stmt->fetch();
				if (password_verify($_POST['currentPassword'], $response['password'])){
					$password = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);
					$stmt = $this->db->prepare("UPDATE users
												SET users.PASSWD = ?
												WHERE users.ID = ?;");
					$stmt->bindParam(1, $password);
					$stmt->bindParam(2, $_SESSION['id']);
					if (!$stmt->execute())
						return array("status" => false, "error" => "sql", "message" => "Server error! Please try again later!");
					else
						return array("status" => true, "message" => "Success, password updated!");
				} else {
						return array("status" => false, "error" => "wrong", "message" => "Wrong Password!");
				}
			}
		}
	}

	function updateNotificationOn() {
		$stmt = $this->db->prepare("UPDATE users SET users.NOTIFICATION = 1 WHERE users.ID = ?;");
		$stmt->bindParam(1, $_SESSION['id']);
		if (!$stmt->execute()) {
			return array("status" => false, "message" => "Server error! Please try again later!");
		} else {
			return array("status" => true, "message" => "Notification enabled!");
			$_SESSION['notification'] = true;
		}
	}

	function updateNotificationoff(&$res) {
		$stmt = $this->db->prepare("UPDATE users SET users.NOTIFICATION = 0 WHERE users.ID = ?;");
		$stmt->bindParam(1, $_SESSION['id']);
		if (!$stmt->execute()) {
			return array("status" => false, "message" => "Server error! Please try again later!");
		} else {
			return array("status" => true, "message" => "Notification disabled!");
			$_SESSION['notification'] = false;
		}
	}

	function UpdateEmail() {
		$newEmail = $_POST['email'];
		if (empty($newEmail))
			return array("status" => false, "message" => "Email required!");
		else if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL))
			return array("status" => false, "message" => "Invalid email address!");
		else {
			$stmt = $this->db->prepare("SELECT * FROM users WHERE users.EMAIL = ?;");
			$stmt->bindParam(1, $newEmail);
			if (!$stmt->execute()) {
				return array("status" => false, "message" => "Server error! Please try again later!");
			} else {
				$user = $stmt->fetch(PDO::FETCH_ASSOC);
				if ($user) {
					return array("status" => false, "message" => "Email address in use!");
				} else {
					$stmt = $this->db->prepare("SELECT users.TOKEN as 'token' FROM users WHERE users.EMAIL = ?;");
					$stmt->bindParam(1, $_SESSION['email']);
					if (!$stmt->execute()) {
						return array("status" => false, "message" => "Server error! Please try again later!");
					} else {
						$token = $stmt->fetch(PDO::FETCH_ASSOC);
						if (!$token) {
							return array("status" => false, "message" => "Server error! Please try again later!");
						} else {
							$currentTime = date("Y-m-d H:i:s.u");
							$md5Time = (md5($currentTime));
							$md5 = md5($token['token']) . $md5Time;
							$pin = mt_rand(0,9) . mt_rand(0,9) . mt_rand(0,9) . mt_rand(0,9) . mt_rand(0,9) . mt_rand(0,9);
							$stmt = $this->db->prepare("UPDATE users 
														SET users.EMAILCHANGETOKEN = ?, users.EMAILPINCODE = ?, users.EMAILEXPR = ?, users.EMAILREQUEST = ?;
														WHERE users.EMAIL = ?;");
							$stmt->bindParam(1, $md5);
							$stmt->bindParam(2, $pin);
							$stmt->bindParam(3, $currentTime);
							$stmt->bindParam(4, $newEmail);
							$stmt->bindParam(5, $_SESSION['email']);
							if (!$stmt->execute()) {
								return array("status" => false, "message" => "Server error! Please try again later!");
							} else {
								$token = $token['token'] . $currentTime;
								$newtoken = bin2hex($_POST['email'] . " " . $_SESSION['username']); 
								$subject = 'Camagru email change request';
								$message = 'Please follow the link below to change the email address on your account.' . "\n" . 'http://127.0.0.1:8080/email/' . $md5;
								$headers = 'From: no-reply@camagru-conguyen.com <Camagru conguyen>' . "\r\n";
								$messageNewEmail = 'Please follow the link that was sent to your original email and type in the pin code below to change your email address' . "\n" . "Pin: $pin";
								// if(!mail($_SESSION['email'], $subject, $message, $headers) && !mail($newEmail, $subject, $messageNewEmail, $headers))
								// 	return array("status" => false, "message" => "Server error! Please try again later!" .);
								// else
									return array("status" => true, "message" => 'An email has been sent to ' . $_SESSION['email'] . " and " . $newEmail . ". Please follow the link on the first email!");
							}
						}
					}
				}
			}
		}
	}
}