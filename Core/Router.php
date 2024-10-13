<?php

namespace Core;

class Router
{

   private $routes = [];



   private function add($uri, $controller, $action, $method)
   {
      return $this->routes[] = [
         "uri" => $uri,
         "controller" => $controller,
         "action" => $action,
         "method" => $method
      ];
   }


   public function get($uri, $controller, $action)
   {
      $this->add($uri, $controller, $action, "GET");
   }

   public function post($uri, $controller, $action)
   {
      $this->add($uri, $controller, $action, "POST");
   }

   public function delete($uri, $controller, $action)
   {
      $this->add($uri, $controller, $action, "DELETE");
   }
   public function put($uri, $controller, $action)
   {
      $this->add($uri, $controller, $action, "PUT");
   }


   public function route($uri, $method, $query)
   {
      foreach ($this->routes as $route) {

         if ($uri === $route["uri"] && $method === $route["method"]) {
            return     call_user_func([$route["controller"], $route["action"]], [$query]);
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