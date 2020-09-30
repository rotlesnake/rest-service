<?php
namespace AdminDev\Migrate\Controllers;


class MigrateController extends \MapDapRest\Controller
{


    public function indexAction($request, $response, $params) {

       $result = \MapDapRest\Migrate::migrate();

       $response->setError(0, "OK");
       return ["result"=>$result];
    }

}