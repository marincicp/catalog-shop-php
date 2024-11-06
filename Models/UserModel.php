<?php

namespace Models;

use Core\Jwt;
use Core\Session;
use Exception;
use UserValidator;
use Validator;

require_once "Model.php";
require_once "./Http/Validators/UserValidator.php";
require_once "./Core/functions.php";
require_once "./Core/Jwt.php";


class UserModel extends Model
{


   ////////////////////////////////////////////////////////////////////////
   // REGISTER logic

   /**
    *  Register a new user
    * @return array
    */
   public static function register(): array
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

   /**
    * Log in a user
    * @return array
    */
   public static function store(): array
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


   /**
    * This method handles the process of authenticating a user by verifying the provided credentials
    * @param string $email
    * @param string $password
    * @return array
    */
   public static function attemptToLogin(string $email, string  $password): array
   {
      $user = self::find($email);

      if ($user) {
         if (password_verify($password, $user["password"])) {
            Session::set("user", ["email" => $email, "id" => $user["id"]]);
            session_regenerate_id(true);

            $jwt = Jwt::generateJWT($user["id"]);

            return ["token" => $jwt, "user" => ["id" => $user["id"], "email" => $user["email"]], "code" => 200];
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


   /**
    * Find a specific user by email
    * @param string $email
    * @return mixed
    */
   public static function find(string $email): mixed
   {
      return  self::db()->query("SELECT * from users WHERE email =  :email", ["email" => $email])->find();
   }

   public static function getCurrentUser()
   {
      $res = Session::get("user");

      return ["user" => Session::get("user")];
   }


   /**
    * Check if the provided email already exists in the database.
    * Each user must have a unique email.
    * @param string $email
    * @return bool
    */
   public static function checkIfEmailExist(string $email): bool
   {
      $res =  self::db()->query("SELECT COUNT(*) FROM users WHERE email = :email", ["email" => $email])->count();

      return $res > 0;
   }
}
