<?php
namespace AdminDev\Database\Controllers;


class ModelController extends \MapDapRest\Controller
{

    /** @POST(module, model) **/
    public function getAction($request, $response, $params) {
       $module = ucfirst($request->getParam("module"));
       $table = \MapDapRest\Utils::getSlug($request->getParam("model"));
       $model = ucfirst($table);
       $file = ROOT_APP_PATH.$module."/Models/".$model.".php";
       $class = "App\\".$module."\\Models\\".$model;

       return ["model" => $class::modelInfo() , 
               "roles" => \MapDapRest\Utils::getAllRoles(false),
               "column_types" => \MapDapRest\Utils::getAllColumnTypes(),
              ];
    }




}//class