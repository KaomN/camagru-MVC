<?php


$routeList = [];

class Route {
	public static function add($routeName, $ControllerAction) {
		$GLOBALS['routelist'][] = ['name'=>$routeName, 'action'=>$ControllerAction];
	}

	public static function run() {
		$request = $_SERVER['REQUEST_URI'];
		foreach($GLOBALS['routelist'] as $route) {
			if ($route['name'] == $request) {
				return $route['action'];
			}
		}
	}
}

if ($_GET['url'] == 'verification') {
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	Route::add($_SERVER['REQUEST_URI'], "UserController@verifyUser");
}
if ($_GET['url'] == 'resetpassword' && $_SERVER['REQUEST_URI'] != "/resetpassword/request") {
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	Route::add($_SERVER['REQUEST_URI'], "UserController@showResetPassword");
}
if ($_GET['url'] == 'email' && $_SERVER['REQUEST_URI'] != "/email/request") {
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	Route::add($_SERVER['REQUEST_URI'], "UserController@showEmailChange");
}
if (!isset($_SESSION['id'])) {
	Route::add("/profile", "UserController@membersOnly");
	Route::add("/upload", "UserController@membersOnly");
	Route::add("/settings", "UserController@membersOnly");
	Route::add("/settings/request", "UserController@notFound");
	Route::add("/upload/request", "UserController@notFound");
	Route::add("/profile/request", "UserController@notFound");
}
Route::add("/login", "UserController@showLogin");
Route::add("/login/", "UserController@showLogin");
Route::add("/login/request", "UserController@checkRequest");
Route::add("/signup", "UserController@showSignup");
Route::add("/signup/", "UserController@showSignup");
Route::add("/signup/request", "UserController@checkRequest");
Route::add("/forgotpassword", "UserController@showForgotPassword");
Route::add("/forgotpassword/request", "UserController@checkRequest");
Route::add("/resetpassword/request", "UserController@checkRequest");
Route::add("/email/request", "UserController@checkRequest");
Route::add("/logout", "UserController@logoutAction");
Route::add("/gallery", "GalleryController@indexAction");
Route::add("/gallery/", "GalleryController@indexAction");
Route::add("/gallery/request", "GalleryController@checkRequest");
Route::add("/profile", "ProfileController@indexAction");
Route::add("/profile/request", "ProfileController@checkRequest");
Route::add("/upload", "UploadController@indexAction");
Route::add("/upload/request", "UploadController@checkRequest");
Route::add("/settings", "SettingsController@showSettings");
Route::add("/settings/request", "SettingsController@checkRequest");

$route = Route::run();