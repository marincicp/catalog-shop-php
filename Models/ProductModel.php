<?php

namespace Models;

use Core\Database;
use Exception;
use ProductValidator;

require "./Models/CategoryModel.php";
require "./Http/Validators/ProductValidator.php";
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



   public static function get()
   {
      try {
         $products = Database::getInstance()->query("SELECT * FROM products")->get();

         return formatRes($products);
      } catch (Exception $err) {
         return ["code" => $err->getCode(), "error" => $err->getMessage()];
      }
   }

   public static function find($sku)
   {
      try {
         $product =   self::getItem($sku);
         return formatRes($product);
      } catch (Exception $err) {
         return ["code" => $err->getCode(), "error" => $err->getMessage()];
      }
   }


   public static function delete($sku)
   {
      try {
         self::getItem($sku);

         self::db()->query("DELETE FROM products WHERE SKU = :SKU", ["SKU" => $sku]);

         return ["message" => "Item successfully deleted", "code" => 200];
      } catch (Exception $err) {
         return ["code" => $err->getCode(), "error" => $err->getMessage()];
      }
   }



   public static function update($sku)
   {
      try {
         $product = self::getItem($sku);

         $data = json_decode(file_get_contents('php://input'), true);

         $validator = new ProductValidator();

         if (
            ! $validator->validate(data: $data)
         ) {
            $errors = $validator->errors();
            return ["error" => $errors, "code" => 400];
         }


         $description = $data["description"] ?? $product["description"];

         self::$db->query(
            "UPDATE products SET
                     name = :name, description = :description, 
                     price = :price, type = :type,
                     image_url = :image_url,
                     category_id = :category_id
                     WHERE SKU = :SKU",
            ["name" => $data["name"], "description" => $description, "price" => $data["price"], "type" => $data["type"], "image_url" => $data["image_url"], "category_id" => $data["category_id"], "SKU" => $sku]
         );



         self::updateProductAttributes($data, $product["id"]);


         $updatedProdcut = self::getItem($sku);

         return ["data" => $updatedProdcut, "code" => 200, "message" => "Product has been successfully updated"];
      } catch (Exception $err) {

         return ["code" => $err->getCode(), "error" => $err->getMessage()];
      }
   }


   protected static function updateProductAttributes($data, $productId)
   {
      if ($data["type"] === "physical") {
         $attributes["color"] = $data["color"];
         $attributes["shipping_price"] = $data["shipping_price"];
      }

      if ($data["type"] === "virtual") {
         $attributes["coupon_code"] = $data["coupon_code"];
         $attributes["expires_at"] = $data["expires_at"];
      }

      foreach ($attributes as $attribute => $value) {
         self::$db->query("UPDATE  attributes SET value = :value WHERE product_id = :product_id AND attribute = :attribute", ["attribute" => $attribute, "value" => $value, "product_id" => $productId]);
      }
   }

   public static function getItem($sku)
   {
      $item =   self::db()->query("SELECT * FROM products WHERE SKU = :SKU", ["SKU" => $sku])->find();

      if (!$item) {
         throw new Exception("No data found", 404);
      }

      return $item;
   }
}
