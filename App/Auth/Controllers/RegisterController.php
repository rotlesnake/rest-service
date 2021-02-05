<?php
namespace App\Auth\Controllers;


class RegisterController extends \MapDapRest\Controller
{

    public $requireAuth = false;
    public $APP;


    public function __construct($app, $request, $response, $args)
    {
        $this->APP = $app;
    }



    /** POST(account) @return account.json  **/
    public function indexAction($request, $response, $params) {

       if (!$request->hasParam("login")) {
          return ["message"=>"Нет логина"];
       }
       if (!$request->hasParam("password")) {
          return ["message"=>"Нет пароля"];
       }

       $login = $request->getParam("login");
       $password = $request->getParam("password");
       $password2 = $request->getParam("password2");

       if ($password != $password2) {
          return ["message"=>"Пароли не совпадают"];
       }
       if (strlen($password)<8) {
          return ["message"=>"Пароль должен быть минимум 8 символов"];
       }


       $user = $this->APP->auth->register($request->getParam("login"), $request->getParam("password"), 1, 5);
       if (!$user) return [];

       return ["user"=>$user];
    }

}