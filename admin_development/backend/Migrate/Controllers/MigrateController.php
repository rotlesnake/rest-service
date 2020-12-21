<?php
namespace AdminDev\Migrate\Controllers;


class MigrateController extends \MapDapRest\Controller
{


    public function indexAction($request, $response, $params) {

       $APP = \MapDapRest\App::getInstance();
       $APP->APP_PATH = ROOT_APP_PATH;

       $result = \MapDapRest\Migrate::migrate();

       $response->setError(0, "OK");
       return ["result"=>$result];
    }

}