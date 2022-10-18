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
			return array("status" => false, "message" => "Image Data has been manipulated!");
		}
	}

	public function InsertCommentHelper($db, $isOwnImage, $deleting, $checkLogin) {
		if(self::checkImageData($this->db, $isOwnImage, $deleting, $checkLogin)) {
			$stmt = $this->db->prepare("INSERT INTO comments (comments.IMAGEID, comments.USERID, comments.COMMENT, comments.DATE)
									VALUE (?, ?, ?, NOW());");
			$stmt->bindParam(1, $_POST['imageid']);
			$stmt->bindParam(2, $_SESSION['id']);
			$stmt->bindParam(3, $_POST['comment']);
			if (strlen($_POST['comment']) > 255)
				return array("status" => false, "message" => "Error comment too long, 255 char max!");
			else
			{
				if (!$stmt->execute())
					return array("status" => false, "message" => "Error inserting comment to database");
				else
				{
					//if($_POST['imageuserid'] != $_SESSION['id']) {
						//sendNotification($conn);
						return  array("status" => true, "message" => "Comment inserted to database");
				}
			}
		} else {
			return array("status" => false, "message" => "Image Data has been manipulated!");
		}
	}
}