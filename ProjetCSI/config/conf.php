<?php

class Conf{

    static $debug = 1;

    static $conf = 'default';

    static $database = array(

        'default' => array(
            'host' => 'localhost',
            'port' => '5432',
            'database' => 'script',
            'login' => 'postgres',
            'password' => 'admin'
        )
    );
}