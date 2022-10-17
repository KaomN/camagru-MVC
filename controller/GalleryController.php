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
		$_SESSION['gallerystart'] = 0;
		$images = $this->model->GetGalleryImages($_SESSION['gallerystart']);
		return require_once('view/gallery.php');
	}
}