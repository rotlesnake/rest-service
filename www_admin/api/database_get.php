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
      sort($rez, SORT_NATURAL | SORT_FLAG_CASE);
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
         $rez[$name] = $info;
      }
      return $rez;
    }
    function getModuleInfo($folder)
    {
      $rez="";
      $file = ROOT_APP_PATH.$folder."/Settings.php";
      if (file_exists($file)) {
          $class = "\\App\\$folder\\Settings";
          $rez = $class::$description;
      }
      return $rez;
    }



       $columns = [];
       $rows = [];
       $modulesInfo = [];
       array_push($columns, ["text"=>"Модуль", "value"=>"module", "width"=>150]);
       array_push($columns, ["text"=>"Модель", "value"=>"model",  "width"=>200]);
       array_push($columns, ["text"=>"Таблица", "value"=>"table",  "width"=>200]);
       array_push($columns, ["text"=>"Наименование", "value"=>"name"]);

       $modules = getModulesList();
       foreach ($modules as $k=>$v) {
          $models = getModelsList($v);
          $modulesInfo[] = ["name"=>$v, "desc"=>getModuleInfo($v) ];
          if (count($models)==0) $rows[] = ["module"=>$v,"model"=>"","table"=>"","name"=>""];

          foreach ($models as $kk=>$vv) {
             $rows[] = [
                "module"=>$v,
                "model"=>$kk,
                "table"=>$vv['table'],
                "name"=>$vv['name'],
             ];
          }
       }

$response->setBody(["columns"=>$columns, "rows"=>$rows, "modules"=>$modulesInfo]);
$response->send();

