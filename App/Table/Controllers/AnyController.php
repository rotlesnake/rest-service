<?php

namespace App\Table\Controllers;


class AnyController  extends \MapDapRest\Controller
{

    public $APP;


    public function __construct($app, $request, $response, $args)
    {
        $this->APP = $app;
        //$this->APP->auth->setUser(1);
    }


    public function anyAction($request, $response, $tablename, $id, $args)
    {
      $id = (int)$id;

      if ($request->method=="GET") {
         $tableHandler = new TableHandler();
         $reqFields = [];
         if (isset($request->params["table_fields"])) $reqFields = $request->params["table_fields"];

         $rows = $tableHandler->get($this->APP, $tablename, $id, $reqFields, $request->params);
         if (count($args)>0 && $args[0]=="raw") {
            return $rows["rows"];
         }
         return $rows;
      }//---GET---


      if ($request->method=="POST") {
      }//---POST---

      if ($request->method=="PUT") {
      }//---PUT---

      if ($request->method=="DELETE") {
      }//---DELETE---

    }//Action

}
