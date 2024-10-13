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