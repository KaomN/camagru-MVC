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
if (!isset($_SESSION['id'])) {
	Route::add("/profile", "UserController@membersOnly");
	Route::add("/upload", "UserController@membersOnly");
	Route::add("/settings", "UserController@membersOnly");
	Route::add("/settings/request", "UserController@notFound");
	Route::add("/upload/request", "UserController@notFound");
	Route::add("/gallery/request", "UserController@notFound");
	Route::add("/gallery/request", "UserController@notFound");
}
Route::add("/login", "UserController@showLogin");
Route::add("/login/", "UserController@showLogin");
Route::add("/login/request", "UserController@checkRequest");
Route::add("/login/notverified", "UserController@notVerified");

Route::add("/signup/", "UserController@showSignup");
Route::add("/signup", "UserController@showSignup");
Route::add("/signup/request", "UserController@checkRequest");
Route::add("/signup/success", "UserController@showSuccess");

Route::add("/forgotpassword", "UserController@showForgotPassword");
Route::add("/forgotpassword/request", "UserController@checkRequest");

Route::add("/resetpassword/request", "UserController@checkRequest");

Route::add("/logout", "UserController@logoutAction");

Route::add("/gallery", "GalleryController@indexAction");
Route::add("/gallery/", "GalleryController@indexAction");
Route::add("/gallery/request", "GalleryController@checkRequest");

Route::add("/profile", "ProfileController@indexAction");
Route::add("/profile/request", "ProfileController@checkRequest");

Route::add("/upload", "UploadController@indexAction");
Route::add("/upload/request", "UploadController@checkRequest");

Route::add("/settings", "SettingsController@indexAction");
Route::add("/settings/request", "SettingsController@checkRequest");

$route = Route::run();
// if (isset($_GET['action'])) {
// 	$request = $_GET['action'];
// 	if ($request === 'login') {
// 		$route = "LoginController@indexAction";
// 	} else if ($request === 'signup') {
// 		$route = "SignupController@indexAction";
// 	} else if ($request === 'forgotpassword') {
// 		$route = "ForgotPasswordController@indexAction";
// 	} else if ($request === 'gallery') {
// 		$route = "GalleryController@indexAction";
// 	} else if ($request === 'logout') {
// 		$route = "LogoutController@indexAction";
// 	} else if ($request === 'settings') {
// 		if (isset($_GET['request'])) {
// 			if ($_GET['request'] === "changeUsername") {
// 				$route = "SettingsController@changeUsername";
// 			}
// 		} else {
// 			$route = "SettingsController@indexAction";
// 		}
// 	} else if ($request === 'profile') {
// 		$route = "ProfileController@indexAction";
// 	} else if ($request === 'upload') {
// 		$route = "UploadController@indexAction";
// 	}
// }