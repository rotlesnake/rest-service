<?php
namespace App\Auth\Controllers;



class RegisterController extends \MapDapRest\Controller
{

    public $requireAuth = false;


    /** POST(account) @return account.json  **/
    public function indexAction($request, $response, $params) {
       die();
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

       $this->APP->auth->login($request->params);
       \App\Auth\Events\Emits::userRegistered($this->APP->auth);

       return ["user"=>$this->APP->auth->getFields()];
    }



}//class