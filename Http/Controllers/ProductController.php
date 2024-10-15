<?php

namespace Http\Controllers;

use Models\ProductModel;

require "./Models/ProductModel.php";

class ProductController
{

   public static function index()
   {
      $products = ProductModel::get();
      return sendJsonRes($products, $products["code"]);
   }



   public static function show($sku)
   {
      $product = ProductModel::find($sku);
      return sendJsonRes($product, $product["code"]);
   }


   public static function destroy($sku)
   {
      $response = ProductModel::delete($sku);
      return sendJsonRes($response, $response["code"]);
   }




   public static function update($sku)
   {
      $response = ProductModel::update($sku);
      return sendJsonRes($response, $response["code"]);
   }
}
