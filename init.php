<?php
define("ROOT_PATH",   str_replace("/", DIRECTORY_SEPARATOR, realpath(__DIR__)."/") );
define("APP_PATH",    str_replace("/", DIRECTORY_SEPARATOR, realpath(__DIR__)."/App/") );

require("vendor/autoload.php");


$ROOT_URL = str_replace("//", "/", dirname($_SERVER["SCRIPT_NAME"])."/");
if (!isset($_SERVER["REQUEST_SCHEME"])) $_SERVER["REQUEST_SCHEME"]="http";
define("ROOT_URL", $ROOT_URL );
define("FULL_URL", $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["SERVER_NAME"].ROOT_URL);


$APP = new MapDapRest\App(ROOT_PATH, ROOT_URL, "App", "App", "www");
$settings = [
        'debug'         => true,
        'timezone'      => 'Etc/GMT-3',
/*
        'database' => [
            'driver'    => 'mysql',
            'host'      => 'l2xl.ru',
            'port'      => '3306',
            'database'  => 'u0513062_snmig',
            'username'  => 'u0513062_snmig',
            'password'  => '0O1i5Z2m',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            //'prefix'    => 'ch_game_',
            'prefix'    => 'chronos_',
            'engine'    => 'InnoDB', //'InnoDB' 'MyISAM'
        ],
*/

        'database' => [
            'driver'    => 'sqlite',
            'database'  => ROOT_PATH."App/database.db",
            'prefix'    => '',
        ],

];

if ($settings['debug']===false) error_reporting(0);

$APP->initDB($settings['database']);
$APP->setAuth( new \App\Auth\Auth() );

ini_set('date.timezone', $settings['timezone']);
date_default_timezone_set($settings['timezone']);

