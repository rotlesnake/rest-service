<?php
namespace App\Test\Controllers;


class ReadController extends \MapDapRest\Controller
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
    public function allAction($request, $response, $params) {
       $user = $this->APP->auth->getFields();
       $table_name = $request->getParam("table_name");

       return [];
    }




    /** чтение таблицы (простой вариант) <br>@POST(table_name) **/
    public function onerecordAction($request, $response, $params) {
       $user = $this->APP->auth->getFields();
       $table_name = $request->getParam("table_name");

       return [];
    }




    /** Добавить запись <br>@POST(table_name, fields) **/
    public function addRecordAction($request, $response, $params) {
       $user = $this->APP->auth->getFields();
       $table_name = $request->getParam("table_name");

       $table_model = $this->APP->getModel($table_name);

       $row = new $table_model();
       $row->name = "test";
       $row->save();

       return ["error"=>0];
    }


}//class