<?php

namespace Core\Middleware;

use Core\Response;

require "./Core/functions.php";
class Middleware
{

   const MAP = ["auth" => AuthMidleware::class];

   public static  function resolve(string $middlewareKey)
   {
      $middleware = static::MAP[$middlewareKey] ?? null;

      if ($middleware) {
         return $middleware::handler();
      } else {
         abort(Response::NOT_FOUND, "Invalid middleware name");
      }
   }
}
