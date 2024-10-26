<?php

namespace Models;

use Core\Session;
use Exception;
use Random\Engine\Secure;
use UserValidator;

require_once "Model.php";
require_once "./Http/Validators/UserValidator.php";
require_once "./Core/functions.php";


class UserModel extends Model
{





   public static function register()
   {

      $data  = decodeJson();
      $email = $data["email"];
      $password = $data["password"];

      $validator = new UserValidator();

      if (!$validator->validate($email, $password)) {
         $errors = $validator->error();
         return ["errors" => $errors, "code" => 400];
      }

      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
      try {

         self::db()->query("INSERT INTO users (email, password) VALUES (:email, :password)", ["email" => $email, "password" => $hashedPassword]);

         $userId = self::db()->conn()->lastInsertId();
         Session::set("user", ["email" => $email, "id" => $userId]);

         return ["code" => 200, "message" => "User has been successfully created", "user" => Session::get("user")];
      } catch (Exception $err) {

         return ["code" => $err->getCode(), "error" => $err->getMessage()];
      }
   }


   public static function login() {}
   public static function logout() {}



   public static function checkIfEmailExist($email)
   {
      return self::db()->query("SELECT COUNT(*) FROM users WHERE email = :email", ["email" => $email])->count();
   }
}
