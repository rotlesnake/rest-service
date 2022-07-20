<?php
namespace App;


/**
 * @OA\Info(
 *     version="1.0",
 *     title="Программный продукт ПэйЭниВейЭвриДей",
 *     description="Эта программа предназначена для...",
 *     @OA\Contact(name="ООО ПП")
 * )
 * @OA\Server(
 *     url="https://mysoft.ru",
 *     description="API server"
 * )
 */
class Settings {

    public static $description = "Система ...";

    public static $isTestMode = true;
    public static $replyEmail = "";


/*
    public static function middleware($APP, $controllerName, $actionName, $request, $response, $params)
    {
        file_put_contents(__DIR__."/debug.txt", "call: ".$controllerName."::".$actionName);
    }
*/


}
