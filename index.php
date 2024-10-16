<?php

use Core\Database;
use Core\Router;

require "vendor/autoload.php";
$dummyData =  require_once "dummyData.php";


require_once "Core/Validator.php";
$router = new Router();
$routes = require "routes.php";
$config = require "Core/config.php";

$parsedUri = parse_url($_SERVER["REQUEST_URI"]);
$uri = $parsedUri["path"];
$query = $parsedUri["query"] ?? "";
$method = $_SERVER["REQUEST_METHOD"];

// TODO You should first create the database 'catalog' and
// TODO then run the code below.
// $db = new Database($config["database"]);
// $db->createAndSeedTables($dummyData);

$router->route($uri, $method, $query);
