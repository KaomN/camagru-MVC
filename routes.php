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

if (!isset($_SESSION['id'])) {
	Route::add("/profile", "UserController@membersOnly");
	Route::add("/upload", "UserController@membersOnly");
	Route::add("/settings", "UserController@membersOnly");
	Route::add("/settings/request", "UserController@notFound");
	Route::add("/upload/request", "UserController@notFound");
	Route::add("/gallery/request", "UserController@notFound");
	Route::add("/gallery/request", "UserController@notFound");
}
Route::add("/login", "UserController@loginAction");
Route::add("/login/notverified", "UserController@notVerified");
Route::add("/signup", "UserController@signupAction");
Route::add("/signup/success", "UserController@showSuccess");
Route::add("/forgotpassword", "UserController@forgotPasswordAction");
Route::add("/logout", "UserController@logoutAction");
Route::add("/gallery", "GalleryController@indexAction");
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