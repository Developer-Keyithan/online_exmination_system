<?php
// load config/config.php
$envFile ='.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', trim($line), 2) + [1 => ''];
        putenv("$name=$value");
        $_ENV[$name] = $value;
    }
}
require_once 'config/config.php';
// load ROUTES class
foreach (glob(LIB_PATH.'*.php') as $filename) {
    require_once $filename;
}
// require_once LIB_PATH.'Router.php';
// require_once LIB_PATH.'Views.php';
// load all modals
foreach (glob(MODAL_PATH.'*.php') as $filename) {
    require_once $filename;
}
// laod all file in helpers
foreach (glob(HELPERS_PATH.'*.php') as $filename) {
    require_once $filename;
}