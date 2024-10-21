<?php

namespace Models;

use Core\Database;

class CategoryModel
{

   static $db;


   public static function db()
   {
      if (!self::$db) {
         self::$db = Database::getInstance();
      }
      return self::$db;
   }

   public static function get()
   {
      try {
         $products = Database::getInstance()->query("SELECT id,name FROM categories")->get();

         return formatRes($products);
      } catch (\Exception $err) {
         return ["code" => 500, "error" => $err->getMessage()];
      }
   }


   public static function validateCategory($id)
   {
      $res = self::db()->query("SELECT COUNT(*) FROM categories WHERE id = :id", ["id" => $id])->count();

      return $res > 0;
   }
}
