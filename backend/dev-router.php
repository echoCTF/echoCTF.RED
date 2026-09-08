<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

$webRoot = __DIR__ . '/web';
$path = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if (is_file($webRoot . $path)) {
    return false;
}
require $webRoot . '/index.php';