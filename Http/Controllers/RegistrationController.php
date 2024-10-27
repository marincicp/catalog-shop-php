<?php

namespace Http\Controllers;

use Models\UserModel;

require "./Models/UserModel.php";

class RegistrationController
{

   public static function store()
   {
      $res =  UserModel::register();
      return sendJsonRes($res, $res["code"]);
   }
}
