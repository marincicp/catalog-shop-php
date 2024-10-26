<?php

namespace Models;

use Core\Database;


class Model
{

   static $db;

   public static function db()
   {
      if (!self::$db) {
         self::$db = Database::getInstance();
      }
      return self::$db;
   }
}
