<?php
include_once('HelperModel.php');
ini_set('display_errors', 1);

class UploadModel extends HelperModel {
	public $db;

	public function FetchUploadedImages(&$res) {
		$stmt = $this->db->prepare("SELECT images.ID as 'id', images.USERID as 'userid', images.FILENAME as 'filename'
									FROM images
									WHERE images.USERID = ?
									ORDER BY images.ID DESC;");
		$stmt->bindParam(1, $_SESSION['id']);
		if (!$stmt->execute())
			$res = array("status" => false);
		else {
			$imageData = $stmt->fetchall(PDO::FETCH_ASSOC);
			if($imageData) {
				$res = array("status" => true);
				return $imageData;
			} else {
				$res = array("status" => true);
			}
		}
	}

	public function UploadImage() {
		define('MB', 1048576);

		function resizeImage(&$imageW, &$imageH, $image) {
			if ($imageW > 1000 || $imageH > 1000) {
				$newWidth = 1000;
				$newHeight = 1000;
				if($newWidth/$imageW < $newHeight/$imageH)
					$newHeight = $imageH * ($newWidth/$imageW);
				else
					$newWidth = $imageW * ($newHeight/$imageH);
				$imageResized = imagecreatetruecolor($newWidth, $newHeight);
				imagecopyresampled($imageResized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $imageW, $imageH);
				$imageW = $newWidth;
				$imageH = $newHeight;
				imagedestroy($image);
				return $imageResized;
			}
			return $image;
		}
		
		function insertFilter($image, $imageW, $imageH, $filterData) {
			$filter = imagecreatefrompng('src/filters/filter1.png');
			$filterOriginalW = imagesx($filter);
			$filterOriginalH = imagesy($filter);
			$filterW = $imageW * $filterData->width;
			$filterH = $imageH * $filterData->height;
			$filterDX = $imageW * $filterData->deltaX;
			$filterDY = $imageH * $filterData->deltaY;
			// Resize Filter according to filterData
			$filterResized = imagecreatetruecolor($filterW, $filterH);
			imagealphablending($filterResized, false);
			imagesavealpha($filterResized, true);
			imagecopyresampled($filterResized, $filter, 0, 0, 0, 0, $filterW, $filterH, $filterOriginalW, $filterOriginalH);
			// Merge uploaded image with filter
			imagecopyresampled($image, $filterResized, $filterDX, $filterDY, 0, 0, $filterW, $filterH, $filterW, $filterH);
			imagedestroy($filter);
			imagedestroy($filterResized);
			return $image;
		}
		
		function setPngBackgroundColor($image, $imageW, $imageH) {
			$backgroundImg = imagecreatetruecolor($imageW, $imageH);
			$color = imagecolorallocate($backgroundImg, 0, 90, 73);
			imagefill($backgroundImg, 0, 0, $color);
			imagecopy($backgroundImg, $image, 0, 0, 0, 0, $imageW, $imageH);
			$image = $backgroundImg;
			imagedestroy($backgroundImg);
			return ($image);
		}
		
		function checkExifData() {
			$exifData = exif_read_data($_FILES["file"]["tmp_name"]);
			$orientation = 1;
			if(isset($exifData['Orientation']))
				$orientation = $exifData['Orientation'];
			$image = imagecreatefromjpeg($_FILES['file']['tmp_name']);
			if ($orientation === 3)
				$image = imagerotate($image, 180, 0);
			else if ($orientation === 4)
				$image = imagerotate($image, 180, 0);
			else if ($orientation === 5)
				$image = imagerotate($image, -90, 0);
			else if ($orientation === 6)
				$image = imagerotate($image, -90, 0);
			else if ($orientation === 7)
				$image = imagerotate($image, 90, 0);
			else if ($orientation === 8)
				$image = imagerotate($image, 90, 0);
			return $image;
		}
		if (!file_exists("src/uploads"))
			mkdir("src/uploads", 0755, true);
		if (!file_exists("src/uploads/" . $_SESSION['username']))
			mkdir("src/uploads/" . $_SESSION['username'], 0755, true);
		$target_dir = "src/uploads/" . $_SESSION['username'] . "/";
		$fileExt = explode(".", $_FILES["file"]["name"]);
		$fileName = uniqid() . '.' . end($fileExt);
		$target_file = $target_dir . $fileName;
		$check = getimagesize($_FILES["file"]["tmp_name"]);
		$image = "";
		if($check === false) {
			return array("status" => false, "message" => "File is not an image!");
		} else {
			// Check file size max 20MB
			if ($_FILES["file"]["size"] > 20 * MB)
				return array("status" => false, "message" => "Image is too big, max 20MB");
			else {
				// Check if filter was added to the image
				if (!strcmp('jpg', end($fileExt)) || !strcmp('jpeg', end($fileExt))) {
					$image = checkExifData($image);
				}
				else if(!strcmp('png', end($fileExt)))
					$image = imagecreatefrompng($_FILES['file']['tmp_name']);
				else {
					return array("status" => false, "message" => "Image file not supported! Supported files: jpg, jpeg and png");
				}
				$imageW = imagesx($image);
				$imageH = imagesy($image);
				if (!strcmp('png', end($fileExt))) {
					$image = setPngBackgroundColor($image, $imageW, $imageH);
				}
				$image = resizeImage($imageW, $imageH, $image);
				$filterData = json_decode($_POST['filterData']);
				if ($filterData->enabled === true) {
					$image = insertFilter($image, $imageW, $imageH, $filterData);
				}
				// Upload the file to server
				if (imagejpeg($image, $target_file, 100)) {
					$stmt =$this->db->prepare("	INSERT INTO images (images.USERID, images.FILENAME, images.DATE)
												VALUE (?, ?, NOW());");
					$stmt->bindParam(1, $_SESSION['id']);
					$stmt->bindParam(2, $fileName);
					// If Database insertion fails, Delete the file from the server
					if (!$stmt->execute()) {
						unlink($target_file);
						return array("status" => false, "message" => "Error uploading image!");
					} else {
						$stmt = $this->db->prepare("SELECT images.ID as 'id', images.FILENAME as 'filename'
													FROM images
													WHERE images.USERID = ?
													AND images.FILENAME = ? LIMIT 1;");
						$stmt->bindParam(1, $_SESSION['id']);
						$stmt->bindParam(2, $fileName);
						if (!$stmt->execute()) {
							return array("status" => false, "message" => "Error uploading image!");
						} else {
							$uploadedImage = $stmt->fetch(PDO::FETCH_ASSOC);
							$uploadedImage['userid'] = $_SESSION['id'];
							$uploadedImage['username'] = $_SESSION['username'];
							$uploadedImage['src'] = "src/uploads/" . $_SESSION['username'] . "/" . $fileName;
							$uploadedImage['status'] = true;
							return $uploadedImage;
						}
					}
				} else {
					return array("status" => false, "message" => "Error uploading image!");
				}
				imagedestroy($image);
			}
		}
	}
	
	public function CreateThumbnail() {
		function createHtmlTag($imageData) {
			$thumbnails = "";
			$imageDir = "src/uploads/" . $_SESSION['username'] . "/";
			$thumbnails .=	'<div class="thumbnail">' .
								'<img src="' . $imageDir . $imageData['filename'] .'" alt="Thumbnail" data-userid="' . $imageData['userid'] . '" data-id="' . $imageData['id'] . '" data-filename="' . $imageData['filename'] . '">' .
								'<i class="material-icons delete" title="Delete">delete</i>'.
							'</div>';
			return $thumbnails;
		}
		$stmt = $this->db->prepare("SELECT images.ID as 'id', images.USERID as 'userid', images.FILENAME as 'filename'
									FROM images
									WHERE images.USERID = ?
									ORDER BY images.ID
									DESC LIMIT 2;");
		$stmt->bindParam(1, $_SESSION['id']);
		if (!$stmt->execute())
			return array("status" => false, "message" => "Error fetching image thumbnails!");
		else {
			$imageData = $stmt->fetch(PDO::FETCH_ASSOC);
			return array("status" => true, "tag" => createHtmlTag($imageData));
		}
	}

	public function DeleteThumbnail() {
		return parent::DeleteImageHelper($this->db, true, true, true);
	}
}
