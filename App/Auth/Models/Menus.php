<?php
namespace App\Auth\Models;


class Menus extends \MapDapRest\Model
{
    protected $table = "menus";
    protected $primaryKey = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
 
    //Добавление записи----------------------
    //Перед добавлением
    public static function beforeAdd($model) {
    }
    
    //После добавления
    public static function afterAdd($model) {
    }
    //--------------------------------

    //Изменение записи----------------------
    //Перед изменением
    public static function beforeEdit($model) {
    }

    //После изменения
    public static function afterEdit($model) {
    }
    //--------------------------------

    //Удаление записи----------------------
    //Перед удалением
    public static function beforeDelete($model) {
    }

    //После удаления
    public static function afterDelete($model) {
    }
    //--------------------------------


    //Перехват post данных
    //Перед сохранением
    public static function beforePost($action, $model, $post) {
    }

    //После сохранения
    public static function afterPost($action, $model, $post) {
    }
    //--------------------------------



    //************************************************************************************************
    //Области видимости записей
    //фильтр на чтение
    public function scopeFilterRead($query)
    {
        return $query;
    }

    //фильтр на изменение
    public function scopeFilterEdit($query)
    {
        return $query;
    }

    //фильтр на удаление
    public function scopeFilterDelete($query)
    {
        return $query;
    }
    //************************************************************************************************


    //************************************************************************************************
    //************************************************************************************************
    //************************************************************************************************
    //Пользовательская логика модели******************************************************************
    //************************************************************************************************

    //************************************************************************************************
    //************************************************************************************************
    //************************************************************************************************
    //************************************************************************************************


    public static function modelInfo() {
      $acc_admin = [1];
      $acc_all = \MapDapRest\Utils::getAllRoles();
      
      return [
           "table"=>"menus", 
           "primary_key"=>"id", 
           "category"=>"Система", 
           "name"=>"Меню системы", 
           "sortBy"=>["id", ], 
           "itemsPerPage"=>100, 
           "itemsPerPageVariants"=>[50,100,200,300,500,1000,], 
           "read"=>$acc_all, 
           "add"=>$acc_admin, 
           "edit"=>$acc_admin, 
           "delete"=>$acc_admin, 
           "parentTables"=>[["table"=>"menus", "field"=>"parent_id"], ["table"=>"menus", "field"=>"sort"]],
           "filter"=>[
                   ], 
           "columns"=>[
                   "id"=>["type"=>"integer", "label"=>"id", "read"=>$acc_all, "add"=>[], "edit"=>[] ], 
                   "created_at"=>["type"=>"timestamp", "label"=>"Дата создания", "read"=>$acc_all, "hidden"=>true, "add"=>[], "edit"=>[] ], 
                   "updated_at"=>["type"=>"timestamp", "label"=>"Дата изменения", "read"=>$acc_all, "hidden"=>true, "add"=>[], "edit"=>[] ], 
                   "created_by_user"=>["type"=>"linkTable", "label"=>"Создано пользователем", "table"=>"users", "field"=>"login", "hidden"=>true, "read"=>$acc_all, "add"=>[], "edit"=>[] ], 

                   "type"=>["type"=>"select", "label"=>"Тип меню", "typeSelect"=>"combobox", "items"=>["1"=>"Основное", "2"=>"Дополнительное", ], "default"=>"1", "read"=>$acc_all, "add"=>$acc_admin, "edit"=>$acc_admin ], 
                   "parent_id"=>["type"=>"linkTable", "label"=>"Родительский пункт", "table"=>"menus", "field"=>"name", "read"=>$acc_all, "add"=>$acc_admin, "edit"=>$acc_admin ], 
                   "sort"=>["type"=>"integer", "label"=>"Сортировка", "read"=>$acc_all, "add"=>$acc_admin, "edit"=>$acc_admin ], 

                   "name"=>["type"=>"string", "label"=>"Название пункта", "read"=>$acc_all, "add"=>$acc_admin, "edit"=>$acc_admin], 
                   "icon"=>["type"=>"string", "label"=>"Иконка", "read"=>$acc_all, "add"=>$acc_admin, "edit"=>$acc_admin], 
                   "url"=>["type"=>"string", "label"=>"URL ссылка", "read"=>$acc_all, "add"=>$acc_admin, "edit"=>$acc_admin], 
                   "roles"=>["type"=>"linkTable", "label"=>"Каким ролям видна", "table"=>"roles", "field"=>"name", "multiple"=>true, "read"=>$acc_all, "add"=>$acc_admin, "edit"=>$acc_admin ], 
		           "app_access"=>["type"=>"linkTable", "label"=>"Разрешения", "table"=>"app_access_list", "field"=>"[name] ([slug])", "typeSelect"=>"tree", "multiple"=>true, "read"=>$acc_all, "add"=>$acc_admin, "edit"=>$acc_admin ],
                   ], 
           
             ];
    }

}//class


