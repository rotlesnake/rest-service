<?php
define("ROOT_PATH",   str_replace("/", DIRECTORY_SEPARATOR, realpath(__DIR__)."/") );
define("APP_PATH",    str_replace("/", DIRECTORY_SEPARATOR, realpath(__DIR__)."/App/") );
define("VENDOR_PATH", str_replace("/", DIRECTORY_SEPARATOR, realpath(__DIR__)."/vendor/") );

$ROOT_URL = str_replace("//", "/", dirname($_SERVER["SCRIPT_NAME"])."/");
define("ROOT_URL", $ROOT_URL );
define("FULL_URL", $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["SERVER_NAME"].ROOT_URL);

require VENDOR_PATH."autoload.php";


$APP = new MapDapRest\App(ROOT_PATH, ROOT_URL, "backend", "frontend");


$APP->initDB([
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'learns',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => 'prj_',
            'engine'    => 'InnoDB', //'InnoDB' 'MyISAM'
        ]);


$APP->setAuth( new \App\Auth\Auth() );

//echo \MapDapRest\Migrate::migrate();
$APP->run(["GET", "POST", "PUT", "DELETE"]);
