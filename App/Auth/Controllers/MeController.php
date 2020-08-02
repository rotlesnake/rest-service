<?php

namespace App\Auth\Controllers;


class MeController extends \MapDapRest\Controller
{

    public $APP;


    public function __construct($app, $request, $response, $args)
    {
        $this->APP = $app;
    }



    public function indexAction($request, $response, $params) {
 
       return $this->APP->auth->getFields([],["token","refresh_token","refresh_token_expire"]);
    }

}