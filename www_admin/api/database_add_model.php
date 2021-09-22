<?php
require(__DIR__."/../../init.php");
$APP->auth->login(["login"=>"admin", "password"=>"admin"]);
define("ROOT_APP_PATH", str_replace("/", DIRECTORY_SEPARATOR, realpath(__DIR__."/../../")."/App/") );

$request = new \MapDapRest\Request();
$response = new \MapDapRest\Response();


       $module = \MapDapRest\Utils::convUrlToModel( $request->getParam("module") );
       $model = \MapDapRest\Utils::convUrlToModel( $request->getParam("model") );
       $table = \MapDapRest\Utils::convUrlToTable( $module.$model );
       $label = $request->getParam("label");
       $tree = $request->getParam("tree");

       if ($APP->hasModel($table)) return ["error"=>1, "message"=>"exists"];
       $txt = file_get_contents(__DIR__.($tree ? "/stub/model_tree.stub" : "/stub/model.stub"));
       $txt = str_replace("<MODULE>",  $module, $txt);
       $txt = str_replace("<MODEL>",  $model, $txt);
       $txt = str_replace("<TABLE>",  $table, $txt);
       $txt = str_replace("<LABEL>",  $label, $txt);
       file_put_contents(ROOT_APP_PATH.$module."/Models/".$model.".php", $txt);

       if (file_exists(ROOT_APP_PATH.$module."/Facades")) {
           $txt = file_get_contents(__DIR__."/stub/facade.stub");
           $txt = str_replace("<MODULE>",  $module, $txt);
           $txt = str_replace("<MODEL>",  $model, $txt);
           file_put_contents(ROOT_APP_PATH.$module."/Facades/".$model."Facade.php", $txt);
       }


$response->setBody(["error"=>0, "message"=>""]);
$response->send();

