<?php
namespace App\Auth\Controllers;


class LoginController extends \MapDapRest\Controller
{

    public $APP;


    public function __construct($app, $request, $response, $args)
    {
        $this->APP = $app;
        if ($request->hasParam("login")) {
           $data = ["login"=>$request->params["login"], "password"=>$request->params["password"]];
           $this->APP->auth->login($data);
        }
    }


    /** Авторизация пользователя <br>
       @login <br>
       @password <br>
       @return [user:{}]
    **/
    public function indexAction($request, $response, $params) {
       return ["user"=>$this->APP->auth->getFields()];
    }

}