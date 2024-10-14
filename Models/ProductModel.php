<?php

namespace Models;

use Core\Database;
use PDOException;

class ProductModel
{
   public static function get()
   {

      try {
         $products = Database::getInstance()->query("SELECT * FROM products")->get();

         return formatRes($products);
      } catch (\Exception $e) {
         return ["code" => 500, "error" => $e->getMessage()];
      }
   }


   public static function find($sku)
   {
      try {

         $product = Database::getInstance()->query("SELECT * FROM products WHERE SKU = :SKU", ["SKU" => $sku])->find();

         return formatRes($product);
      } catch (\Exception $err) {
         dd($err->getMessage());
      }
   }
}
