<?php
require(__DIR__."/../../init.php");
$APP->auth->login(["login"=>"admin", "password"=>"admin"]);
define("ROOT_APP_PATH", str_replace("/", DIRECTORY_SEPARATOR, realpath(__DIR__."/../../")."/App/") );

$request = new \MapDapRest\Request();
$response = new \MapDapRest\Response();

       $module = \MapDapRest\Utils::convUrlToModel( $request->getParam("module") );
       $model = \MapDapRest\Utils::convUrlToModel( $request->getParam("model") );
       $file = ROOT_APP_PATH.$module."/Models/".$model.".php";
    
       unlink($file);

$response->setBody(["error" => 0]);
$response->send();

