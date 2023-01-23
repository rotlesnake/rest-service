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
        $model->slug = \MapDapRest\Utils::getSlug($model->name);
    }
    
    //После добавления
    public static function afterAdd($model) {
    }
    //--------------------------------

    //Изменение записи----------------------
    //Перед изменением
    public static function beforeEdit($model) {
        $model->slug = \MapDapRest\Utils::getSlug($model->name);
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
	"name"=>"Список ролей в системе",

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
                "created_at" => ["type"=>"timestamp", "label"=>"Дата создания", "hidden"=>true, "read"=>$acc_all, "add"=>[], "edit"=>[] ],
                "updated_at" => ["type"=>"timestamp", "label"=>"Дата изменения", "hidden"=>true, "read"=>$acc_all, "add"=>[], "edit"=>[] ],
                "created_by_user" => ["type"=>"linkTable", "label"=>"Создано пользователем", "table"=>"users", "field"=>"login", "hidden"=>true, "read"=>$acc_all, "add"=>[], "edit"=>[] ],

                "slug" => ["type"=>"string", "label"=>"Код",  "read"=>$acc_all, "add"=>$acc_admin, "edit"=>$acc_admin ], 
                "name" => ["type"=>"string", "label"=>"Наименование роли",  "read"=>$acc_all, "add"=>$acc_admin, "edit"=>$acc_admin ], 
                "description" => ["type"=>"string", "label"=>"Описание роли",  "read"=>$acc_all, "add"=>$acc_admin, "edit"=>$acc_admin ], 
	],
	"seeds"=> [
	             [
                      'id'    => 1,
                      'created_by_user'    => 1,
                      'slug'    => 'admin',
                      'name'    => 'Администратор системы',
                      'description'    => 'Администратор системы',
                     ],
	             [
                      'id'    => 2,
                      'created_by_user'    => 1,
                      'slug'    => 'support',
                      'name'    => 'Помошник администратора',
                      'description'    => 'Помошник администратора',
                     ],
	             [
                      'id'    => 3,
                      'created_by_user'    => 1,
                      'slug'    => 'dir',
                      'name'    => 'Директор',
                      'description'    => 'Директор',
                     ],
	             [
                      'id'    => 4,
                      'created_by_user'    => 1,
                      'slug'    => 'buh',
                      'name'    => 'Бухгалтер',
                      'description'    => 'Бухгалтер',
                     ],
	             [
                      'id'    => 5,
                      'created_by_user'    => 1,
                      'slug'    => 'user',
                      'name'    => 'Пользователь',
                      'description'    => 'Пользователь',
                     ],
        ]

      ];
    }//modelInfo

}//class
