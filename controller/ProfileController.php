<?php

class ProfileController {
	
	public $model;
	private static $style = '<link rel="stylesheet" href="styles/profile.css">';
	private static $script = '<script src="scripts/profile.js"></script>';
	private static $navbar = true;

	public function indexAction() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		$images = $this->model->GetProfileImages();
		return require_once('view/profile.php');
	}

	public function checkRequest() {
		if (isset($_POST['request']) && $_POST['request'] === "getComments")
			self::getComments();
		else if (isset($_POST['request']) && $_POST['request'] === "deleteImage" && isset($_SESSION['id']))
			self::deleteImage();
		else if (isset($_POST['request']) && $_POST['request'] === "insertComment" && isset($_SESSION['id']))
			self::insertComment();
		else
			header("location: /profile");
	}

	private function getComments() {
		echo json_encode($this->model->GetComments());
	}

	private function deleteImage() {
		echo json_encode($this->model->DeleteImage());
	}

	private function insertComment() {
		echo json_encode($this->model->InsertComment());
	}
}