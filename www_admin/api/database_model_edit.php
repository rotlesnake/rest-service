<?php
require(__DIR__."/../../init.php");
$APP->auth->login(["login"=>"admin", "password"=>"admin"]);
define("ROOT_APP_PATH", str_replace("/", DIRECTORY_SEPARATOR, realpath(__DIR__."/../../")."/App/") );

function objectToPHP($fields, $level=0, $prekey="") {
   $str="";

   foreach ($fields as $k=>$v) {
       if (gettype($v)!="NULL") {
          if (gettype($v)=="array") {
             if ($k=="read") $v = array_map('intval',$v);
             if ($k=="add") $v = array_map('intval',$v);
             if ($k=="edit") $v = array_map('intval',$v);
             if ($k=="delete") $v = array_map('intval',$v);

             $str .= "\"".$k."\"=>[";
             if ($k=="columns") { $str .= "\r\n                   "; }
             if ($k=="filter") { $str .= "\r\n                   "; }
             $str .= objectToPHP($v, $level+1, $k);
             $str .= "], ";
             if ($prekey=="columns") { $str .= "\r\n                   "; }
             if ($prekey=="filter") { $str .= "\r\n                   "; }
          }
          if (gettype($v)=="string") {
             if (gettype($k)=="integer" && $prekey!="items") {
               $str .= "\"".$v."\", ";
             } else {
               $str .= "\"".$k."\"=>\"".$v."\", ";
             }
          }
          if (gettype($v)=="integer") {
             if (gettype($k)=="integer") {
               $str .= "".$v.",";
             } else {
               $str .= "\"".$k."\"=>".$v.", ";
             }
          }
          if (gettype($v)=="double") {
             if (gettype($k)=="integer") {
               $str .= "".$v.",";
             } else {
               $str .= "\"".$k."\"=>".$v.", ";
             }
          }
          if (gettype($v)=="boolean") {
             if (gettype($k)=="integer") {
               $str .= "".($v?'true':'false').", ";
             } else {
               $str .= "\"".$k."\"=>".($v?'true':'false').", ";
             }
          }
       } else {
          $str .= "\"***".$k."\", ";
       }

       if ($level<1) $str .= "\r\n           ";
   }//fields

   return $str;
}

$request = new \MapDapRest\Request();
$response = new \MapDapRest\Response();

       $module = \MapDapRest\Utils::convUrlToModel( $request->getParam("module") );
       $model = \MapDapRest\Utils::convUrlToModel( $request->getParam("model") );
       $file = ROOT_APP_PATH.$module."/Models/".$model.".php";
       $class = "App\\".$module."\\Models\\".$model;
    
       $func = new ReflectionMethod($class, "modelInfo");
       $f = $func->getFileName();
       $start_line = $func->getStartLine() - 1;
       $end_line = $func->getEndLine();
       $length = $end_line - $start_line;
       $source = file_get_contents($f);
       $source = preg_split('/' . PHP_EOL . '/', $source);
       $full_length = count($source);

       //$func_code = implode(PHP_EOL, array_slice($source, $start_line, $length));

       $func_code_return = objectToPHP($request->getParam("info"));

       $body = "";
       $body .= implode(PHP_EOL, array_slice($source, 0, $start_line)) . PHP_EOL;

       $body .= "    public static function modelInfo() {\r\n      \$acc_admin = [1];\r\n      \$acc_all = \MapDapRest\Utils::getAllRoles();\r\n      \r\n      return [\r\n";
       $body .= "           ".$func_code_return."\r\n";
       $body .= "             ];\r\n";
       $body .= "    }";
       $body .= PHP_EOL;

       $body .= implode(PHP_EOL, array_slice($source, $start_line+$length, $full_length));

file_put_contents($f, $body . PHP_EOL);
$response->setBody(["error" => 0]);
$response->send();

