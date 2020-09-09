<?php

namespace App\Table\Controllers;


class AnyController  extends \MapDapRest\Controller
{


    public function anyAction($request, $response, $tablename, $action_or_id, $args)
    {
 
      //
      if ($request->method=="GET") {
         $id = (int)$action_or_id;

         $tableHandler = new TableHandler($this->APP);
         $reqFields = [];
         if (isset($request->params["fields"])) $reqFields = $request->params["fields"];

         $rows = $tableHandler->get($tablename, $id, $reqFields, $request->params);
         if (count($args)>0) {
            return $rows;
         }

         return isset($rows["rows"]) ? $rows["rows"] : $rows;
      }//---GET-----------------------------------
 

      //
      if ($request->method=="POST") {
         $action = $action_or_id;
         $id = $args[0];
         $rows = [];

         $tableHandler = new TableHandler($this->APP);
         
         if ($action=="add")    $rows = $tableHandler->add($tablename, $request->params);
         if ($action=="edit")   $rows = $tableHandler->edit($tablename, $id, $request->params);
         if ($action=="delete") $rows = $tableHandler->delete($tablename, $id);
         
         return $rows;
      }//---POST-----------------------------------


      //
      if ($request->method=="PUT") {
         $id = $action_or_id;
         $rows = [];

         $tableHandler = new TableHandler($this->APP);
         
         if ($id==0) {
            $rows = $tableHandler->add($tablename, $request->params);
         } else {
            $rows = $tableHandler->edit($tablename, $id, $request->params);
         }

         return $rows;
      }//---PUT-----------------------------------


      //
      if ($request->method=="DELETE") {
         $id = $args[0];
         $rows = [];

         $tableHandler = new TableHandler($this->APP);
        
         $rows = $tableHandler->delete($tablename, $id);
         
         return $rows;
      }//---DELETE-----------------------------------

    }//Action

}
