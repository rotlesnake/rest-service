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
       $name = \MapDapRest\Utils::getSlug( $request->getParam("name"), false, true );
       $name = ucfirst($name);
       $dir = ROOT_APP_PATH."/".$name;

       mkdir($dir, 0777);
       mkdir($dir."/Controllers/", 0777);
       mkdir($dir."/Models/", 0777);

       return [];
    }


    /** @POST(module:'название модуля', name:'название контроллера') **/
    public function createControllerAction($request, $response, $params) {
       $module = ucfirst($request->getParam("module"));
       $name = \MapDapRest\Utils::getSlug( $request->getParam("name"), false, true );
       $name = ucfirst($name);

       $txt = file_get_contents(APP_PATH."Modules/stub/controller.stub");
       $txt = str_replace("<MODULE>",  $module, $txt);
       $txt = str_replace("<NAME>",  $name, $txt);
       file_put_contents(ROOT_APP_PATH.$module."/Controllers/".$name."Controller.php", $txt);

       return [];
    }



    /** Добавление метода в контроллер <br>@POST(module:'название модуля', controller:'название контроллера', name:'название метода', type:'тип метода') **/
    public function createMethodAction($request, $response, $params) {
       $module = ucfirst($request->getParam("module"));
       $controller = ucfirst($request->getParam("controller"));
       $method = lcfirst($request->getParam("name"));
       $method = \MapDapRest\Utils::getSlug( $method, false, true );
       $type = $request->getParam("type");

       $method_text = file_get_contents(APP_PATH."Modules/stub/".$type.".method");
       $method_text = str_replace("<METHOD_NAME>",  $method, $method_text);

       $file_controller = ROOT_APP_PATH.$module."/Controllers/".$controller."Controller.php";

       $file = file($file_controller);
       $file_len = count($file);
       while (true) {
          $file_len--;
          if ( strpos( $file[$file_len], "}") !== false) { break; }
       }
       $file[$file_len - 1] .= "\r\n".$method_text."\r\n\r\n";
       file_put_contents($file_controller, $file);

       return ["error"=>0];
    }


}//class