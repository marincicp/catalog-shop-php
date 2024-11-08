<?php

use Http\Controllers\CategoryController;
use Http\Controllers\ProductController;
use Http\Controllers\RegistrationController;
use Http\Controllers\SessionController;

$router->get("/products", ProductController::class, "index")->only("auth");
$router->get("/products/{SKU}", ProductController::class, "show");
$router->delete("/products/{SKU}", ProductController::class, "destroy");
$router->put("/products/{SKU}", ProductController::class, "update");
$router->post("/products", ProductController::class, "store");

$router->get("/categories", CategoryController::class, "index");


$router->post("/register", RegistrationController::class, "store");
$router->post("/login", SessionController::class, "store");
$router->delete("/logout", SessionController::class, "destroy");
$router->get("/getSession", SessionController::class, "getSession");
