<?php
require(__DIR__."/../../init.php");
$APP->auth->login(["login"=>"admin", "password"=>"admin"]);
define("ROOT_APP_PATH", str_replace("/", DIRECTORY_SEPARATOR, realpath(__DIR__."/../../")."/App/") );

$request = new \MapDapRest\Request();
$response = new \MapDapRest\Response();

       if (strlen($request->getParam("name"))==0) die();

       $module = \MapDapRest\Utils::convUrlToModel( $request->getParam("module") );
       $controller = \MapDapRest\Utils::convUrlToModel( $request->getParam("controller") );
       $method = \MapDapRest\Utils::convUrlToMethod( $request->getParam("name") );
       $type = $request->getParam("type");

       $method_text = file_get_contents(__DIR__."/stub/".$type.".method");
       $method_text = str_replace("<METHOD_NAME>",  $method, $method_text);

       $file_controller = ROOT_APP_PATH.$module."/Controllers/".$controller."Controller.php";

       $file = file($file_controller);
       $file_len = count($file);
       while (true) {
          $file_len--;
          if ( strpos( $file[$file_len], "}") !== false) { break; }
       }
       $file[$file_len - 1] .= "\r\n".$method_text."\r\n\r\n";
       file_put_contents($file_controller, $file);


$response->setBody(["error"=>0]);
$response->send();

