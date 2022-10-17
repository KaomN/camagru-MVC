<?php

class SettingsModel {
	public $db;

	public function GetUserData(&$res) {
		$stmt = $this->db->prepare("	SELECT users.USERNAME as 'username', users.VERIFIED as 'verified', users.NOTIFICATION as 'notification', users.EMAIL as 'email', users.ID as 'id'FROM users WHERE users.ID = ?;");
		$stmt->bindParam(1, $_SESSION['id']);
		if (!$stmt->execute())
			$res['status'] = false;
		else
		{
			$x = 0;
			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			foreach($res as $key => $value) {
				if ($res[$key] === '1' && $key != 'id')
					$res[$key] = true;
				else if ($res[$key] === '0' && $key != 'id')
					$res[$key] = false;
			}
		}
	}

	public function UpdateUsername(&$res) {
		if (strlen($_POST['username']) === 0) {
			$res = array("status" => false, "message" => "Username required!");
		} else if (!preg_match("/^[a-zA-Z0-9\-\_]+$/", $_POST['username'])) {
			$res = array("status" => false, "message" => "Username can only contain 'a-z', '0-9', '-' and '_'");
		} else if (strlen($_POST['username']) > 10) {
			$res = array("status" => false, "message" => "Username maximum length of 20!");
		} else if (strlen($_POST['username']) < 4) {
			$res = array("status" => false, "message" => "Username minumim length of 4!");
		} else {
			$stmt = $this->db->prepare("SELECT * FROM users WHERE users.USERNAME = ?;");
			$stmt->bindParam(1, $_POST['username']);
			if (!$stmt->execute()) {
				$res = array("status" => false, "message" => "Error fetching user data!");
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
						$res = array("status" => false, "message" => "Error updating user data!");
					} else {
						$res = array("status" => true, "message" => "Success, username updated!");
						$_SESSION['username'] = $_POST['username'];
						// $oldDir = "../../src/img/uploads/" . $_SESSION['username'];
						// $newDir = "../../src/img/uploads/" . $_POST['username'];
						// if (rename($oldDir, $newDir)) {
						// 	$_SESSION['username'] = $_POST['username'];
						// 	echo(json_encode(array("status" => true, "message" => "Success, username updated!")));
						// } else {
						// 	$token = bin2hex($_SESSION['email'] . $oldUsername); 
						// 	$stmt = $dbconn->prepare("UPDATE users SET users.USERNAME = ?, users.TOKEN = ? WHERE users.ID = ?;");
						// 	$stmt->bindParam(1, $oldUsername);
						// 	$stmt->bindParam(2, $token);
						// 	$stmt->bindParam(3, $_SESSION['id']);
						// 	$stmt->execute();
						// 	echo(json_encode(array("status" => false, "message" => "Error updating user data!")));
						// }
					}
				}
				else
					$res = array("status" => false, "message" => "Username already taken!");
			}
		}
	}
	public function UpdatePassword(&$res) {
		if (strlen($_POST['currentPassword']) === 0)
			$res = array("status" => false, "error" => "empty", "message" => "Password required!");
		else if (strlen($_POST['newPassword']) === 0)
			$res = array("status" => false, "error" => "emptyNp", "message" => "Password required!");
		else if (strlen($_POST['confirmPassword']) === 0)
			$res = array("status" => false, "error" => "emptyCp", "message" => "Password confirmation required!");
		else if ($_POST['newPassword'] != $_POST['confirmPassword'])
			$res = array("status" => false, "error" => "match", "message" => "Password did not match with Confirmation!");
		else if(!preg_match("/\d|[A-Z]/", $_POST['newPassword']))
			$res = array("status" => false, "error" => "complex", "message" => "Password needs to include atleast an uppercase letter or number!");
		else if(strlen($_POST['newPassword']) < 8)
			$res = array("status" => false, "error" => "short", "message" => "Password minimum length of 8!");
		else if(strlen($_POST['newPassword']) > 255)
			$res = array("status" => false, "error" => "long", "message" => "Password needs to be shorter than 255 characters!");
		else {
			$stmt = $this->db->prepare("SELECT users.PASSWD as 'password' FROM users WHERE users.ID = ?;");
			$stmt->bindParam(1, $_SESSION['id']);
			if (!$stmt->execute()) {
				$res = array("status" => false, "error" => "sql", "message" => "Error fetching user data!");
			} else {
				$response = $stmt->fetch();
				if (password_verify($_POST['currentPassword'], $response['password'])){
					$password = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);
					$stmt = $this->db->prepare("	UPDATE users
												SET users.PASSWD = ?
												WHERE users.ID = ?;");
					$stmt->bindParam(1, $password);
					$stmt->bindParam(2, $_SESSION['id']);
					if (!$stmt->execute())
						$res = array("status" => false, "error" => "sql", "message" => "Error updating user data!");
					else
					$res = array("status" => true, "message" => "Success, password updated!");
				} else {
					$res = array("status" => false, "error" => "wrong", "message" => "Wrong Password!");
				}
			}
		}
	}

	function updateNotificationOn(&$res) {
		$stmt = $this->db->prepare("UPDATE users SET users.NOTIFICATION = 1 WHERE users.ID = ?;");
		$stmt->bindParam(1, $_SESSION['id']);
		if (!$stmt->execute()) {
			$res = array("status" => false, "message" => "Error updating notification settings!");
		} else {
			$res = array("status" => true, "message" => "Notification enabled!");
			$_SESSION['notification'] = true;
		}
	}

	function updateNotificationoff(&$res) {
		$stmt = $this->db->prepare("UPDATE users SET users.NOTIFICATION = 0 WHERE users.ID = ?;");
		$stmt->bindParam(1, $_SESSION['id']);
		if (!$stmt->execute()) {
			$res = array("status" => false, "message" => "Error updating notification settings!");
		} else {
			$res = array("status" => true, "message" => "Notification disabled!");
			$_SESSION['notification'] = false;
		}
	}

	function UpdateEmail(&$res) {
		$newEmail = $_POST['email'];
		if (empty($newEmail))
			$res = array("status" => false, "message" => "Email required!");
		else if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL))
			$res = array("status" => false, "message" => "Invalid email address!");
		else {
			$stmt = $this->db->prepare("SELECT * FROM users WHERE users.EMAIL = ?;");
			$stmt->bindParam(1, $newEmail);
			if (!$stmt->execute()) {
				$res = array("status" => false, "message" => "Error updating user data!");
			} else {
				$user = $stmt->fetch();
				if ($user) {
					$res = array("status" => false, "message" => "Email address in use!");
				} else {
					$stmt = $this->db->prepare("SELECT users.TOKEN as 'token' FROM users WHERE users.EMAIL = ?;");
					$stmt->bindParam(1, $_SESSION['email']);
					if (!$stmt->execute()) {
						$res = array("status" => false, "message" => "Error updating user data!");
					} else {
						$token = $stmt->fetch();
						if (!$token) {
							$res = array("status" => false, "message" => "Error updating user data!");
						} else {
							$currentTime = bin2hex(" " . strtotime(date("Y-m-d H:i:s")));
							$token = $token['token'] . $currentTime;
							$newtoken = bin2hex($_POST['email'] . " " . $_SESSION['username']); 
							$subject = 'Camagru email change request';
							$message = 'Please follow the link below to change the email on your account.' . "\n" . 'http://127.0.0.1:8080/camagru?t=' . $token . "&nt=" . $newtoken;
							$headers = 'From: no-reply@camagru-conguyen.com <Camagru conguyen>' . "\r\n";
							//mail($newEmail, $subject, $message, $headers);
						}
					}
				}
			}
		}
	}
}
