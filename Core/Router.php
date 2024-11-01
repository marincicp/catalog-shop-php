<?php

namespace Core;

use Core\Middleware\Middleware;

require_once "./Core/Middleware/Middleware.php";

class Router
{

   private $routes = [];

   public function add($uri, $controller, $action, $method)
   {
      $this->routes[] = [
         "uri" => $uri,
         "controller" => $controller,
         "action" => $action,
         "method" => $method,
         "middleware" => null
      ];


      return $this;
   }

   public function only($middlewareKey)
   {
      $this->routes[array_key_last($this->routes)]["middleware"] = $middlewareKey;
   }


   public function get($uri, $controller, $action)
   {
      return  $this->add($uri, $controller, $action, "GET");
   }

   public function post($uri, $controller, $action)
   {
      return   $this->add($uri, $controller, $action, "POST");
   }

   public function delete($uri, $controller, $action)
   {
      return    $this->add($uri, $controller, $action, "DELETE");
   }
   public function put($uri, $controller, $action)
   {
      return   $this->add($uri, $controller, $action, "PUT");
   }


   public function route($uri, $method, $query)
   {
      $uriParts = explode("/", trim($uri, "/"),);

      foreach ($this->routes as $route) {
         $routeParts = explode("/", trim($route["uri"], "/"));

         if ($uriParts[0] === $routeParts[0] && count($uriParts) === count($routeParts) && $method === $route["method"]) {


            if ($route["middleware"] !== null) {
               Middleware::resolve(
                  $route["middleware"]
               );
            }


            $id = $uriParts[1] ??  "";

            return     call_user_func(
               [$route["controller"], $route["action"]],
               $id
            );
         }
      }
      $this->abort();
   }






   protected function abort($code = 404, $message = "The requested route could not be found")
   {
      http_response_code($code);
      header("Content-Type:application/json");
      echo json_encode(["error" => $message]);
      exit();
   }
}
