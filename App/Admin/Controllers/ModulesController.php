<?php
namespace App\Admin\Controllers;


class ModulesController extends \MapDapRest\Controller
{

    /**  **/
    public function indexAction($request, $response, $params) {
       $user = $this->APP->auth->getFields();
       $modules = $this->getModulesList();
       foreach ($modules as $k=>$v) {
           $modules[$k] = [
              "module"=>$v,
              "controllers"=>$this->getControllersList($v),
              "desc"=>$this->getModuleInfo($v),
           ];
       }
       return ["modules"=>$modules];
    }

    /** any other action **/
    public function anyAction($request, $response, $controller, $action, $params) {
       return [];
    }

    private function getModulesList() {
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

    private function getModuleInfo($folder)
    {
      $rez="";
      $file = ROOT_PATH."App/".$folder."/Settings.php";
      if (file_exists($file)) {
          $class = "\\App\\$folder\\Settings";
          $rez = $class::$description;
      }
      return $rez;
    }

    private function getControllersList($folder)
    {
      $php_parser = new \MapDapRest\PhpParser();
      $rez=[];
      $files = glob(ROOT_PATH."App/".$folder."/Controllers/*.php");
      foreach ($files as $file) {
         $fn = str_replace(ROOT_PATH."App/".$folder."/Controllers/", "", $file);
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

}//class