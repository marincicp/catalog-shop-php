<?php

use Http\Controllers\ProductController;

$router->get("/products", ProductController::class, "index");

$router->get("/products/{SKU}", ProductController::class, "show");

$router->delete("/products/{SKU}", ProductController::class, "destroy");
