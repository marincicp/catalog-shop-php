<?php



function dd($value)
{
   echo "<pre>";
   var_dump($value);
   echo "</pre>";
   exit();
}


function abort($code = 404, $message = "The requested route could not be found")
{
   http_response_code($code);
   header("Content-Type: application/json");
   echo json_encode(["error" => $message]);
   exit();
}


function sendJsonRes($data, $code = 200)
{
   http_response_code($code);
   header('Content-Type: application/json');
   echo json_encode($data);
   exit();
}

function formatRes($data)
{
   if ($data) {
      return ["data" => $data, "code" => 200];
   }
   return ["error" => "No data found", "code" => 404];
}
