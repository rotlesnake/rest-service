<?php

namespace App\Table\Controllers;


class AnyController extends ApiBaseController
{

    public function indexAction($request, $response, $args)
    {
        return "ERR";
    }


    public function tablesAction($request, $response, $args)
    {
        $json_response = $this->add_error(0);
        $req_params = $request->getParams();
        $user = $this->auth->getUserFields();
        if ( !$this->auth->hasRoles([2]) ) { return json_encode($this->add_error(3)); }

        $json_response["columns"] = [];
        $json_response["rows"] = [];
        $json_response["pagination"] = [];


        $json_response['columns'] = [
                                   ["text"=>"Категория", "value"=>"category", "class"=>"table-headers", "width"=>200], 
                                   ["text"=>"Имя таблицы", "value"=>"name", "class"=>"table-headers", "width"=>200], 
                                   ["text"=>"Наименование", "value"=>"description", "class"=>"table-headers", "width"=>500], 
                                   ["text"=>"Модель", "value"=>"model", "class"=>"table-headers", "width"=>200], 
                                ];

        $json_response['pagination'] = [
                                "key"=> "name",
                                "page"=> 1,
                                "totalItems"=> -1,
                                "itemsPerPage"=> 1000,
                                "rowsPerPageItems"=> [10, 50, 100, 500, 1000]
                                ];

        $files = Models::orderBy('table')->get();
        foreach ($files as $row) {
          $class = $row["model"];
          $info = $class::modelInfo();

               $json_response['rows'][] = [
                                   "name" => $info["table"], 
                                   "category" => $info["category"], 
                                   "description" => $info["label"], 
                                   "model" => $class, 
                                 ];

        }


        return json_encode($json_response);
    }




    public function params_to_string($params) {
        $str = "";
        foreach ($params as $x=>$y) {
           if (in_array($x, ['type', 'label', 'roles_read', 'roles_add', 'roles_edit'])) continue;
           $str .= $x."=".print_r($y,1)."<br>\r\n";
        }
        return $str;
    }

    public function fieldsAction($request, $response, $args)
    {
        $json_response = $this->add_error(0);
        $req_params = $request->getParams();
        if ( !$this->auth->hasRoles([2]) ) { return json_encode($this->add_error(3)); }
        $user = $this->auth->getUserFields();
        if (!isset($req_params['model'])) return $json_response;

        $json_response["columns"] = [];
        $json_response["rows"] = [];
        $json_response["pagination"] = [];
        $json_response['columns'] = [
                                   ["text"=>"Имя поля",     "value"=>"name", "class"=>"table-headers"], 
                                   ["text"=>"Наименование", "value"=>"description", "class"=>"table-headers"], 
                                   ["text"=>"Тип поля",     "value"=>"type_name", "class"=>"table-headers"], 
//                                   ["text"=>"Параметры",     "value"=>"params", "class"=>"table-headers"], 
                                   ["text"=>"Роли Чтения",  "value"=>"roles_read", "class"=>"table-headers"], 
                                   ["text"=>"Роли Добавления", "value"=>"roles_add", "class"=>"table-headers"], 
                                   ["text"=>"Роли Редактирования", "value"=>"roles_edit", "class"=>"table-headers"], 
                                ];

        $class = $req_params['model'];
        $info = $class::modelInfo();
        foreach ($info["columns"] as $x=>$y) {

            $json_response['rows'][] = [
                               "name" => $x, 
                               "description" => $y["label"],
                               "type_name" => $y["type"],
                               "params" => $this->params_to_string($y), 
                               "roles_read" => Roles::rolesToString($y["roles_read"]), 
                               "roles_add" => Roles::rolesToString($y["roles_add"]), 
                               "roles_edit" => Roles::rolesToString($y["roles_edit"]),
                             ];
        }

        $json_response['pagination'] = [
                                "key"=> "name",
                                "page"=> 1,
                                "totalItems"=> -1,
                                "itemsPerPage"=> 10,
                                "rowsPerPageItems"=> [10, 50, 100, 500, 1000]
                                ];


        return json_encode($json_response);
    }





    public function getMenuTree($menu_name, $parent_id=0) {
        $json_response = [];
        $items = Menu::where("menu", $menu_name)->where("parent_id", $parent_id)->orderBy("sort")->get();
        foreach ($items as $item) {
             $item_tree = [];
             $item_tree["server_id"] = $item->id;
             $item_tree["id"] = $item->id;
             $item_tree["pid"] = $item->parent_id;
             $item_tree["addTreeNodeDisabled"] = true;
             $item_tree["open"] = true;
             $item_tree["name"] = $item->title;
             $item_tree["children"] = $this->getMenuTree($menu_name, $item->id);

             array_push($json_response, $item_tree);
        }
        return $json_response;
    }

    public function getmenuAction($request, $response, $args)
    {
        $json_response = $this->add_error(0);
        $user = $this->auth->getUserFields();
        if ( !$this->auth->hasRoles([2]) ) { return json_encode($this->add_error(3)); }

        $json_response["items"] = $this->getMenuTree($args[0], 0);

        return json_encode($json_response);
    }



    public function updateMenuItems($menu_name, $items, $parent_id=0, $sort=0) {
        
        foreach ($items as $item) {
             $sort++;
             $id = 0;
             if (isset($item["server_id"])) $id = (int)$item["server_id"];

             $row = Menu::findOrNew($id);
             $row->menu = $menu_name;
             $row->parent_id = $parent_id;
             $row->title = $item["name"];
             if ($row->id == 0) {
                 $row->icon = "home";
                 $row->roles_ids = "2";
                 $row->url = "/home";
             }
             $row->sort = $sort;
             $row->save();

             if (isset($item["children"]) && count($item["children"])>0 ) {
                 $sort = $this->updateMenuItems($menu_name, $item["children"], $row->id, $sort);
             }
        }
        return $sort;
    }

    public function setmenuAction($request, $response, $args)
    {
        $json_response = $this->add_error(0);
        $req_params = $request->getParams();

        $user = $this->auth->getUserFields();
        if ( !$this->auth->hasRoles([2]) ) { return json_encode($this->add_error(3)); }

        $this->updateMenuItems($args[0], $req_params);

        return $this->getmenuAction($request, $response, $args);
    }



}
