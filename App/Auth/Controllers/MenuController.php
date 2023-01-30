<?php
namespace App\Auth\Controllers;

use App\Auth\Models\Menus;


class MenuController extends \MapDapRest\Controller
{

    public $user_acl_ids = [];

    /** Получить меню <br>
       @param string $type  <i>- тип меню</i>
       @return object { items:[{}] }
    **/
    public function getAction($request, $response, $params) {
        $me = $this->APP->auth->getFields();
        $this->user_acl_ids = \MapDapRest\Utils::getUserAcl($this->APP->auth->user->id, "id");
        $json_response = ["items"=>[]];

        $menu = $this->getMenuTree($request->getParam("type"), 0);
        foreach ($menu as $item) {
          array_push($json_response["items"], $item);
        }

        $json_response["columns"] = Menus::modelInfo()["columns"];
        foreach ($json_response["columns"] as $x=>$y) {
            if (!$this->APP->auth->hasRoles($y["edit"])) { $json_response["columns"][$x]["protected"]=true; } 
            if (isset($json_response["columns"][$x]["read"])) { unset($json_response["columns"][$x]["read"]); }
            if (isset($json_response["columns"][$x]["add"])) { unset($json_response["columns"][$x]["add"]); }
            if (isset($json_response["columns"][$x]["edit"])) { unset($json_response["columns"][$x]["edit"]); }
        }
        return $json_response;
    }


    /** Изменить меню <br>
       @param string $type  <i>- тип меню</i>
       @param array $menu  <i>- пункты меню</i>
       @return object { items:[{}] }
    **/
    public function setAction($request, $response, $params) {
        $me = $this->APP->auth->getFields();
        $this->user_acl_ids = \MapDapRest\Utils::getUserAcl($this->APP->auth->user->id, "id");

        $this->setMenuTree($request->getParam("type"), $request->getParam("menu"), 0,0);

        $json_response = ["items"=>[]];
        $menu = $this->getMenuTree($request->getParam("type"), 0);
        foreach ($menu as $item) {
          array_push($json_response["items"], $item);
        }
        return $json_response;
    }


    /** Удалить пункт меню <br>
       @param int $id  <i>- id меню</i>
    **/
    public function deleteAction($request, $response, $params) {
        $this->APP->DB::table("menus")->where("id", $request->getParam("id"))->delete();
        return [];
    }




    public function getMenuTree($menu_type, $parent_id=0) {
        $json_response = [];
        $items = Menus::where("type", $menu_type)->where("parent_id", $parent_id)->orderBy("sort")->get();
        foreach ($items as $item) {
             if (!$this->APP->auth->hasRoles($item->roles)) {continue;}
             if (isset($item->app_access) && strlen($item->app_access)>0) {
                 $reqAccess = array_map('intval', explode(',', $item->app_access));
                 if (!\MapDapRest\Utils::inArray($reqAccess, $this->user_acl_ids)) continue;
             }
             $item_tree = $item->getConvertedRow();
             $item_tree["server_id"] = $item_tree["id"];
             $item_tree["children"] = $this->getMenuTree($menu_type, $item->id);
             array_push($json_response, $item_tree);
        }
        return $json_response;
    }


    public function setMenuTree($menu_type, $items, $parent_id=0, $sort=0) {
        foreach ($items as $item) {
             $sort++;
             $id = 0;
             if (isset($item["server_id"])) $id = (int)$item["server_id"];

             $row = Menus::findOrNew($id);
             $row = $row->fillRow("add", $item);
             $row->type = $menu_type;
             $row->parent_id = $parent_id;
             $row->sort = $sort;
             $row->save();

             if (isset($item["children"]) && count($item["children"])>0 ) {
                 $sort = $this->setMenuTree($menu_type, $item["children"], $row->id, $sort);
             }
        }
        return $sort;
    }

}