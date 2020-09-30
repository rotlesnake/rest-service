<?php
namespace AdminDev\Modules\Controllers;


class ModulesController extends \MapDapRest\Controller
{

    /**  **/
    public function indexAction($request, $response, $params) {

       $modules = $this->getModulesList();
       foreach ($modules as $k=>$v) {
          $modules[$k] = [
              "module"=>$v,
              "controllers"=>$this->getControllersList($v),
          ];
       }

       return ["modules"=>$modules];
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

    public function getControllersList($folder)
    {
      $php_parser = new \MapDapRest\PhpParser();
      $rez=[];
      $files = glob(ROOT_APP_PATH.$folder."/Controllers/*.php");
      foreach ($files as $file) {
         $fn = str_replace(ROOT_APP_PATH.$folder."/Controllers/", "", $file);
         $name = lcfirst(str_replace("Controller.php", "", $fn));
         $path = lcfirst($folder)."/".$name."/";

         $methods = [];
         $classes = $php_parser->extractPhpClasses($file);
         $class = $classes[0];
         $class_methods = get_class_methods($class);
         foreach ($class_methods as $methodName) {
             if (substr($methodName,-6) != "Action") continue;
             $method_name = substr($methodName,0, -6);
             $methods[] = ["name"=>$method_name, "comment"=>$php_parser->getComments($class, $methodName) ];
         }
         array_push($rez, [ "path"=>$path, "controller"=>$name, "methods"=>$methods ] );
      }
      return $rez;
    }




    /** @POST(name) **/
    public function createModuleAction($request, $response, $params) {
       $name = ucfirst($request->getParam("name"));
       $dir = ROOT_APP_PATH."/".$name;

       mkdir($dir, 0777);
       mkdir($dir."/Controllers/", 0777);
       mkdir($dir."/Models/", 0777);

       return [];
    }


    /** @POST(module:'название модуля', name:'название контроллера') **/
    public function createControllerAction($request, $response, $params) {
       $module = ucfirst($request->getParam("module"));
       $name = ucfirst($request->getParam("name"));

       $txt = file_get_contents(APP_PATH."Modules/stub/controller.stub");
       $txt = str_replace("<MODULE>",  $module, $txt);
       $txt = str_replace("<NAME>",  $name, $txt);
       file_put_contents(ROOT_APP_PATH.$module."/Controllers/".$name."Controller.php", $txt);

       return [];
    }


}//class