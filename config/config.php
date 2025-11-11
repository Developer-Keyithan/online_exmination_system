<?php 
if (!defined('BASE_URL')) define('BASE_URL', getenv('APP_URL') ?? 'http://localhost/NIT/exam');
if (!defined('ROOT')) define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);
if (!defined('CONFIG_PATH')) define('CONFIG_PATH', ROOT . 'config' . DIRECTORY_SEPARATOR);
if (!defined('FRONTEND_PATH')) define('FRONTEND_PATH', ROOT . 'frontend' . DIRECTORY_SEPARATOR);
if (!defined('BACKEND_PATH')) define('BACKEND_PATH', ROOT . 'backend' . DIRECTORY_SEPARATOR);
if (!defined('HELPERS_PATH')) define('HELPERS_PATH', BACKEND_PATH . 'helpers' . DIRECTORY_SEPARATOR);
if (!defined('MODAL_PATH')) define('MODAL_PATH', BACKEND_PATH . 'modal' . DIRECTORY_SEPARATOR);
if (!defined('ASSETS_PATH')) define('ASSETS_PATH', FRONTEND_PATH . 'assets' . DIRECTORY_SEPARATOR);
if (!defined('ROUTES_PATH')) define('ROUTES_PATH', FRONTEND_PATH . 'routes' . DIRECTORY_SEPARATOR);
if (!defined('LIB_PATH')) define('LIB_PATH', ROOT . 'lib' . DIRECTORY_SEPARATOR);
if (!defined('MIDDLEWARE_PATH')) define('MIDDLEWARE_PATH', BACKEND_PATH . 'middleware' . DIRECTORY_SEPARATOR);
if (!defined('API_PATH')) define('API_PATH', BACKEND_PATH . 'API' . DIRECTORY_SEPARATOR);
if (!defined('VERSION')) define('VERSION', '1.0.0');
if (!defined('NAME')) define('NAME', 'Online Exam');
if (!defined('SKIP_ROOT')) define('SKIP_ROOT', false);
