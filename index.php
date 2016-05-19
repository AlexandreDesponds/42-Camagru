<?php

    session_start();

    function __autoload($name) {
        require_once(str_replace('\\', '/', $name).'.class.php');
    }

    $app = new \app\Ponps;
    $app->setRoute($_SERVER['REQUEST_URI']);
    $app->setMethod($_SERVER['REQUEST_METHOD']);
    $app->setPrefix('');
    require_once('app/route/route.php');
    $app->run();