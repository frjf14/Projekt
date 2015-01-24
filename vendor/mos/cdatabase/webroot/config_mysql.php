<?php

return [
    'dsn'     => "mysql:host=blu-ray.student.bth.se;dbname=frjf14;",
    'username'        => "test",
    'password'        => "test",
    'driver_options'  => [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"],
    'table_prefix'    => "test_",
    'verbose' => true,
    'debug_connect' => 'true',
];
