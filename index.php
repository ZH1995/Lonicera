<?php
define('_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('_SYS_PATH', _ROOT . 'Lonicera' . DIRECTORY_SEPARATOR);
define('_APP', _ROOT . 'app' . DIRECTORY_SEPARATOR);
require _SYS_PATH . 'Lonicera.php';
require _SYS_PATH . 'config.php';

$app = new Lonicera;
$app->run();
