<?php

use Models\CategoryModel;
use Models\ProductModel;


require_once "ImageValidator.php";

class ProductValidator
{
   protected $errors = [];
   protected const PRODUCT_TYPES = ["virtual", "physical"];

   public function __construct() {}


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

      if (!isset($data["image"]) || !ImageValidator::validate($data["image"])) {
         $this->errors["image"] = "The image must be in one of the following formats: JPG, JPEG, PNG, WEBP, and smaller than 20 MB";
      }


      if (!isset($data["price"]) || !Validator::number($data["price"]) || $data["price"] === 0) {
         $this->errors["price"] = "Price field must be a number greater than 0";
      }

      if (!isset($data["category_id"])  || !CategoryModel::validateCategory($data["category_id"])) {
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