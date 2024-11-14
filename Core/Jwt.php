<?php

namespace Core;

use Exception;
use \Firebase\JWT\JWT as JWTFirebase;
use \Firebase\JWT\Key;



class Jwt
{

   private const SECRET_KEY = "user-secret-key";

   public static function generateJWT($userId)
   {
      $issuedAt = time();
      $expirationTime = $issuedAt + 3600; //1h

      $payload = [
         "user_id" => $userId,
         "iat" => $issuedAt,
         "expiresAt" => $expirationTime
      ];

      return JWTFirebase::encode($payload, self::SECRET_KEY, "HS256");
   }

   public static function verifyJWT($jwt)
   {
      try {
         $decoded = JWTFirebase::decode($jwt, new Key(self::SECRET_KEY, "HS256"));
         return $decoded;
      } catch (Exception $err) {
         return null;
      }
   }



   public static function authorizeUser($productUserId)
   {
      $headers = getallheaders();
      $auth = $headers["Authorization"];
      $token = explode(" ", $auth)[1];

      $user = self::verifyJWT($token);

      authorize($user->user_id === $productUserId);
   }
}
