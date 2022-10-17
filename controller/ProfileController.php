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
		return require_once('view/profile.php');
	}
}