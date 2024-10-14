<?php

namespace Models;

use Core\Database;
use PDOException;

class ProductModel
{
   static $db;

   public static function db()
   {
      if (!self::$db) {
         self::$db = Database::getInstance();
      }
      return self::$db;
   }

   public static function getItem($sku)
   {
      return self::db()->query("SELECT * FROM products WHERE SKU = :SKU", ["SKU" => $sku])->find();
   }


   public static function get()
   {
      try {
         $products = Database::getInstance()->query("SELECT * FROM products")->get();

         return formatRes($products);
      } catch (\Exception $err) {
         return ["code" => 500, "error" => $err->getMessage()];
      }
   }

   public static function find($sku)
   {
      try {

         $product =   self::getItem($sku);
         return formatRes($product);
      } catch (\Exception $err) {
         dd($err->getMessage());
      }
   }


   public static function delete($sku)
   {
      try {
         $product = self::getItem($sku);

         if (empty($product)) {
            return ["error" => "No data found", "code" => 404, "data" => []];
         }

         self::db()->query("DELETE FROM products WHERE SKU = :SKU", ["SKU" => $sku]);

         return ["message" => "Item successfully deleted", "code" => 200];
      } catch (\Exception $err) {
         return ["code" => 500, "error" => $err->getMessage()];
      }
   }
}
