<?php

use Http\Controllers\ProductController;

$router->get("/products", ProductController::class, "index");

$router->get("/products/{SKU}", ProductController::class, "show");

$router->delete("/products/{SKU}", ProductController::class, "destroy");

$router->put("/products/{SKU}", ProductController::class, "update");

$router->post("/products", ProductController::class, "store");
