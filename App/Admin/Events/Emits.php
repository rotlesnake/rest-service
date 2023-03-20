<?php
namespace App\Admin\Events;


class Emits
{

    public static function someEvent($eventData) 
    {
        $APP = \MapDapRest\App::getInstance();
        $APP->emit("someEvent", $eventData);
    }



}