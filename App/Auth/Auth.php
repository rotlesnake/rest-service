<?php

namespace App\Auth;


use \App\Auth\Models\Users;


class Auth extends \MapDapRest\Auth
{

	public $user = null;
	public $ModelUsers = "\\App\\Auth\\Models\\Users";
	
	public function __construct(){

	}
	

        public function getFields($keys=[], $exclude=[]) {
            $fields = $this->getAllFields();
            unset($fields["created_by_user"]);
            unset($fields["password"]);
            unset($fields["created_at"]);
            unset($fields["updated_at"]);
  
            if (count($keys)>0) {
               $fields = array_filter($fields, function($k) use($keys) { return in_array($k,$keys); }, ARRAY_FILTER_USE_KEY);
            }
            if (count($exclude)>0) {
               foreach ($exclude as $k=>$v) {
                  unset($fields[$v]);
               }
            }
            
            return $fields;
        }


        public function getAllFields() {
            $fields = $this->user->attributesToArray();
            return $fields;
        }


      



}//class
