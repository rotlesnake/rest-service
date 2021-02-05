<?php
require(__DIR__."/../../init.php");
$APP->auth->login(["login"=>"admin", "password"=>"admin"]);
define("ROOT_APP_PATH", str_replace("/", DIRECTORY_SEPARATOR, realpath(__DIR__."/../../")."/App/") );

$request = new \MapDapRest\Request();
$response = new \MapDapRest\Response();

       if (strlen($request->getParam("module"))==0) die();

       $module = \MapDapRest\Utils::convUrlToModel( $request->getParam("module") );
       $controller = \MapDapRest\Utils::convUrlToModel( $request->getParam("name") );

       $txt = file_get_contents(__DIR__."/stub/controller.stub");
       $txt = str_replace("<MODULE>",  $module, $txt);
       $txt = str_replace("<NAME>",  $controller, $txt);
       file_put_contents(ROOT_APP_PATH.$module."/Controllers/".$controller."Controller.php", $txt);


$response->setBody([]);
$response->send();

