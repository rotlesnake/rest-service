<?php

namespace App\Table\Controllers;


class TreeController extends \MapDapRest\Controller
{


    public function anyAction($request, $response, $action, $args)
    {
       $json_response = $this->add_error(0);
       $req_params = $request->getParams();
       $user = $this->auth->getUserFields();
       $json_response["items"] = [];

       $model = Core::tablenameToModel($args[0]);
       $tableInfo = $model::modelInfo();

       if ($action=="get") { 
          $json_response["items"] = $this->getTreeTable($model, 0);

          return json_encode($json_response);
       }
       if ($action=="set") { 
        $this->setTreeTable($model, $req_params);
        $json_response["items"] = $this->getTreeTable($model, 0);

          return json_encode($json_response);
       }
    }


    public function getTreeTable($model, $parent_id=0) {
        $json_response = [];
        $items = $model::where("parent_id", $parent_id)->orderBy("sort")->get();
        foreach ($items as $item) {
             $item_tree = [];
             $item_tree["server_id"] = $item->id;
             $item_tree["id"] = $item->id;
             $item_tree["pid"] = (int)$item->parent_id;
             $item_tree["addTreeNodeDisabled"] = true;
             $item_tree["open"] = true;
             $item_tree["name"] = $item->name;
             $item_tree["children"] = $this->getTreeTable($model, $item->id);

             array_push($json_response, $item_tree);
        }
        return $json_response;
    }


    public function setTreeTable($model, $items, $parent_id=0, $sort=0) {
        
        foreach ($items as $item) {
             $sort++;
             $id = 0;
             if (isset($item["server_id"])) $id = (int)$item["server_id"];

             $row = $model::findOrNew($id);
             $row->parent_id = $parent_id;
             $row->name = $item["name"];
             if ($row->id == 0) {
               //default values
             }
             $row->sort = $sort;
             $row->save();

             if (isset($item["children"]) && count($item["children"])>0 ) {
                 $sort = $this->setTreeTable($model, $item["children"], $row->id, $sort);
             }
        }
        return $sort;
    }

}
