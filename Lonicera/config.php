<?php
$_config = array(
    'mode'              => 'debug',
    'filter'            => true,
    'charSet'           => 'utf-8',
    'defaultApp'        => 'front',
    'defaultController' => 'index',
    'defaultAction'     => 'index',
    'UrlControllerName' => 'c',
    'UrlActionName'     => 'g',
    'db'                => array(
        'dsn'      => 'mysql:host=127.0.0.1;dbname=lonicera',
        'username' => 'root',
        'password' => '123456',
        'param'    => array(),
    ),
    'smtp'              => array(),
);