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

    function getControllersList($folder)
    {
      $php_parser = new \MapDapRest\PhpParser();
      $rez=[];
      $files = glob(ROOT_APP_PATH.$folder."/Controllers/*.php");
      foreach ($files as $file) {
         $fn = str_replace(ROOT_APP_PATH.$folder."/Controllers/", "", $file);
         $name = str_replace("Controller.php", "", $fn);
         $path = lcfirst($folder)."/".lcfirst($name)."/";

         $methods = [];
         $classes = $php_parser->extractPhpClasses($file);
         $class = $classes[0];
         $class_methods = get_class_methods($class);
         foreach ($class_methods as $methodName) {
             if (substr($methodName,-6) != "Action") continue;
             $method_name = substr($methodName,0, -6);
             $method_name = \MapDapRest\Utils::convNameToUrl($method_name);
             $methods[] = ["name"=>$method_name, "comment"=>$php_parser->getComments($class, $methodName) ];
         }
         $path = \MapDapRest\Utils::convNameToUrl($path);
         array_push($rez, [ "path"=>$path, "controller"=>$name, "methods"=>$methods ] );
      }
      return $rez;
    }


       $modules = getModulesList();
       foreach ($modules as $k=>$v) {
          $modules[$k] = [
              "module"=>$v,
              "controllers"=>getControllersList($v),
          ];
       }


$response->setBody(["modules"=>$modules]);
$response->send();

