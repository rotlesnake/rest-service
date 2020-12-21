<?php
namespace App\Journals\Controllers;

class GetController extends \MapDapRest\Controller
{

    /**  **/
    public function indexAction($request, $response, $params) {
       $user = $this->APP->auth->getFields();

       return [];
    }


    /**  **/
    public function anyAction($request, $response, $controller, $action, $params) {

       return [];
    }


    /** чтение таблицы (простой вариант) <br>@POST(table_name) **/
    public function sdfdsfdsAction($request, $response, $params) {
       $user = $this->APP->auth->getFields();

       return [];
    }



    /** чтение таблицы (простой вариант) <br>@POST(table_name) **/
    public function testAction($request, $response, $params) {
       $user = $this->APP->auth->getFields();

       return [];
    }


}//class