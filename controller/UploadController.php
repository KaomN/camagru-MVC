<?php

class UploadController {
	
	public $model;
	private static $res = [];
	private static $style = '<link rel="stylesheet" href="styles/upload.css">';
	private static $script = '<script src="scripts/upload.js"></script>';
	private static $navbar = true;

	public function indexAction() {
		$style = self::$style;
		$script = self::$script;
		$navbar = self::$navbar;
		$imageData = $this->model->FetchUploadedImages(self::$res);
		$res = self::$res;
		return require_once('view/upload.php');
	}

	public function checkRequest() {
		if (isset($_POST['request']) && $_POST['request'] === "uploadImage")
			self::uploadImage();
	}

	public function uploadImage() {
		$this->model->UploadImage(self::$res);
		echo(json_encode(array(self::$res)));
	}
}