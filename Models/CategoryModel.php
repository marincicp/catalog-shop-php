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
         $products = Database::getInstance()->query("SELECT * FROM categories")->get();

         return formatRes($products);
      } catch (\Exception $err) {
         return ["code" => 500, "error" => $err->getMessage()];
      }
   }


   public static function getCategoryIds()
   {
      $res = Database::getInstance()->query("SELECT id FROM categories")->get();

      return  array_column($res, "id");
   }
}
