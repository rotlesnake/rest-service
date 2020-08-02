<?php

namespace App\Auth\Models;


class Users extends \MapDapRest\Model
{
    protected $table = 'users';
 
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
    public static function beforePost($model, $post) {
    }

    //После сохранения
    public static function afterPost($model, $post) {
    }
    //--------------------------------



    //Область видимости записей
    //фильтр на чтение
    public function scopeFilterRead($query)
    {
	$APP = \MapDapRest\App::getInstance();
        $user = $APP->getCurrentUser();
        if (!$user) { throw new Exception('user not found'); }
 
        if ($user->hasRoles([1])) return $query;

        return $query->where('created_by_user', '=', $user->id);
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
    //--------------------------------


    //************************************************************************************************
    //Пользовательская логика модели******************************************************************
    //************************************************************************************************

        public function getRolesAttribute($value) {
           return array_map('intval', explode(',', $value));
        }

        public function getPhotoAttribute($value) {
          if (strlen($value) > 1) {
            $json = json_decode($value);
            $photo = FULL_URL."uploads/image/users/".$this->id."_photo_".$json[0];
          } else {
            $photo = FULL_URL."uploads/image/users/default.jpg";
          }
           return $photo;
        }
        
        //проверить на содержание одной из ролей
        public function hasRoles($checkList=[]) {
          if (gettype($checkList)!="array") $checkList = explode(",", $checkList);
          if (count($checkList)==0) return false;

          $rolesList = $this->roles();
          foreach ($checkList as $v) {
             if (in_array($v, $rolesList)) return true;
          }
          return false;
        }
    //************************************************************************************************
    //************************************************************************************************
    //************************************************************************************************


    public static function modelInfo() {
      $acc_admin = [1];
      $acc_all = [1,2,3,4,5,6,7,8];
      
      return [
	"table"=>"users",
	"category"=>"Система",
	"name"=>"Пользователи",

        "sortBy"=>["id"],
        "sortDesc"=>["asc"],
        "itemsPerPage"=>100,
        "itemsPerPageVariants"=>[50,100,200,300,500,1000],

	"read"=>[],
	"add"=>[],
	"edit"=>[],
	"delete"=>[],
	
	"type"=>"standart",
        "style"=>["outlined"=>true, "filled"=>false, "color"=>"#909090", "counter"=>true, "dark"=>false, "dense"=>false, "hide-details"=>false, "persistent-hint"=>false, "rounded"=>false, "shaped"=>false, "clearable"=>false],

        //"parentTables"=>[["table"=>"users", "id"=>"user_id"]],
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
 			"visible"=>true,
 			"read"=>[],
 			"add"=>[],
 			"edit"=>[],
		],
		"created_at"=>[
 			"type"=>"dateTime",
 			"label"=>"Дата создания",
 			"width"=>200,
 			"read"=>[],
 			"add"=>[],
 			"edit"=>[],
		],
		"updated_at"=>[
 			"type"=>"dateTime",
 			"label"=>"Дата изменения",
 			"width"=>200,
 			"hidden"=>true,
 			"read"=>[],
 			"add"=>[],
 			"edit"=>[],
		],
		"created_by_user"=>[
 			"type"=>"linkTable",
 			"label"=>"Создано пользователем",
 			"table"=>"users",
 			"field"=>"<%login%>",
 			"multiple"=>false,
 			"typeSelect"=>"table",
 			"object"=>false,
 			"width"=>200,
 			"hidden"=>true,
 			"read"=>[],
 			"add"=>[],
 			"edit"=>[],
		],


		"login"=>[
 			"type"=>"string",
 			"label"=>"Логин",
 			"placeholder"=>"Фамилия Имя - пользователя",
 			"hint"=>"Уникальное поле",
                        "index"=>"unique",
 			"width"=>200,
 			"rules"=>"[ v => v.length>2 || 'Обязательное поле' ]",
 			"style"=>["prepend-icon"=>"person", "append-icon"=>"person", "type"=>"text", "outlined"=>true, "filled"=>false, "color"=>"#909090", "counter"=>true, "dark"=>false, "dense"=>false, "hide-details"=>false, "persistent-hint"=>false, "rounded"=>false, "shaped"=>false, "clearable"=>false ],
 			"read"=>[],
 			"add"=>[],
 			"edit"=>[],
		],
		"password"=>[
 			"type"=>"password",
 			"label"=>"Пароль",
 			"width"=>200,
 			"rules"=>"[ v => v.length==0 || v.length>7 || 'Минимальная длинна 8 символов' ]",
 			"defaut"=>"12345678",
 			"read"=>[],
 			"add"=>[],
 			"edit"=>[],
		],
		"roles"=>[
 			"type"=>"linkTable",
 			"label"=>"Роли",
 			"table"=>"roles",
 			"field"=>"<%name%>",
 			"multiple"=>true,
 			"typeSelect"=>"table",
 			"object"=>false,
 			"width"=>200,
 			"read"=>[],
 			"add"=>[],
 			"edit"=>[],
		],
		"status"=>[
 			"type"=>"select",
 			"label"=>"Статус",
 			"typeSelect"=>"combobox",
 			"items"=>["1"=>"Активный", "0"=>"Заблокирован"],
 			"defaut"=>"1",
 			"width"=>200,
 			"read"=>[],
 			"add"=>[],
 			"edit"=>[],
		],
		


		"email"=>[
 			"type"=>"string",
 			"label"=>"E-Mail",
 			"placeholder"=>"name@domain.ru",
 			"hint"=>"",
 			"width"=>200,
 			"rules"=>"[ v => v.length>2 || 'Обязательное поле' ]",
 			"read"=>[],
 			"add"=>[],
 			"edit"=>[],
                ],
		"photo"=>[
 			"type"=>"images",
 			"label"=>"Фотография",
 			"multiple"=>false,
 			"hint"=>"",
 			"width"=>200,
 			"rules"=>"[(files) => (files.length==0) || (!files[0].size) || (files[0] && files[0].size < 2*1024*1024) || 'Размер файла должен быть не более 2 MB']",
 			"read"=>[],
 			"add"=>[],
 			"edit"=>[],
		],
		"phone"=>[
 			"type"=>"string",
 			"label"=>"Телефон",
 			"placeholder"=>"8(###)###-##-##",
 			"mask"=>"8(999)999-99-99",
 			"hint"=>"",
 			"width"=>200,
 			"rules"=>"[ v => (v.length>9 && v.indexOf('_')<0) || 'Обязательное поле' ]",
 			"read"=>[],
 			"add"=>[],
 			"edit"=>[],
                ],
		
	],


      ];
    }//modelInfo

}//class
