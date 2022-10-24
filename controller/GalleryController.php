<?php

class GalleryController {
	
	public $model;
	private static $style = '<link rel="stylesheet" href="styles/gallery.css">';
	private static $script = '<script src="scripts/gallery.js"></script>';
	private static $navbar = true;

	public function indexAction() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		$imagePack = $this->model->GetGalleryImages(0);
		if ($imagePack['status'] === false)
			$imagePack['tag'] = '<div style="display:flex;align-items:center;justify-content:center;">No images uploaded!</div>';
		return require_once('view/gallery.php');
	}

	public function checkRequest() {
		if (isset($_POST['request']) && $_POST['request'] === "getGalleryImages")
			self::getGalleryImages();
		else if (isset($_POST['request']) && $_POST['request'] === "getComments")
			self::getComments();
		else if (isset($_POST['request']) && $_POST['request'] === "getLikesData")
			self::getLikesData();
		else if (isset($_POST['request']) && $_POST['request'] === "deleteImage" && isset($_SESSION['id']))
			self::deleteImage();
		else if (isset($_POST['request']) && $_POST['request'] === "insertComment" && isset($_SESSION['id']))
			self::insertComment();
		else if (isset($_POST['request']) && $_POST['request'] === "likeImage" && isset($_SESSION['id']))
			self::likeImage();
		else if (isset($_POST['request']) && $_POST['request'] === "unlikeImage" && isset($_SESSION['id']))
			self::unlikeImage();
		else
			header("location: /gallery");
	}

	private function getGalleryImages() {
		echo json_encode($this->model->GetGalleryImages($_POST['start']));
	}

	private function deleteImage() {
		echo json_encode($this->model->DeleteImage());
	}

	private function getComments() {
		echo json_encode($this->model->GetComments());
	}

	private function insertComment() {
		echo json_encode($this->model->InsertComment());
	}

	private function likeImage() {
		echo json_encode($this->model->LikeImage());
	}

	private function unlikeImage() {
		echo json_encode($this->model->unlikeImage());
	}

	private function getLikesData() {
		echo json_encode($this->model->getLikesData());
	}
}