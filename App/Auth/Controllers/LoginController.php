<?php
namespace App\Auth\Controllers;


class LoginController extends \MapDapRest\Controller
{

    public $APP;
    public $requireAuth = false;


    public function __construct($app, $request, $response, $args)
    {
        $this->APP = $app;
        if ($request->hasParam("login") && $request->hasParam("password")) {
            $data = ["login"=>$request->params["login"], "password"=>$request->params["password"]];
            $this->APP->auth->login($data);
            return;
        }
        if ($request->hasParam("token")) {
            $data = ["token"=>$request->params["token"]];
            $this->APP->auth->login($data);
            return;
        }
    }


    /** Авторизация пользователя **/
    public function indexAction($request, $response, $params) {
       if ($this->APP->auth->isGuest()) {
           $response->setResponseCode(401);
           return ["error"=>1, "message"=>"Ошибка в логине или пароле"];
           die();
       }

       return ["user"=>$this->APP->auth->getFields()];
    }

}