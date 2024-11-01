<?php

use Core\Database;
use Core\Router;

require "vendor/autoload.php";
$dummyData =  require_once "dummyData.php";


require_once "Core/Validator.php";
$router = new Router();
$routes = require "routes.php";


header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Access-Control-Allow-Methods: GET, POST,PUT,DELETE, OPTIONS");

$parsedUri = parse_url($_SERVER["REQUEST_URI"]);
$uri = $parsedUri["path"];
$query = $parsedUri["query"] ?? "";
$method = $_SERVER["REQUEST_METHOD"];

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
   http_response_code(200);
   exit();
}

session_start();
// TODO You should first create the database 'catalog' and
// TODO then run the code below.
// $db =  Database::getInstance();
// $db->createAndSeedTables($dummyData);

$router->route($uri, $method, $query);
