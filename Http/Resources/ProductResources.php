<?php

use Enums\ProductType;
use Http\Resources\JsonResource;

require "./Enums/ProductType.php";
class ProductResources extends JsonResource

{
   protected static function formatResource($product)
   {
      $resourceData =  [
         "id" => $product["id"],
         "SKU" => $product["SKU"],
         "name" => $product["name"],
         "description" => $product["description"],
         "price" => $product["price"],
         "type" => $product["type"],
         "category_id" => $product["category_id"],
         "user_id" => $product["user_id"],
         "category_name" => $product["category_name"],
         "image_url" => $product["image_url"]
      ];

      if ($product["type"] === ProductType::VIRTUAL->value) {
         $resourceData = array_merge($resourceData, self::virtualProductFields($product));
      }

      if ($product["type"] === ProductType::PHYSICAL->value) {
         $resourceData = array_merge($resourceData, self::physicalProductFields($product));
      }

      return $resourceData;
   }


   private static function virtualProductFields($product)
   {
      return  [
         "coupon_code" => $product["coupon_code"],
         "expires_at" => $product["expires_at"],
      ];
   }

   private static function physicalProductFields($product)
   {
      return  [
         "color" => $product["color"],
         "shipping_price" => $product["shipping_price"],

      ];
   }
}
