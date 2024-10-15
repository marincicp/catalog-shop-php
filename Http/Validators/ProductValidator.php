<?php

use Models\CategoryModel;
use Models\ProductModel;

class ProductValidator
{
   protected $errors = [];
   protected const PRODUCT_TYPES = ["virtual", "physical"];
   protected $categoyIds;

   public function __construct()
   {

      $this->categoyIds = CategoryModel::getCategoryIds();
   }


   public function validate($data, $includeSKU = false)
   {

      if ($includeSKU) {
         if (!isset($data["sku"]) || ProductModel::validateSku($data["sku"])) {
            $this->errors["sku"] = "SKU field must be unique";
         }
      }

      if (!isset($data["name"]) || !Validator::string($data["name"])) {
         $this->errors["name"] = "Name field is required";
      }

      if (!isset($data["type"]) || !in_array($data["type"], self::PRODUCT_TYPES)) {
         $this->errors["type"] = "Type field must be either 'virtual' or 'physical'";
      }

      if (!isset($data["image_url"]) || !Validator::string($data["image_url"])) {
         $this->errors["image_url"] = "Image Url field is required";
      }

      if (!isset($data["price"]) || !Validator::number($data["price"]) || $data["price"] === 0) {
         $this->errors["price"] = "Price field must be a number greater than 0";
      }

      if (!isset($data["category_id"]) || !in_array($data["category_id"], $this->categoyIds)) {
         $this->errors["category_id"] = "Category does not exist";
      }

      if (isset($data["type"])) {
         $this->validateProductTypesFields($data);
      }

      return empty($this->errors);
   }



   public function validateProductTypesFields($data)
   {
      switch ($data["type"]) {
         case "virtual":
            if (!isset($data["expires_at"]) || !Validator::string($data["expires_at"])) {
               $this->errors["expires_at"] = "Expires At field is required";
            }
            if (!isset($data["coupon_code"]) || !Validator::string($data["coupon_code"])) {
               $this->errors["coupon_code"] = "Cupon Code field is required";
            }
            break;

         case "physical":
            if (!isset($data["color"]) || !Validator::string($data["color"])) {
               $this->errors["color"] = "Color field is required";
            }
            if (!isset($data["shipping_price"]) || !Validator::string($data["shipping_price"])) {
               $this->errors["shipping_price"] = "Shipping Price field is required";
            }
            break;
      }
   }



   public function errors()
   {
      return $this->errors;
   }
}
