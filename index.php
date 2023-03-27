<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require('./components/autoload.php');
define('ROOT', dirname(__FILE__));
$GLOBALS['configs'] = require(__DIR__ . '/config/config.php');
use Components\Router;
$router = new Router;
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: *");
$router->run();