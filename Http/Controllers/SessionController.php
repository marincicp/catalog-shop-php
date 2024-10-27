<?php

namespace Http\Controllers;

use Models\UserModel;

require_once "./Models/UserModel.php";

class SessionController
{
   public static function store()
   {

      $res = UserModel::store();
      return sendJsonRes($res, $res["code"]);
   }


   public static function destroy()
   {
      $res = UserModel::logout();
      return sendJsonRes($res);
   }


   public static function getSession()
   {

      $res = UserModel::getCurrentUser();
      return  sendJsonRes($res);
   }
}
