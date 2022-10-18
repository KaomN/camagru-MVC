<?php

ini_set('display_errors', 1);
class UserModel {
	public $db;

	public function CheckUserLogin($username, $password, &$res) {
		$stmt = $this->db->prepare("SELECT * FROM users WHERE USERNAME = ? LIMIT 1");
		$stmt->bindParam(1, $username);
		if (!$stmt->execute()) {
			$res = array("status" => false, "message" => "Server connection error! Please try again later");
		} else {
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($user != null)
			{
				if (password_verify($password, $user['PASSWD']))
				{
					if ($user['VERIFIED'] === 0) {
						$_SESSION['email'] = $user['EMAIL'];
						$res = array("status" => false, "message" => "Your account has not been verified!");
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
							$res['status'] = true;
					}
					
				}
				else
				 $res = array("status" => false, "message" => "Incorrect Username/Password.");
			}
			else
				$res = array("status" => false, "message" => "Incorrect Username/Password.");
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
							$message = 'Please follow the link below to verify your account.' . "\n" . 'http://127.0.0.1:8080/camagru?t=' . $token . "&v=1";
							$headers = 'From: no-reply@camagru-conguyen.com <Camagru conguyen>' . "\r\n";
							//mail($email, $subject, $message, $headers);
							$res['status'] = true;
						}
					}
				}
			}
		}
	}
}