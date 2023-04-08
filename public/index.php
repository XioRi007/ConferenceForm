<?php
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
ini_set('display_errors', 1);
error_reporting(E_ALL);
// require('./components/autoload.php');
define('ROOT', dirname(__FILE__));
// $GLOBALS['configs'] = require(__DIR__ . '/..Core/config/config.php');
// use Core\Components\Router;
$router = new Core\Components\Router();
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: *");
$router->run();