<?php 
define('BASE_URL', getenv('APP_URL') ?? 'http://localhost/web_2/');
define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('CONFIG_PATH', ROOT . 'config' . DIRECTORY_SEPARATOR);
define('FRONTEND_PATH', ROOT . 'frontend' . DIRECTORY_SEPARATOR);
define('BACKEND_PATH', ROOT . 'backend' . DIRECTORY_SEPARATOR);
define('HELPERS_PATH', BACKEND_PATH . 'helpers' . DIRECTORY_SEPARATOR);
define('MODAL_PATH', BACKEND_PATH . 'modal' . DIRECTORY_SEPARATOR);
define('ASSETS_PATH', FRONTEND_PATH . 'assets' . DIRECTORY_SEPARATOR);
define('ROUTES_PATH', FRONTEND_PATH . 'routes' . DIRECTORY_SEPARATOR);
define('LIB_PATH', ROOT . 'lib' . DIRECTORY_SEPARATOR);
// Middleware
define('MIDDLEWARE_PATH', BACKEND_PATH . 'middleware' . DIRECTORY_SEPARATOR);
define('API_PATH', BACKEND_PATH . 'API' . DIRECTORY_SEPARATOR);