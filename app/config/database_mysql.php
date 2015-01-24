<?php

return [
    'dsn'     => "mysql:host=blu-ray.student.bth.se;dbname=frjf14;",
    'username'        => "frjf14",
    'password'        => "2gpOI$8d",
    // 'dsn'     => "mysql:host=localhost;dbname=project;",
    // 'username'        => "root",
    // 'password'        => "password",
    'driver_options'  => [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"],
    'table_prefix'    => "project_", //do not change since alot has had to be hard coded into sql queries.
    //'verbose' => true,
    //'debug_connect' => 'true',
];
