<?php

namespace Core\Middleware;

use Core\Jwt;
use Core\Response;

class GuestMiddleware
{



   public static function handler()
   {

      $token = getJwtToken();
      if ($token || Jwt::verifyJWT($token)) {
         abort(Response::FORBIDDEN, "Access denied: You do not have permission to perform this action");
      };
   }
}
