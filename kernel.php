<?php
session_start();

include_once('config/database.php');
include_once('config/setup.php');
include_once('config/connection.php');

spl_autoload_register(function ($class_name) {
	if (file_exists('./controller/' . $class_name . '.php')) {
		include_once './controller/' . $class_name . '.php';
	} else if (file_exists('./model/' . $class_name . '.php')) {
		include_once './model/' . $class_name . '.php';
	}
});

$db = Connection::connect($DB_DSN, $DB_NAME, $DB_USER, $DB_PASSWORD);
include_once('routes.php');
if(!empty($route)) {
	$routes = explode('@', $route);
	$controller = ucfirst($routes[0]);
	$model = ucfirst(str_replace("Controller", "", $routes[0])) . "Model";
	$action = lcfirst($routes[1]);
} else if ($_SERVER['REQUEST_URI'] === "/"){
	$controller = "UserController";
	$model = "UserModel";
	$action = "showLogin";
} else {
	$controller = "UserController";
	$model = "UserModel";
	$action = "notFound";
}
$loadNew = new $controller();
$model = new $model();
$loadNew->model=$model;
$model->db = $db;
$index = $loadNew->$action();