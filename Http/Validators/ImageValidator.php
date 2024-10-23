<?php


class ImageValidator
{


   protected const ALLOWED_EXT = ["jpg", "jpeg", "png", "webp"];
   protected const MAX_SIZE = 20000000; // 20 mb




   public static function validate($image)
   {

      $imgExt = pathinfo($image["name"], PATHINFO_EXTENSION);

      return  in_array($imgExt, static::ALLOWED_EXT) && $image["size"] <= self::MAX_SIZE;
   }
}