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
            $data = ["login"=>$request->params["login"], "password"=>$request->params["password"], "hours"=>3];
            return $this->APP->auth->login($data);
        }
        if ($request->hasParam("token")) {
            $data = ["token"=>$request->params["token"]];
            return $this->APP->auth->login($data);
        }        
    }


/**
* @OA\Post(
*     tags={"Auth"},
*     path="/auth/login",
*     summary="Авторизация в системе",
*     description="Вход в систему по логину и пароль, либо по токену",
*     @OA\Parameter(ref="#/components/parameters/login"),
*     @OA\Parameter(ref="#/components/parameters/password"),
*     @OA\Parameter(ref="#/components/parameters/token"),
*     @OA\Response(response="200", description="Успешная авторизация",
*         @OA\JsonContent(type="object", @OA\Property(property="user", type="object", ref="#/components/schemas/users") )
*     ),
*     @OA\Response(response="401", description="Ошибка авторизации",
*         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
*     )
* )
**/
    public function indexAction($request, $response, $params) {
       if ($this->APP->auth->isGuest()) {
           $response->setResponseCode(401);
           $message = "Ошибка в логине или пароле";
           if ($request->hasParam("token")) $message = "Ошибка в токене";
           if ($request->hasParam("password")) $message = "Ошибка в логине или пароле";
           return ["error"=>1, "message"=>$message];
           die();
       }

       $user = $this->APP->auth->getFields();
       \MapDapRest\App\Auth\Events\Emits::userLogin($this->APP->auth);
       return ["status"=>1, "token"=>$user["token"], "user"=>$user];
    }

}
