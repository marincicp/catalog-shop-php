<?php

namespace Models;

use Core\Database;


class Model
{

   static protected $db;


   /**
    * Get instance of the Database connection
    * @return \Core\Database
    */
   protected static function db(): Database
   {
      if (!static::$db) {
         static::$db = Database::getInstance();
      }
      return static::$db;
   }
}
