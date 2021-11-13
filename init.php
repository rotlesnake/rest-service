<?php
define("ROOT_PATH",   str_replace("/", DIRECTORY_SEPARATOR, realpath(__DIR__)."/") );
define("APP_PATH",    str_replace("/", DIRECTORY_SEPARATOR, realpath(__DIR__)."/App/") );

require("vendor/autoload.php");


$ROOT_URL = str_replace("//", "/", dirname($_SERVER["SCRIPT_NAME"])."/");
if (!isset($_SERVER["REQUEST_SCHEME"])) $_SERVER["REQUEST_SCHEME"]="http";
define("ROOT_URL", $ROOT_URL );
define("FULL_URL", $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["SERVER_NAME"].(in_array($_SERVER["SERVER_PORT"],[80,443]) ? "" : ":".$_SERVER["SERVER_PORT"]).ROOT_URL);


$APP = new MapDapRest\App(ROOT_PATH, ROOT_URL, "App", "App", "www");
$settings = [
        'debug'         => true,
        'timezone'      => 'Etc/GMT-3',
/*
        'database' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'port'      => '3306',
            'database'  => 'test',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
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

