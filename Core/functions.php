<?php

use Core\Response;



function dd($value)
{
   echo "<pre>";
   var_dump($value);
   echo "</pre>";
   exit();
}


function abort($code = Response::NOT_FOUND, $message = "The requested route could not be found")
{
   http_response_code($code);
   header("Content-Type: application/json");
   echo json_encode(["error" => $message]);
   exit();
}


function authorize($condition)
{
   if (!$condition) {
      abort(Response::FORBIDDEN, "Access denied: You do not have permission to perform this action");
   }
}


function sendJsonRes($data, $code = Response::SUCCESS)
{
   http_response_code($code);
   header('Content-Type: application/json');
   echo json_encode($data);
   exit();
}

function formatRes($data)
{
   if (empty($data)) {
      return ["error" => "No data found", "code" => Response::NOT_FOUND, "data" => []];
   }
   return ["data" => $data, "code" => Response::SUCCESS];
}






function decodeJson()
{
   $data = json_decode(file_get_contents("php://input"), true);

   if (!$data) {
      throw new Exception("Invalid JSON data", Response::BAD_REQUEST);
   }
   return $data;
}


function getJwtToken()
{
   $headers = getallheaders();
   $authorizationHeader = $headers["Authorization"] ?? null;

   if ($authorizationHeader) {
      $token = explode(" ", $authorizationHeader)[1];
      return $token;
   }
}
