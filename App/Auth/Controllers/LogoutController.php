<?php
namespace App\Auth\Controllers;


class LogoutController extends \MapDapRest\Controller
{

    public $APP;


    public function __construct($app, $request, $response, $args)
    {
        $this->APP = $app;
    }


    /** Выход из системы **/
    public function indexAction($request, $response, $params) {
       return $this->APP->auth->logout();
    }

}