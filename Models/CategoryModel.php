<?php

namespace Models;

require_once "Model.php";

class CategoryModel extends Model
{


   /**
    * Get all categories from the database
    * @return array
    */
   public static function get(): array
   {
      try {
         $products = static::db()->query("SELECT id,name FROM categories")->get();

         return formatRes($products);
      } catch (\Exception $err) {
         return ["code" => 500, "error" => $err->getMessage()];
      }
   }

   /**
    * Check if a category with the given id exists
    * @param int $id
    * @return bool
    */
   public static function validateCategory(int $id): bool
   {
      $res = static::db()->query("SELECT COUNT(*) FROM categories WHERE id = :id", ["id" => $id])->count();

      return $res > 0;
   }
}
