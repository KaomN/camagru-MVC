<?php
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Helsinki');
include_once('HelperModel.php');

class ProfileModel extends HelperModel {
	public $db;

	public function GetProfileImages() {
		$stmt = $this->db->prepare("SELECT images.ID as 'imageid', images.USERID as 'imageuserid', images.FILENAME as 'filename'
								FROM images
								WHERE images.USERID = ?;");
		$stmt->bindParam(1, $_SESSION['id']);
		if (!$stmt->execute()) {
			return array("status" => false);
		} else {
			$imageData = $stmt->fetchall(PDO::FETCH_ASSOC);
			if ($imageData) {
				$image = "";
				$imageDir = "src/uploads/" . $_SESSION['username'] . "/";
				foreach ($imageData as $elem) {
					$count = self::fetchLikesCount($elem);
					$image .=	'<div class="image-container">' .
									'<div>' . 
										'<h4>' . $_SESSION['username'] . '</h4>' .
									'</div>' . 
									'<div>' .
										'<img src="' . $imageDir . $elem['filename'] . '"' . ' data-userid="' . $elem['imageuserid'] . '"' . ' data-id="' . $elem['imageid'] . '"' . ' data-filename="' . $elem['filename'] . '"' . '>' .
									'</div>' . 
									'<div class="icon-container">' .
										'<i class="material-icons comment enabled" title="Comment">comment</i>' .
										'<span class="like-amount" id="likeAmount" title="Likes"> ' . $count['likes'] . ' like(s)</span>' .
										'<i class="material-icons delete enabled" title="Delete">delete</i>' .
									'</div>' . 
									'<div class="message-container off">' .
										'<div class="user-message-input">' .
											'<span class="user-message-username">' . $_SESSION['username'] . '</span>' .
											'<input type="text" id="userMessage" placeholder="Write a comment..." autocomplete="off">' .
										'</div>' . 
										'<div class="messages"></div>' .
									'</div>' .
								'</div>';
				}
				return array("status" => true, "tag" => $image);
			}
			return array("status" => false);
		}
	}

	private function fetchLikesCount($elem) {
		$stmt = $this->db->prepare("	SELECT COUNT(*) as 'likes'
									FROM likes
									INNER JOIN images ON likes.IMAGEID = images.ID
									WHERE likes.IMAGEID = ? AND likes.LIKE = '1';");
		$stmt->bindParam(1, $elem['imageid']);
		if (!$stmt->execute()) {
			return 0;
		} else {
			$count = $stmt->fetch(PDO::FETCH_ASSOC);
			return $count;
		}
	}

	public function GetComments() {
		return parent::GetCommentsHelper($this->db, false, false, false);
	}

	public function DeleteImage() {
		return parent::DeleteImageHelper($this->db, true, true, true);
	}

	public function InsertComment() {
		return parent::InsertCommentHelper($this->db, false, false, true);
	}

}
