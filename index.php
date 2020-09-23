<?php
//--------------------------------------------------------------------
//php server: /opt/lampp/bin/php -S 127.0.0.1:8000 -t ./ 
//--------------------------------------------------------------------
define("ROOT_PATH",   str_replace("/", DIRECTORY_SEPARATOR, realpath(__DIR__)."/") );
define("APP_PATH",    str_replace("/", DIRECTORY_SEPARATOR, realpath(__DIR__)."/backend/") );
define("VENDOR_PATH", str_replace("/", DIRECTORY_SEPARATOR, realpath(__DIR__)."/vendor/") );

$ROOT_URL = str_replace("//", "/", dirname($_SERVER["SCRIPT_NAME"])."/");
if (!isset($_SERVER["REQUEST_SCHEME"])) $_SERVER["REQUEST_SCHEME"]="http";

define("ROOT_URL", $ROOT_URL );
define("FULL_URL", $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["SERVER_NAME"].ROOT_URL);

require(VENDOR_PATH."autoload.php");



$APP = new MapDapRest\App(ROOT_PATH, ROOT_URL, "backend", "App", "frontend");
$settings = require(ROOT_PATH.'settings.php');
$APP->initDB($settings['database']);
$APP->setAuth( new \App\Auth\Auth() );

ini_set('date.timezone', $settings['timezone']);
date_default_timezone_set($settings['timezone']);


$APP->run(["GET", "POST", "PUT", "DELETE"]);
