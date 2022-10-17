<?php

class DeleteHelperModel {
	
	public function checkImageData($conn, $isOwnImage, $deleting, $checkLogin) {
		if ($checkLogin && !isset($_SESSION['id']))
			return false;
		$components = explode('/', $_POST['imagesrc']);
		if(count($components) != 7)
			return false;
		$username = $components[5];
		$imagename = $components[6];
		if ($imagename != $_POST['imagename'])
			return false;
		$stmt = $conn->prepare("SELECT images.ID as 'imageid', images.USERID as 'imageuserid', images.FILENAME as 'filename', users.USERNAME as 'username'
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
}