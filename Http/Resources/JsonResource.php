<?php

namespace Http\Resources;

use Core\Response;

abstract class JsonResource
{

   abstract protected static  function formatResource($product);

   public static function collection($resources): array
   {

      $data = array_map(static::class . "::formatResource", $resources);

      return self::formatRes($data);
   }


   public static function single($resource, $msg = ""): array
   {
      $resourceItem = static::formatResource($resource);
      return self::formatRes($resourceItem, $msg);
   }


   private static function formatRes($data, $successMsg = ""): array
   {

      if (empty($data)) {
         return ["error" => "No data found", "code" => Response::NOT_FOUND, "data" => []];
      }

      $data =  ["data" => $data, "code" => Response::SUCCESS];

      if ($successMsg) {
         $data[] = ["message" => $successMsg];
      }

      return $data;
   }
}
