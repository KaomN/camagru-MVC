<?php

ini_set('display_errors', 1);
class GalleryModel {
	public $db;

	public function GetGalleryImages($start) {
		$stmt = $this->db->prepare("SELECT images.ID as 'imageid', images.USERID as 'userid', images.FILENAME as 'filename', users.USERNAME as 'username'
									FROM images
									INNER JOIN users ON images.USERID = users.ID
									LIMIT {$start},6;");
		if (!$stmt->execute()) {
			exit();
		} else {
			$images = $stmt->fetchall();
			if($images) {
				return $images;
			}
			else
				return $images = false;
		}
	}
}
