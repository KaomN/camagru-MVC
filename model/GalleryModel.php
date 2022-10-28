<?php
date_default_timezone_set('Europe/Helsinki');
include_once('HelperModel.php');

class GalleryModel extends HelperModel{
	public $db;

	public function GetGalleryImages($start) {
		$stmt = $this->db->prepare("SELECT images.ID as 'imageid', images.USERID as 'userid', images.FILENAME as 'filename', users.USERNAME as 'username'
									FROM images
									INNER JOIN users ON images.USERID = users.ID
									LIMIT {$start},6;");
		if (!$stmt->execute()) {
			return array("status" => false);
		} else {
			$images = $stmt->fetchall(PDO::FETCH_ASSOC);
			if($images) {
				return array("status" => true, "tag" => self::CreateImagePacks($images));
			}
			else
				return array("status" => false);
		}
	}
	private function CreateImagePacks($images) {
		$imageElem = "";
		$hidden = "";
		if(!isset($_SESSION['id'])) {
			$hidden = "hidden";
			$username = "";
		} else {
			$username = $_SESSION['username'];
		}
		foreach ($images as $elem) {
			$imageDir = "src/uploads/" . $elem['username'] . "/";
			if (isset($_SESSION['id']) && strval($_SESSION['id']) === strval($elem['userid'])) {
				$disableBtn = 'enabled';
				$enableBtn = 'disabled';
			} else {
				$disableBtn = 'disabled';
				$enableBtn = 'enabled';
				if(!isset($_SESSION['id'])) {
					$enableBtn = 'disabled';
				}
			}
			$count = self::GetLikeCount($elem);
			$showLike = "";
			$showUnlike = "hidden";
			self::GetLikeStatus($elem, $showLike, $showUnlike);
			if (isset($_SESSION['id'])) {
				$likeunlikeBtn = '<i class="material-icons like ' . $enableBtn . ' ' . $showLike .'" title="Like">thumb_up</i>' .
									'<i class="material-icons unlike ' . $showUnlike . '" title="Unlike"">thumb_down</i>';
				$deleteBtn = '<i class="material-icons delete '. $disableBtn . '" title="Delete">delete</i>';
			} else {
				$likeunlikeBtn = "";
				$deleteBtn = "";
			}
			$imageElem .=	'<div class="image-container">' . 
								'<div>' . $elem['username'] .  '</div>' . 
								'<div>' .
									'<img src="' . $imageDir . $elem['filename'] . '"' . ' data-userid="' . $elem['userid'] . '"' . ' data-id="' . $elem['imageid'] . '"' . ' data-filename="' . $elem['filename'] . '"' . '>' .
								'</div>' . 
									'<div class="icon-container">' .
									'<i class="material-icons comment enabled" title="Comment">comment</i>' .
									$likeunlikeBtn .
									'<span class="like-amount" title="Likes"> ' . $count['likes'] . ' like(s)</span>' .
									$deleteBtn .
								'</div>' . 
								'<div class="message-container off">' .
									'<div class="user-message-input ' . $hidden . '">' .
										'<span class="user-message-username">' . $username . '</span>' .
										'<input type="text" id="userMessage" placeholder="Write a comment..." autocomplete="off">' .
									'</div>' . 
									'<div class="messages"></div>' .
								'</div>' . 
							'</div>';
		}
		return $imageElem = '<div class="image-6pack">' . $imageElem . '</div>';
	}

	private function GetLikeCount($elem) {
		$stmt = $this->db->prepare("SELECT COUNT(*) as 'likes'
									FROM likes
									INNER JOIN images ON likes.IMAGEID = images.ID
									WHERE likes.IMAGEID = ?
									AND likes.LIKE = '1';");
		$stmt->bindParam(1, $elem['imageid']);
		if (!$stmt->execute()) {
			return 0;
		} else {
			$count = $stmt->fetch(PDO::FETCH_ASSOC);
			return $count;
		}
	}

	private function GetLikeStatus($elem, &$showLike, &$showUnlike) {
		if (isset($_SESSION['id'])) {
			$stmt = $this->db->prepare("SELECT likes.LIKE as 'liked'
										FROM likes
										INNER JOIN images ON likes.IMAGEID = images.ID
										WHERE likes.IMAGEID = ?
										AND likes.USERID = ?;");
			$stmt->bindParam(1, $elem['imageid']);
			$stmt->bindParam(2, $_SESSION['id']);
			if (!$stmt->execute()) {
				return;
			} else {
				$likes = $stmt->fetch(PDO::FETCH_ASSOC);
				if(!$likes) {
					$showLike = "";
					$showUnlike = "hidden";
				} else {
					if ($likes['liked'] == "0") {
						$showLike = "";
						$showUnlike = "hidden";
					} else {
						$showLike = "hidden";
						$showUnlike = "";
					}
				}
			}
		}
	}

	public function GetComments() {
		return parent::GetCommentsHelper($this->db, false, false, false);
	}

	public function InsertComment() {
		return parent::InsertCommentHelper($this->db, false, false, true);
	}

	public function DeleteImage() {
		return parent::DeleteImageHelper($this->db, true, true, true);
	}

	public function LikeImage() {
		if (parent::checkImageData($this->db, true, false, true)) {
			$stmt = $this->db->prepare("SELECT likes.ID as 'likesid'
										FROM likes
										WHERE likes.IMAGEID = ?
										AND likes.USERID = ?;");
			$stmt->bindParam(1, $_POST['imageid']);
			$stmt->bindParam(2, $_SESSION['id']);
			if (!$stmt->execute()) {
				return array("status" => false, "message" => "Error fetching user like data");
			} else {
				$response = $stmt->fetch(PDO::FETCH_ASSOC);
				if (!$response) {
					$stmt = $this->db->prepare("INSERT INTO likes (likes.IMAGEID, likes.USERID, likes.LIKE)
												VALUE (?, ?, 1);");
					$stmt->bindParam(1, $_POST['imageid']);
					$stmt->bindParam(2, $_SESSION['id']);
					if (!$stmt->execute())
						return array("status" => false, "message" => "Error inserting likes data to database");
					else
						return array("status" => true, "message" => "Updated likes data in database");
				} else {
					$stmt = $this->db->prepare("UPDATE likes
												SET likes.LIKE = 1
												WHERE likes.IMAGEID = ?
												AND likes.USERID = ?;");
					$stmt->bindParam(1, $_POST['imageid']);
					$stmt->bindParam(2, $_SESSION['id']);
					if (!$stmt->execute())
						return array("status" => false, "message" => "Error updating like data to database");
					else
						return array("status" => true, "message" => "Updated likes data in database");
				}
			}
		}
		return array("status" => false, "message" => "Image Data has been manipulated/Not your image!");
	}

	public function unlikeImage() {
		if (parent::checkImageData($this->db, true, false, true)) {
			$stmt = $this->db->prepare("UPDATE likes
									SET likes.like = 0
									WHERE likes.IMAGEID = ?
									AND likes.USERID = ?;");
			$stmt->bindParam(1, $_POST['imageid']);
			$stmt->bindParam(2, $_SESSION['id']);
			if (!$stmt->execute())
				return array("status" => false, "message" => "Error inserting Likes data to database");
			else
				return array("status" => true, "message" => "Updated likes data in database");
		}
		return array("status" => false, "message" => "Image Data has been manipulated/Not your image!");
	}

	public function getLikesData() {
		$stmt = $this->db->prepare("SELECT COUNT(*) as 'likes'
									FROM likes INNER JOIN images ON likes.IMAGEID = images.ID
									WHERE likes.IMAGEID = ?
									AND likes.LIKE = '1';");
		$stmt->bindParam(1, $_POST['imageid']);
		if (!$stmt->execute()) {
			return array("status" => false, "message" => "Error fetching likes count");
		} else {
			$count = $stmt->fetch(PDO::FETCH_ASSOC);
			$stmt = $this->db->prepare("SELECT likes.LIKE as 'liked'
										FROM likes INNER JOIN images ON likes.IMAGEID = images.ID
										WHERE likes.IMAGEID = ?
										AND likes.USERID = ?;");
			$stmt->bindParam(1, $_POST['imageid']);
			$stmt->bindParam(2, $_SESSION['id']);
			if (!$stmt->execute()) {
				return array("status" => false, "message" => "Error fetching likes data");
			} else {
				$likesData = $stmt->fetch(PDO::FETCH_ASSOC);
				if (!$likesData) {
					$likesData['userid']  = $_SESSION['id'];
					$likesData['likecount'] = $count['likes'];
					$likesData['liked'] = false;
				} else {
					unset($likesData[0]);
					$likesData['userid'] = $_SESSION['id'];
					$likesData['likecount'] = $count['likes'];
					if (strval($likesData['liked']) === "0")
						$likesData['liked'] = false;
					else
						$likesData['liked'] = true;
				}
				return $likesData;
			}
		}
	}
}
