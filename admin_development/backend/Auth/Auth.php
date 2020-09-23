<?php
namespace AdminDev\Auth;

use \MapDapRest\App;

class EmptyClass {}

class Auth 
{

	public $user = null;

	
	public function __construct(){

	}
	

        public function isGuest() {
          if (!isset($this->user)) return true;
        }


        public function login($credentials) {
            $APP = App::getInstance();
            if ($credentials['login']=="admin" && $credentials['password']=="12345") {
                $hours_token = 4;
                $this->user = new EmptyClass();
                $this->user->token_expire = date("Y-m-d H:i:s", strtotime("+".$hours_token." hours"));
                $this->user->token = sha1($credentials['login'].$this->user->token_expire);
                setcookie("token", $this->user->token, time()+($hours_token*60*60), $APP->ROOT_URL, $_SERVER["SERVER_NAME"]);
                return true;
            }
            return false;
        }

        public function logout() {
            $this->user = null;
            setcookie( "token", "", time()-3600, '/', '');
        }

        public function getFields() {
            return (array)$this->user;
        }
}//class
