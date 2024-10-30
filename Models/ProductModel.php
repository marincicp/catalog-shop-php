<?php

namespace Models;

use Core\Session;
use Exception;
use ProductResources;
use ProductValidator;

require_once "Model.php";
require_once "./Models/CategoryModel.php";
require_once "./Http/Validators/ProductValidator.php";
require_once "./Core/functions.php";
require_once "./Http/Resources/ProductResources.php";
class ProductModel extends Model
{

   public static function get()
   {
      try {

         $sql = "SELECT products.*, 
                categories.name AS category_name,
                GROUP_CONCAT(CASE 
                WHEN attributes.attribute = 'color' THEN attributes.value 
                END) AS color,
                GROUP_CONCAT(CASE 
                WHEN attributes.attribute = 'shipping_price' THEN attributes.value 
                END) AS shipping_price,
                GROUP_CONCAT(CASE 
                WHEN attributes.attribute = 'coupon_code' THEN attributes.value 
                END) AS coupon_code,
                GROUP_CONCAT(CASE 
                WHEN attributes.attribute = 'expires_at' THEN attributes.value 
                END) AS expires_at
                FROM products 
                INNER JOIN categories ON products.category_id = categories.id 
                LEFT JOIN attributes ON attributes.product_id = products.id";

         $conditions = [];
         $params = [];

         if (isset($_GET["name"])) {
            $params["name"] = "%" .  $_GET["name"] . "%";
            $conditions[] = "products.name LIKE :name";
         }

         if (isset($_GET["category"])) {
            $params["category"] = $_GET["category"];
            $conditions[] = "categories.name = :category";
         }

         if (isset($_GET["type"])) {
            $params["type"] = $_GET["type"];
            $conditions[] = "products.type = :type";
         }
         if (isset($_GET["minPrice"])) {
            $params["minPrice"] = $_GET["minPrice"];
            $conditions[] = "products.price  >= :minPrice";
         }
         if (isset($_GET["maxPrice"])) {
            $params["maxPrice"] = $_GET["maxPrice"];
            $conditions[] = "products.price  <= :maxPrice";
         }


         if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
         }

         $sql .= " GROUP BY products.id";

         $products = self::db()->query($sql, $params)->get();

         return ProductResources::collection($products);
      } catch (Exception $err) {
         return ["code" => $err->getCode(), "error" => $err->getMessage()];
      }
   }

   public static function find($id): array
   {
      try {
         $data = self::getItem(sku: $id);

         return  ProductResources::single($data);
      } catch (Exception $err) {
         return ["code" => $err->getCode(), "error" => $err->getMessage()];
      }
   }


   public static function delete($sku)
   {
      try {
         $product =    self::getItem($sku);
         $curUser = Session::get("user");

         // authorize($product["user_id"] === $curUser["id"]);

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
         $data  = decodeJson();
         $validator = new ProductValidator();

         $user = Session::get("user");
         authorize(!(empty($product["user_id"]) && empty($user["id"]))  &&  $product["user_id"] ===  $user["id"]);

         if (
            !$validator->validate(data: $data)
         ) {
            $errors = $validator->errors();
            return ["error" => $errors, "code" => 400];
         }


         $description = $data["description"] ?? $product["description"];

         self::$db->query(
            "UPDATE products SET
                     name = :name, description = :description, 
                     price = :price, type = :type,
                     category_id = :category_id
                     WHERE SKU = :SKU",
            ["name" => $data["name"], "description" => $description, "price" => $data["price"], "type" => $data["type"], "category_id" => $data["category_id"], "SKU" => $sku]
         );



         self::updateProductAttributes($data, $product["id"], "update");


         $updatedProduct = self::getItem($sku);

         ProductResources::single($updatedProduct, "Product has been successfully updated");
      } catch (Exception $err) {

         return ["code" => $err->getCode(), "error" => $err->getMessage()];
      }
   }


   protected static function updateProductAttributes($data, $productId, $type = "create")
   {
      if ($data["type"] === "physical") {
         $attributes["color"] = $data["color"];
         $attributes["shipping_price"] = $data["shipping_price"];
      }

      if ($data["type"] === "virtual") {
         $attributes["coupon_code"] = $data["coupon_code"];
         $attributes["expires_at"] = $data["expires_at"];
      }


      if ($type === "create") {
         foreach ($attributes as $attribute => $value) {
            self::db()->query("INSERT INTO attributes  (attribute, value, product_id) VALUES (:attribute, :value, :product_id)", ["attribute" => $attribute, "value" => $value, "product_id" => $productId]);
         }
      } else {
         foreach ($attributes as $attribute => $value) {
            self::$db->query("UPDATE  attributes SET value = :value WHERE product_id = :product_id AND attribute = :attribute", ["attribute" => $attribute, "value" => $value, "product_id" => $productId]);
         }
      }
   }


   public static function getPostData()
   {

      $fields = ["name", "sku", "description", "price", "type", "category_id", "coupon_code", "expires_at", "shipping_price", "color", "user_id"];



      foreach ($fields as $field) {
         $data[$field] = $_POST[$field] ?? "";
      }

      $data["image"] = $_FILES["image_url"] ?? "";
      return $data;
   }

   public static function store()
   {
      try {

         $data = self::getPostData();

         authorize($data["user_id"]);

         $validator = new ProductValidator();

         if (!$validator->validate($data, true)) {
            $errors = $validator->errors();
            return ["error" => $errors, "code" => 400];
         }


         $imagePath = self::saveImage($data["image"]);

         $description = $data["description"] ?? "";

         self::db()->query("INSERT INTO products
         (name,  SKU, type, price, category_id, description, image_url, user_id) VALUES
        (:name,  :SKU, :type, :price, :category_id, :description, :image_url, :user_id)", ["name" => $data["name"], "SKU" => $data["sku"], "type" => $data["type"], "price" => $data["price"], "category_id" => $data["category_id"], "description" => $description, "image_url" => $imagePath, "user_id" => $data["user_id"]]);

         $productId = self::db()->conn()->lastInsertId();

         self::updateProductAttributes($data, $productId, "create");

         $createdProduct = self::getItem($data["sku"]);

         return ["data" => $createdProduct, "code" => 200, "message" => "Product has been successfully created"];
      } catch (Exception $err) {
         return ["code" => $err->getCode(), "error" => $err->getMessage()];
      }
   }


   public static function validateSku($sku)
   {
      return self::db()->query("SELECT COUNT(*) FROM products WHERE SKU = :SKU", ["SKU" => $sku])->count();
   }
   public static function getItem($sku)
   {
      $item = self::db()->query(
         "SELECT  products.*, 
         categories.name AS category_name,
          GROUP_CONCAT(CASE 
          WHEN attributes.attribute = 'color' THEN attributes.value 
          END) AS color,
          GROUP_CONCAT(CASE 
          WHEN attributes.attribute = 'shipping_price' THEN attributes.value 
          END) AS shipping_price,
          GROUP_CONCAT(CASE 
          WHEN attributes.attribute = 'coupon_code' THEN attributes.value 
          END) AS coupon_code,
          GROUP_CONCAT(CASE 
          WHEN attributes.attribute = 'expires_at' THEN attributes.value 
          END) AS expires_at
         FROM products INNER JOIN 
         categories ON products.category_id = categories.id 
         LEFT JOIN attributes 
         ON attributes.product_id = products.id  
         WHERE SKU = :SKU
         GROUP BY products.id;",
         ["SKU" => $sku]
      )->find();

      if (!$item) {
         throw new Exception("No data found", 404);
      }

      return $item;
   }


   public static function saveImage($image)
   {
      $imageName = baseName($image["name"]);
      $newImagePath = "productImages/" . $imageName;

      move_uploaded_file($image["tmp_name"], $newImagePath);

      return $newImagePath;
   }
}
