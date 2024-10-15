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

$router->route($uri, $method, $query);

// TODO You should first create the database 'catalog' and
// TODO then run the code below.
// $db = new Database($config["database"]);
// $db->createTables();
// $db->insertDataIntoTable($dummyData["users"], $dummyData["sql"]["users"]);
// $db->insertDataIntoTable($dummyData["categories"], $dummyData["sql"]["categories"]);
// $db->insertDataIntoTable($dummyData["products"], $dummyData["sql"]["products"]);
// $db->insertDataIntoTable($dummyData["attributes"], $dummyData["sql"]["attributes"]);