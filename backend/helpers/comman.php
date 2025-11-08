<?php
use Lib\View;
use Lib\Database;
if (!function_exists('route')) {
    function route($name, $parameters = [])
    {
        $router = Router::getInstance();
        return $router->url($name, $parameters);
    }
}
if (!function_exists('redirect')) {
    function redirect($name, $parameters = [])
    {
        $router = Router::getInstance();
        header('Location: ' . $router->url($name, $parameters));
        exit();
    }
}
if (!function_exists('config')) {
    function config($key = null, $default = null)
    {
        static $config = null;

        if ($config === null) {
            $config = [];
            foreach (glob(CONFIG_PATH . '*.php') as $file) {
                $name = basename($file, '.php');
                $config[$name] = require $file;
            }
        }

        if ($key === null) {
            return $config;
        }

        // Support dot notation: e.g., config('app.name')
        $keys = explode('.', $key);
        $value = $config;

        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }

        return $value;
    }
}

// Global view helper (add to helpers.php)
function view($view, $data = [])
{
    // use Core\View;
    $viewPath = FRONTEND_PATH . "views/" . str_replace('.', '/', $view) . ".php";
    $viewEngine = new View($data);
    return $viewEngine->render($viewPath, $data);
}

// view_path helper for includes
function view_path($view)
{
    return FRONTEND_PATH . "views/" . str_replace('.', '/', $view) . ".php";
}


if (!function_exists('asset')) {
    function asset(string $path)
    {
        // Detect protocol (http / https)
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];

        // Determine base directory (relative to web root)
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

        // Remove '/public' if it exists at the end or in middle of the path
        // $base = preg_replace('#/frontend$#', '', $base); // end of path
        // $base = str_replace('/frontend/', '/', $base);   // middle of path

        // Build final URL
        $url = $protocol . $host . $base . '/frontend/' . ltrim($path, '/');

        // Normalize duplicate slashes (except after "http://")
        $url = preg_replace('#(?<!:)//+#', '/', $url);

        return $url;
    }
}

// db()
if(!function_exists('db')){
    function db(){
        $db = new Database();
        return $db->getConnection();
    }
}

// dbTable
if (!function_exists('dbTable')) {
    /**
     * Define a database table structure with optional seeds
     *
     * @param string $table Table name
     * @param array $columns Column definitions ['col_name' => 'SQL_TYPE ...']
     * @param array $seeds Optional default data
     * @return array
     */
    function dbTable(string $table, array $columns, array $seeds = []): array
    {
        return [
            'table'   => $table,
            'columns' => $columns,
            'seeds'   => $seeds
        ];
    }
}
