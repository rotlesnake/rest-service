<?php
require(__DIR__."/../../init.php");
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
    function convertRowData($str) {
        $str = str_replace("\r\n","\\n",$str);
        $str = str_replace("\n","\\n",$str);
        $str = str_replace("'","\\'",$str);
        return $str;
    }


       $rows = [];
       $modulesInfo = [];

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
                "info"=>$vv,
             ];
          }
       }

       $export_folder = ROOT_PATH."_export_database/";
       if (!file_exists($export_folder)) mkdir($export_folder, 0777);

       foreach ($rows as $info) {
           if (!$APP->DB->schema()->hasTable($info["table"])) continue;
           $sql_script = "";
           //$sql_script_pre = "INSERT INTO ".$info["table"]." () VALUES();";
           $table_data = $APP->DB::table($info["table"])->get();
           echo $info["table"]."<br>";
           foreach ($table_data as $tr) {
               $sql_script .= "INSERT INTO ".$info["table"]." (";
               $fields_names = "";
               $fields_values = "";
               foreach ($info["info"]["columns"] as $cn=>$col) {
                   if ($col["type"]=="integer" || $col["type"]=="bigInteger") $tr->{$cn} = (int)$tr->{$cn};
                   if ($col["type"]=="float" || $col["type"]=="double") $tr->{$cn} = (float)$tr->{$cn};
                   $fields_names .= ",`".$cn."`";
                   $fields_values .= ",'".convertRowData($tr->{$cn})."' ";
               }
               $sql_script .= substr($fields_names,1).") ";
               $sql_script .= "VALUES(".substr($fields_values,1).");\r\n";
           }
           file_put_contents($export_folder.$info["table"].".sql", $sql_script);
           echo "export ".count($table_data)." rows<hr>";
       }
die();
$response->setBody($rows);
$response->send();

