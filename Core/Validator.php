<?php


class  Validator
{

   public static function string($value, $min = 1, $max = 500)
   {
      $value = trim($value);
      $strLength  = strlen($value);

      if ($strLength >= $min && $strLength <= $max) {
         return true;
      }
      return false;
   }

   public static function number($value)
   {
      return is_numeric($value);
   }
}
