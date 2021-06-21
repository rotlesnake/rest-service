<?php
namespace App\Auth\Models;


class Users extends \MapDapRest\Model
{
    protected $table = "users";
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
	$APP = \MapDapRest\App::getInstance();
        if (!$APP->auth->user) { throw new Exception('user not found'); }
 
        if ($APP->auth->user->hasRoles([1])) return $query; //Админу выдаем всех

        return $query->where('id', '=', $APP->auth->user->id); //Другие видят только себя
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

      //** Отношения **********************************************************************************************
        //Привязка одной записи из таблицы к локальному полю
        public function role()
        {
            return $this->hasOne('App\Auth\Models\Roles', 'id', 'role_id');
        }

        //Привязка множественных записей из таблицы по внешнему полю
        public function posts()
        {
            return $this->hasMany('App\Auth\Models\UserPosts', 'user_id', 'id');
        }


      //** Свои атрибуты **********************************************************************************************
        public function getRolesAttribute() {
            return array_map('intval', explode(',', $this->role_id));
        }

        public function getPhotoAttribute($value) {
            if (strlen($value) == 0) {
                return FULL_URL."uploads/image/user/default.jpg";
            }
            return $value;
        }

      //** Свои функции **********************************************************************************************
        //проверить на содержание одной из ролей
        public function hasRoles($checkList=[]) {
            if (gettype($checkList)!="array") $checkList = explode(",", $checkList);
            if (count($checkList)==0) return false;

            $rolesList = $this->roles;       // -> getRolesAttribute()
            foreach ($checkList as $v) {
                if (in_array($v, $rolesList)) return true;
            }
            return false;
        }
    //************************************************************************************************
    //************************************************************************************************
    //************************************************************************************************
    //************************************************************************************************


    public static function modelInfo() {
      $acc_admin = [1];
      $acc_all = \MapDapRest\Utils::getAllRoles();
      
      return [
           "table"=>"users", 
           "primary_key"=>"id", 
           "category"=>"Система", 
           "name"=>"Пользователи системы", 
           "sortBy"=>["id", ], 
           "itemsPerPage"=>100, 
           "itemsPerPageVariants"=>[50,100,200,300,500,1000,], 
           "read"=>$acc_all, 
           "add"=>$acc_admin, 
           "edit"=>$acc_all, 
           "delete"=>[], 
           "childrenTables"=>[["table"=>"user_posts", "id"=>"user_id"]],
           "filter"=>[
                   "created_by_user"=>["label"=>"Кто создал", "filterType"=>"=" ], 
                   "login"=>["label"=>"Логин", "filterType"=>"like" ], 
                   ], 
           "columns"=>[
                   "id"=>["type"=>"integer", "label"=>"id", "read"=>$acc_all, "add"=>[], "edit"=>[] ], 
                   "created_at"=>["type"=>"timestamp", "label"=>"Дата создания", "read"=>$acc_all, "hidden"=>true, "add"=>[], "edit"=>[] ], 
                   "updated_at"=>["type"=>"timestamp", "label"=>"Дата изменения", "read"=>$acc_all, "hidden"=>true, "add"=>[], "edit"=>[] ], 
                   "created_by_user"=>["type"=>"linkTable", "label"=>"Создано пользователем", "table"=>"users", "field"=>"login", "hidden"=>true, "read"=>$acc_all, "add"=>[], "edit"=>[] ], 

                   "login"=>["type"=>"string", "label"=>"Логин", "rules"=>"[v=>v && v.length > 3 || 'Обязательное поле']", "read"=>$acc_all, "add"=>$acc_admin, "edit"=>$acc_admin, "visible"=>true], 
                   "password"=>["type"=>"password", "label"=>"Пароль", "read"=>$acc_all, "add"=>$acc_all, "edit"=>$acc_all, "name"=>"password", ], 
                   "role_id"=>["type"=>"linkTable", "label"=>"Роль", "table"=>"roles", "field"=>"name", "read"=>$acc_all, "add"=>$acc_admin, "edit"=>$acc_admin ], 
                   "status"=>["type"=>"select", "label"=>"Статус", "typeSelect"=>"combobox", "items"=>["-1"=>"Заблокирован", "1"=>"Активный", ], "defaut"=>"1", "read"=>$acc_all, "add"=>$acc_admin, "edit"=>$acc_admin ], 
                   "blocking_comment"=>["type"=>"string", "label"=>"Причина блокировки", "rules"=>"[v=>v && v.length > 3 || 'Обязательное поле']", "vif"=>"[status] < 0", "read"=>$acc_all, "add"=>$acc_admin, "edit"=>$acc_admin, "visible"=>true], 
                   "photo"=>["type"=>"images", "label"=>"Фотография", "multiple"=>false, "read"=>$acc_all, "add"=>$acc_all, "edit"=>$acc_all, "name"=>"photo", ], 

                   "token"=>["type"=>"string", "label"=>"Токен", "index"=>"index", "width"=>200,         "read"=>$acc_all, "add"=>[], "edit"=>[], "name"=>"token", "hidden"=>true, "masked"=>false, ], 
                   "token_expire"=>["type"=>"dateTime", "label"=>"Срок токена", "width"=>200,            "read"=>[], "add"=>[], "edit"=>[], "name"=>"token_expire", ], 
                   "refresh_token"=>["type"=>"string", "label"=>"Токен", "index"=>"index", "width"=>200, "read"=>[], "add"=>[], "edit"=>[], "name"=>"refresh_token", ], 
                   "refresh_token_expire"=>["type"=>"dateTime", "label"=>"Срок токена", "width"=>200,    "read"=>[], "add"=>[], "edit"=>[], "name"=>"refresh_token_expire", ], 
                   ], 
           
             ];
    }

}//class


