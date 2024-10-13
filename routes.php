<?php

use Http\Controllers\ProductController;

$router->get("/products", ProductController::class, "index");