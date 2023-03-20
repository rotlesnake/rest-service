<?php
namespace App\Admin\Controllers;


class MigrateController extends \MapDapRest\Controller
{

    /** Миграция **/
    public function indexAction($request, $response, $params) {
       return \MapDapRest\Migrate::migrate();
    }

    /** any other action **/
    public function anyAction($request, $response, $controller, $action, $params) {
       return [];
    }

}//class