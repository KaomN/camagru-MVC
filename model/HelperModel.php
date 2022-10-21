<?php

class HelperModel {

	public function checkImageData($db, $isOwnImage, $deleting, $checkLogin) {
		if ($checkLogin && !isset($_SESSION['id']))
			return false;
		$components = explode('/', $_POST['imagesrc']);
		if(count($components) != 7)
			return false;
		$username = $components[5];
		$imagename = $components[6];
		if ($imagename != $_POST['imagename'])
			return false;
		$stmt = $db->prepare("	SELECT images.ID as 'imageid', images.USERID as 'imageuserid', images.FILENAME as 'filename', users.USERNAME as 'username'
								FROM images
								INNER JOIN users ON images.USERID = users.ID
								WHERE images.ID = ? AND images.USERID = ? AND images.FILENAME = ? AND users.USERNAME = ?;");
		$stmt->bindParam(1, $_POST['imageid']);
		$stmt->bindParam(2, $_POST['imageuserid']);
		$stmt->bindParam(3, $_POST['imagename']);
		$stmt->bindParam(4, $username);
		if (!$stmt->execute()) {
			return false;
		} else {
			$imageData = $stmt->fetch(PDO::FETCH_ASSOC);
			if (!$imageData)
				return false;
			if ($isOwnImage) {
				if (!$deleting && strval($imageData['imageuserid']) === strval($_SESSION['id']))
					return false;
				if ($deleting && strval($imageData['imageuserid']) != strval($_SESSION['id']))
					return false;
			}
		}
		return true;
	}

	public function DeleteImageHelper($db, $isOwnImage, $deleting, $checkLogin) {
		if(self::checkImageData($this->db, $isOwnImage, $deleting, $checkLogin)) {
			$targetFile = "src/uploads/" . $_SESSION['username'] . "/" . $_POST['imagename'];
			$stmt = $db->prepare("	DELETE
									FROM images
									WHERE ID = ?
									LIMIT 1;");
			$stmt->bindParam(1, $_POST['imageid']);
			if (!$stmt->execute()) {
				return array("status" => false, "message" => "Failed to delete image from database");
			} else {
				$stmt = $db->prepare("	DELETE likes.*, comments.*
										FROM likes
										INNER JOIN comments
										ON likes.IMAGEID = comments.IMAGEID
										WHERE likes.IMAGEID = ?;");
				$stmt->bindParam(1, $_POST['imageid']);
				if (!$stmt->execute()) {
					unlink($targetFile);
					return array("status" => true, "message" => "Image deleted from database!");
				} else {
					unlink($targetFile);
					return array("status" => true, "message" => "Image deleted from database!");
				}
			}
		} else {
			return array("status" => false, "message" => "Image Data has been manipulated/not your image");
		}
	}

	public function InsertCommentHelper($db, $isOwnImage, $deleting, $checkLogin) {
		if(self::checkImageData($this->db, $isOwnImage, $deleting, $checkLogin)) {
			$comment = htmlspecialchars($_POST['comment']);
			$stmt = $this->db->prepare("INSERT INTO comments (comments.IMAGEID, comments.USERID, comments.COMMENT, comments.DATE)
									VALUE (?, ?, ?, NOW());");
			$stmt->bindParam(1, $_POST['imageid']);
			$stmt->bindParam(2, $_SESSION['id']);
			$stmt->bindParam(3, $comment);
			if (strlen($_POST['comment']) > 255) {
				return array("status" => false, "message" => "Error comment too long, 255 char max!");
			} else {
				if (!$stmt->execute()) {
					return array("status" => false, "message" => "Error inserting comment to database");
				} else {
					if($_POST['imageuserid'] != $_SESSION['id']) {
						$stmt = $this->db->prepare("SELECT users.NOTIFICATION as 'status', users.EMAIL as 'email'
													FROM users
													WHERE users.id = ? ;");
						$stmt->bindParam(1, $_POST['imageuserid']);
						if (!$stmt->execute()) {
							return array("status" => false);
						} else {
							$user = $stmt->fetch(PDO::FETCH_ASSOC);
							if (strval($notification['status']) === "1") {
								$subject = 'Camagru comment notification';
								$message = 'You received a comment on one of your images from: ' . $_SESSION['username'] . "\n\n" . "Comment: " . $_POST['comment'] . "\n\n" ."If you want to stop receiving comment notifications, please change notification settings";
								$headers = 'From: no-reply@camagru-conguyen.com <Camagru conguyen>' . "\r\n";
								//mail($user['email'], $subject, $message, $headers);
								return  array("status" => true, "message" => "Comment inserted to database");
							} else {
								return  array("status" => true, "message" => "Comment inserted to database");
							}
						}
					} else {
						return  array("status" => true, "message" => "Comment inserted to database");
					}
				}
			}
		} else {
			return array("status" => false, "message" => "Image Data has been manipulated!");
		}
	}

	public function GetCommentsHelper($db, $isOwnImage, $deleting, $checkLogin) {
		if (self::checkImageData($this->db, $isOwnImage, $deleting, $checkLogin)) {
			$stmt = $db->prepare("SELECT comments.COMMENT as 'comment', users.USERNAME as 'username', comments.ID, comments.DATE as 'date'
										FROM comments
										INNER JOIN users ON comments.USERID = users.ID
										WHERE comments.IMAGEID = ?
										ORDER BY comments.DATE
										DESC;");
			$stmt->bindParam(1, $_POST['imageid']);
			if (!$stmt->execute()) {
				return array("status" => false);
			} else {
				$comments = $stmt->fetchall(PDO::FETCH_ASSOC);
				return array("status" => true, "tag" => self::CreateCommentsElement($comments));
			}
		}
		return array("status" => false, "message" => "Image Data has been manipulated/Not your image!");
	}

	private function CreateCommentsElement($comments) {
		date_default_timezone_set('Europe/Helsinki');
		$comment = "";
		if($comments) {
			foreach (array_reverse($comments) as $elem) {
				$comment .=	'<div class="comments-container">' .
								'<span>' . $elem['username'] .  '</span>' .
								'<div class="message">' . $elem['comment'] . '</div>' .
								'<span class="time">' . self::calculateDate(strtotime($elem['date']), strtotime(date('Y-m-d H:i:s', time()))) .  '</span>' .
							'</div>';
			}
			$comment = '<div>' . $comment . '</div>';
		} else {
			$comment .=	'<div class="no-comments-container">' . 
								'<div>' . 'No Comments...'. '</div>' . 
							'</div>';
		}
		return $comment;
	}

	private function calculateDate($sentDate, $nowDate) {
		$seconds = $nowDate - $sentDate;
		$str = $dateEnd = "";
		if ($seconds < 60)
			return $str = $seconds . "s";
		else if ($seconds > 59 && $seconds < 3600) 
			return $str = floor($seconds/60) . "m";
		else if ($seconds > 3599 && $seconds < 86400)
			return $str = floor($seconds/3600) . "h";
		else if ($seconds > 86399 && $seconds < 2592000)
			return $str = floor($seconds/86400) . "d";
		else if ($seconds > 2591999 && $seconds < 31104000)
			return $str = floor($seconds/2592000) . "M";
		else
			return $str = floor($seconds/31104000) . "y";
	}
}