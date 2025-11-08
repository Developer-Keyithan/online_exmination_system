<?php
session_start();
// sample user set session
// $_SESSION['user'] = 'admin';
// display errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// set timezoon colombo
date_default_timezone_set('Asia/Colombo');
// echo date('Y-m-d H:i:s');
// load autoload
require_once 'autoload.php';
// echo config('app.name');
// Create router instance (Singleton)
$router = Router::getInstance();
$router->loadRoutes('web');
$router->route();
$response = $router->dispatch('web');

if (is_string($response)) {
    echo $response;
}

// database connection
use Lib\Database;
$db = new Database();
// Auto sync all models (create/alter tables + seeds)
$db->syncModels();
$db->syncSeeds();
