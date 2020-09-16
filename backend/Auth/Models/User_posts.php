<?php
namespace App\Auth\Models;


class User_posts extends \MapDapRest\Model
{
    protected $table = 'user_posts';
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
        //Привязка один к одному
        public function user()
        {
            return $this->belongsTo('App\Auth\Models\Users', 'user_id', 'id');
        }

    //************************************************************************************************
    //************************************************************************************************
    //************************************************************************************************


    public static function modelInfo() {
      $acc_admin = [1];
      $acc_all = [1,2,3,4,5,6,7,8];
      
      return [
	"table"=>"user_posts",
	"primary_key"=>"id",
	"category"=>"Система",
	"name"=>"Сообщения",

        "sortBy"=>["id"],
        "sortDesc"=>["asc"],
        "itemsPerPage"=>100,
        "itemsPerPageVariants"=>[50,100,200,300,500,1000],

	"read"=>$acc_all,
	"add"=>$acc_admin,
	"edit"=>$acc_all,
	"delete"=>[],
	
	"type"=>"standart",
        //"parentTables"=>[["table"=>"user", "id"=>"user_id"]],
        //"childrenTables"=>[["table"=>"user_posts", "id"=>"user_id"]],

        "filter"=>[
            "created_by_user"=>[
		"label"=>"Кто создал",
                "filterType"=>"like",
            ],
        ],

	"columns"=>[
		"id"=>[
 			"type"=>"integer",
 			"label"=>"id",
 			"width"=>200,
 			"read"=>$acc_all,
 			"add"=>[],
 			"edit"=>[],
		],
		"created_at"=>[
 			"type"=>"timestamp",
 			"label"=>"Дата создания",
 			"width"=>200,
 			"read"=>$acc_all,
 			"add"=>[],
 			"edit"=>[],
		],
		"updated_at"=>[
 			"type"=>"timestamp",
 			"label"=>"Дата изменения",
 			"width"=>200,
 			"hidden"=>true,
 			"read"=>$acc_all,
 			"add"=>[],
 			"edit"=>[],
		],
		"created_by_user"=>[
 			"type"=>"linkTable",
 			"label"=>"Создано пользователем",
 			"table"=>"users",
 			"field"=>"<%login%>",
 			"width"=>200,
 			"read"=>$acc_all,
 			"add"=>[],
 			"edit"=>[],
		],

		"user_id"=>[
 			"type"=>"linkTable",
 			"label"=>"User Name",
 			"table"=>"users",
 			"field"=>"<%login%>",
 			"multiple"=>false,
 			"typeSelect"=>"table",
 			"object"=>false,
 			"width"=>200,
 			"read"=>$acc_all,
 			"add"=>$acc_all,
 			"edit"=>$acc_all,
		],


		"text"=>[
 			"type"=>"string",
 			"label"=>"Логин",
 			"placeholder"=>"Фамилия Имя - пользователя",
 			"hint"=>"Уникальное поле",
                        "index"=>"unique",
 			"width"=>200,
 			"rules"=>"[ v => v.length>2 || 'Обязательное поле' ]",
 			"style"=>["prepend-icon"=>"person", "append-icon"=>"person", "type"=>"text", "outlined"=>true, "filled"=>false, "color"=>"#909090", "counter"=>true, "dark"=>false, "dense"=>false, "hide-details"=>false, "persistent-hint"=>false, "rounded"=>false, "shaped"=>false, "clearable"=>false ],
 			"read"=>$acc_all,
 			"add"=>$acc_all,
 			"edit"=>$acc_all,
		],
		
	],


      ];
    }//modelInfo

}//class
