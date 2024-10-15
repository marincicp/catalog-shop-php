<?php

namespace Http\Controllers;

use Models\CategoryModel;

require "./Models/CategoryModel.php";
class CategoryController
{
   public static function index()
   {
      $res = CategoryModel::get();
      return  sendJsonRes($res, $res["code"]);
   }
}
