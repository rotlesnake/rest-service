<?php
namespace AdminDev\Modules\Controllers;


class ListController extends \MapDapRest\Controller
{


    /** Выдает список шаблонных методов **/
    public function getMethodsAction($request, $response, $params) {

      $methods = [];

      $files = glob(APP_PATH."/Modules/stub/*.method");
      foreach ($files as $file) {
         $name = basename($file, ".method");
         $text = file_get_contents($file);
         preg_match('|\/\*\*(.*)\*\*\/|isU', $text, $rez);
         $text = $rez[1];
         $text = substr($text, 0, strpos($text, "<br>"));

         array_push($methods, [ "value"=>$name, "text"=>$text ] );
      }

       return ["methods"=>$methods];
    }




}//class