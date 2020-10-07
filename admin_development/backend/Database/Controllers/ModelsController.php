<?php
namespace AdminDev\Database\Controllers;


class ModelsController extends \MapDapRest\Controller
{

    /**  **/
    public function indexAction($request, $response, $params) {

       $columns = [];
       $rows = [];
       array_push($columns, ["text"=>"Модуль", "value"=>"module", "width"=>150]);
       array_push($columns, ["text"=>"Модель", "value"=>"model",  "width"=>180]);
       array_push($columns, ["text"=>"Наименование", "value"=>"name"]);

       $modules = $this->getModulesList();
       foreach ($modules as $k=>$v) {
          $models = $this->getModelsList($v);
          foreach ($models as $kk=>$vv) {
             $rows[] = [
                "module"=>$v,
                "model"=>$vv['table'],
                "name"=>$vv['name'],
             ];
          }
       }

       return ["columns"=>$columns, "rows"=>$rows];
    }


    public function getModulesList()
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

    public function getModelsList($folder)
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




    /** @POST(module:'название модуля', model:'название модели') **/
    public function addModelAction($request, $response, $params) {
       $module = ucfirst($request->getParam("module"));
       $table = \MapDapRest\Utils::getSlug($request->getParam("model"));
       $model = ucfirst($table);
       $label = $request->getParam("label");

       if ($this->APP->hasModel($table)) return ["error"=>1, "message"=>"exists"];

       $txt = file_get_contents(APP_PATH."Database/stub/model.stub");
       $txt = str_replace("<MODULE>",  $module, $txt);
       $txt = str_replace("<MODEL_NAME>",  $model, $txt);
       $txt = str_replace("<TABLE_NAME>",  $table, $txt);
       $txt = str_replace("<TABLE_CATEGORY>",  $label, $txt);
       $txt = str_replace("<TABLE_LABEL>",  $label, $txt);
       file_put_contents(ROOT_APP_PATH.$module."/Models/".$model.".php", $txt);

       return ["error"=>0, "message"=>""];
    }


}//class