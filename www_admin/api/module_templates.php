<?php
require(__DIR__."/../../init.php");
$APP->auth->login(["login"=>"admin", "password"=>"admin"]);
define("ROOT_APP_PATH", str_replace("/", DIRECTORY_SEPARATOR, realpath(__DIR__."/../../")."/App/") );

$request = new \MapDapRest\Request();
$response = new \MapDapRest\Response();


      $methods = [];

      $files = glob(__DIR__."/stub/*.method");
      foreach ($files as $file) {
         $name = basename($file, ".method");
         $text = file_get_contents($file);
         preg_match('|\/\*\*(.*)\*\*\/|isU', $text, $rez);
         $text = $rez[1];
         $text = substr($text, 0, strpos($text, "<br>"));

         array_push($methods, [ "value"=>$name, "text"=>$text ] );
      }


$response->setBody(["methods"=>$methods]);
$response->send();

