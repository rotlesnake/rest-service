<?php
require(__DIR__."/../../init.php");
$APP->auth->login(["login"=>"admin", "password"=>"admin"]);
define("ROOT_APP_PATH", str_replace("/", DIRECTORY_SEPARATOR, realpath(__DIR__."/../../")."/App/") );

$request = new \MapDapRest\Request();
$response = new \MapDapRest\Response();

       if (strlen($request->getParam("name"))==0) die();

       $module = \MapDapRest\Utils::convUrlToModel( $request->getParam("name") );
       $dir = ROOT_APP_PATH."/".$module;

       mkdir($dir, 0777);
       mkdir($dir."/Controllers/", 0777);
       mkdir($dir."/Models/", 0777);
       mkdir($dir."/Events/", 0777);
       mkdir($dir."/Facades/", 0777);
       //mkdir($dir."/Services/", 0777);


       $txt = file_get_contents(__DIR__."/stub/event_emits.stub");
       $txt = str_replace("<MODULE>",  $module, $txt);
       file_put_contents(ROOT_APP_PATH.$module."/Events/Emits.php", $txt);

       $txt = file_get_contents(__DIR__."/stub/event_listening.stub");
       $txt = str_replace("<MODULE>",  $module, $txt);
       file_put_contents(ROOT_APP_PATH.$module."/Events/Listening.php", $txt);

       $txt = file_get_contents(__DIR__."/stub/module_settings.stub");
       $txt = str_replace("<MODULE>",  $module, $txt);
       file_put_contents(ROOT_APP_PATH.$module."/Settings.php", $txt);


$response->setBody([]);
$response->send();

