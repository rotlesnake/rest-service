<?php
require(__DIR__."/../../init.php");
$APP->auth->login(["login"=>"admin", "password"=>"admin"]);
define("ROOT_APP_PATH", str_replace("/", DIRECTORY_SEPARATOR, realpath(__DIR__."/../../")."/App/") );

$request = new \MapDapRest\Request();
$response = new \MapDapRest\Response();

    function getModulesList()
    {
      $rez=[];
    
        $extDir = ROOT_APP_PATH."/";
        if ($dh = opendir($extDir)) {
            while (($file = readdir($dh)) !== false) {
               if ($file != "." && $file != ".." && is_dir($extDir."/".$file)) {
                  array_push($rez, $file);
               }
            }
        closedir($dh);
        }

      return $rez;
    }

    function getModelsList($folder)
    {
      $php_parser = new \MapDapRest\PhpParser();
      $rez=[];
      $files = glob(ROOT_APP_PATH.$folder."/Models/*.php");
      foreach ($files as $file) {
         $name = ucfirst(basename($file,".php"));
         $class = "\\App\\$folder\\Models\\".$name;
         $info = $class::modelInfo();
         array_push($rez, $info );
      }
      return $rez;
    }



       $columns = [];
       $rows = [];
       array_push($columns, ["text"=>"Модуль", "value"=>"module", "width"=>150]);
       array_push($columns, ["text"=>"Модель", "value"=>"model",  "width"=>200]);
       array_push($columns, ["text"=>"Наименование", "value"=>"name"]);

       $modules = getModulesList();
       foreach ($modules as $k=>$v) {
          $models = getModelsList($v);
          if (count($models)==0) $rows[] = ["module"=>$v,"model"=>"","name"=>""];

          foreach ($models as $kk=>$vv) {
             $rows[] = [
                "module"=>$v,
                "model"=>$vv['table'],
                "name"=>$vv['name'],
             ];
          }
       }

$response->setBody(["columns"=>$columns, "rows"=>$rows]);
$response->send();

