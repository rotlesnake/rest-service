<?php
namespace App\Auth\Models;


class Roles extends \MapDapRest\Model
{
    protected $table = "roles";
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
    //Пользовательская логика модели******************************************************************
    //************************************************************************************************

    //************************************************************************************************


    public static function modelInfo() {
      $acc_admin = [1];
      $acc_all = \MapDapRest\Utils::getAllRoles();
      
      return [
	"table"=>"roles",
	"primary_key"=>"id",
	"category"=>"Система",
	"name"=>"Таблица или Справочник",

        "sortBy"=>["id"],
        "itemsPerPage"=>100,
        "itemsPerPageVariants"=>[50,100,200,300,500,1000],

	"read"=>$acc_all,
	"add"=>$acc_admin,
	"edit"=>$acc_all,
	"delete"=>[],
	
        "filter"=>[
            "created_by_user"=>[
		"label"=>"Кто создал",
                "filterType"=>"like",
            ],
        ],

	"columns"=>[
                "id" => ["type"=>"integer", "label"=>"id", "read"=>$acc_all, "add"=>[], "edit"=>[] ],
                "created_at" => ["type"=>"timestamp", "label"=>"Дата создания", "read"=>$acc_all, "add"=>[], "edit"=>[] ],
                "updated_at" => ["type"=>"timestamp", "label"=>"Дата изменения", "read"=>$acc_all, "add"=>[], "edit"=>[] ],
                "created_by_user" => ["type"=>"linkTable", "label"=>"Создано пользователем", "table"=>"users", "field"=>"login", "read"=>$acc_all, "add"=>[], "edit"=>[] ],

                "name" => ["type"=>"string", "label"=>"Наименование",  "read"=>$acc_all, "add"=>$acc_all, "edit"=>$acc_all ], 
	],


      ];
    }//modelInfo

}//class
