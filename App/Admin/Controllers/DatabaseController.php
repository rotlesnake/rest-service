<?php
namespace App\Admin\Controllers;


class DatabaseController extends \MapDapRest\Controller
{

    /**  **/
    public function indexAction($request, $response, $params) {
       return [];
    }

    /** any other action **/
    public function anyAction($request, $response, $controller, $action, $params) {
       return [];
    }

    /** чтение таблицы (простой вариант) <br>@POST(table_name) **/
    public function tablesAction($request, $response, $params) {
       $columns = [];
       $rows = [];
       $modulesInfo = [];
       array_push($columns, ["text"=>"Модуль", "value"=>"module", "width"=>150]);
       array_push($columns, ["text"=>"Модель", "value"=>"model",  "width"=>200]);
       array_push($columns, ["text"=>"Таблица", "value"=>"table",  "width"=>200]);
       array_push($columns, ["text"=>"Наименование", "value"=>"name"]);

       $modules = $this->getModulesList();
       foreach ($modules as $k=>$v) {
          $models = $this->getModelsList($v);
          $modulesInfo[] = ["name"=>$v, "desc"=>$this->getModuleInfo($v) ];
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
       return ["columns"=>$columns, "rows"=>$rows, "modules"=>$modulesInfo];
    }


    public function getModulesList()
    {
      $rez=[];
    
        $extDir = ROOT_PATH."App";
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

    private function getModelsList($folder)
    {
      $php_parser = new \MapDapRest\PhpParser();
      $rez=[];
      $files = glob(ROOT_PATH."App/".$folder."/Models/*.php");
      foreach ($files as $file) {
         $name = ucfirst(basename($file,".php"));
         $class = "\\App\\$folder\\Models\\".$name;
         $info = $class::modelInfo();
         $rez[$name] = $info;
      }
      return $rez;
    }
    private function getModuleInfo($folder)
    {
      $rez="";
      $file = ROOT_PATH."App".$folder."/Settings.php";
      if (file_exists($file)) {
          $class = "\\App\\$folder\\Settings";
          $rez = $class::$description;
      }
      return $rez;
    }


//$response->setBody(["columns"=>$columns, "rows"=>$rows, "modules"=>$modulesInfo]);


    /** чтение таблицы (простой вариант) <br>@POST(table_name) **/
    public function modulesAction($request, $response, $params) {
       return ["rows"=>$rows];
    }


}//class