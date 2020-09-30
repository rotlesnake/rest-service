<?php
return [
        'debug'         => true,
        'timezone'      => 'Etc/GMT-3',

        'database' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'port'      => '3306',
            'database'  => 'learns',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => 'prj_',
            'engine'    => 'InnoDB', //'InnoDB' 'MyISAM'
        ],

];
