<?php

namespace Models;

use Core\Session;
use Exception;
use UserValidator;
use Validator;

require_once "Model.php";
require_once "./Http/Validators/UserValidator.php";
require_once "./Core/functions.php";



class UserModel extends Model
{


   ////////////////////////////////////////////////////////////////////////
   // REGISTER logic


   public static function register()
   {

      $data  = decodeJson();
      $email = $data["email"];
      $password = $data["password"];

      $validator = new UserValidator();

      if (!$validator->validateRegister($email, $password)) {
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


   ////////////////////////////////////////////////////////////////////////
   // LOGIN logic


   public static function store()
   {

      $data =  decodeJson();
      $password = $data["password"];
      $email =  $data["email"];


      $validator = new UserValidator();

      if (! $validator->validateLogin($email, $password)) {
         $errors = $validator->error();
         return ["errors" => $errors, "code" => 400];
      }

      return    self::attemptToLogin($email, $password);
   }



   public static function attemptToLogin($email, $password)
   {
      $user = self::find($email);

      if ($user) {
         if (password_verify($password, $user["password"])) {
            Session::set("user", ["email" => $email, "id" => $user["id"]]);
            session_regenerate_id(true);

            return ["user" => ["id" => $user["id"], "email" => $user["email"]], "code" => 200];
         }
      }

      return ["errors" => "Invalid email or password", "code" => 400];
   }

   public static function logout()
   {
      Session::clear();
      session_destroy();

      $params = session_get_cookie_params();
      return setcookie("PHPSESSID", "", time() - 3600, $params["path"], $params["domain"]);
   }



   ////////////////////////////////////////////////////////////////////////
   // HELPERS

   public static function find($email)
   {

      return self::db()->query("SELECT * from users WHERE email =  :email", ["email" => $email])->find();
   }

   public static function getCurrentUser()
   {
      $res = Session::get("user");

      return ["user" => Session::get("user")];
   }


   public static function checkIfEmailExist($email)
   {
      return self::db()->query("SELECT COUNT(*) FROM users WHERE email = :email", ["email" => $email])->count();
   }
}
