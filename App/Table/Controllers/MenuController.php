<?php

namespace App\Controllers\Api;

use App\Models\Menu;
use App\Controllers\ApiBaseController;

class MenuController extends ApiBaseController
{


    public function anyAction($request, $response, $action, $args)
    {
        $json_response = $this->add_error(0);
        $req_params = $request->getParams();

        $user = $this->auth->getUserFields();
        $json_response["items"] = [];

        //Если это Администратор то доп.пункты меню
        if ( $this->auth->hasRoles([2]) && $action=="right" ) {
            $admin_items = [];
            //array_push($admin_items, ["id"=>-5, "pid"=>-1, "title"=>"База данных",       "icon"=>"domain",                 "to"=>"/admin/database" ]);
            array_push($admin_items, ["id"=>-6, "pid"=>-1, "title"=>"Меню основное",     "icon"=>"format_indent_decrease", "to"=>"/admin/menu1" ]);
            array_push($admin_items, ["id"=>-7, "pid"=>-1, "title"=>"Меню второе",       "icon"=>"format_indent_increase", "to"=>"/admin/menu2" ]);
            array_push($admin_items, ["id"=>-8, "pid"=>-1, "title"=>"Роли",              "icon"=>"wc",                     "to"=>"/table/sys_roles" ]);
            array_push($admin_items, ["id"=>-9, "pid"=>-1, "title"=>"Пользователи",      "icon"=>"group",                  "to"=>"/table/users" ]);

            array_push($json_response["items"], ["id"=>-1, "pid"=>0,  "title"=>"Администрирование", "icon"=>"build", "children"=>$admin_items ]);
        }//----------------------------------------

        $menu = $this->getMenuTree($action, 0);
        foreach ($menu as $item) {
          array_push($json_response["items"], $item);
        }

        return json_encode($json_response);
    }


    public function getMenuTree($menu_name, $parent_id=0) {
        $json_response = [];
        $items = Menu::where("menu", $menu_name)->where("parent_id", $parent_id)->orderBy("sort")->get();
        foreach ($items as $item) {
             if (!$this->auth->hasRoles($item->roles_ids)) {continue;}
             $item_tree = [];
             $item_tree["id"] = $item->id;
             $item_tree["pid"] = $item->parent_id;
             $item_tree["title"] = $item->title;
             $item_tree["icon"]  = $item->icon;
             $item_tree["to"]    = $item->url;
             $item_tree["children"] = $this->getMenuTree($menu_name, $item->id);

             array_push($json_response, $item_tree);
        }
        return $json_response;
    }

}
