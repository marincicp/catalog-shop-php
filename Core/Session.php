<?php

namespace Core;

use Models\UserModel;

class Session
{

   public static function set($key, $value)
   {
      return  $_SESSION[$key] = $value;
   }


   public static function get($key, $default = null)
   {
      return $_SESSION[$key] ?? $default;
   }





   public static function clear()
   {


      return $_SESSION = [];
   }
}
