<?php

use Core\Router;

require "vendor/autoload.php";


$router = new Router();
$routes = require "routes.php";

$parsedUri = parse_url($_SERVER["REQUEST_URI"]);
$uri = $parsedUri["path"];
$query = $parsedUri["query"] ?? "";
$method = $_SERVER["REQUEST_METHOD"];


$router->route($uri, $method, $query);