<?php
require(__DIR__."/../../init.php");
$APP->auth->login(["login"=>"admin", "password"=>"admin"]);

$request = new \MapDapRest\Request();
$response = new \MapDapRest\Response();

$migrateResult = \MapDapRest\Migrate::migrate();

$response->setBody($migrateResult);
$response->send();

