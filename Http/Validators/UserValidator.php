<?php

use Models\UserModel;

class UserValidator
{

   private $errors = [];
   public function validateRegister($email, $password)
   {

      if (!isset($email) || !Validator::email($email)) {
         $this->errors["email"] = "Invalid email";
      }

      if (!isset($email) || UserModel::checkIfEmailExist($email)) {
         $this->errors["email"] = "Email alredy taken";
      }
      if (!isset($password) || !Validator::string($password))
         $this->errors["password"] = "Invalid password";


      if (!empty($this->errors)) {

         return false;
      }

      return true;
   }

   public function validateLogin($email, $password)
   {
      if (!isset($email) || !Validator::email($email)) {
         $this->errors["email"] = "Invalid email";
      }

      if (!isset($password) || !Validator::string($password))
         $this->errors["password"] = "Invalid password";

      if (!empty($this->errors)) {
         return false;
      }

      return true;
   }



   public function error()
   {
      return $this->errors;
   }
}
