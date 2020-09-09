<?php
namespace App\Auth;

use \App\Auth\Models\Users;

class Auth extends \MapDapRest\Auth
{

	public $user = null;
	public $ModelUsers = "\\App\\Auth\\Models\\Users";
	
	public function __construct(){

	}
	


}//class
