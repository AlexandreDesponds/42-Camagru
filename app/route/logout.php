<?php
    $app->get('/logout', function () use ($app) {
        $app->access('onlyMember');
        unset($_SESSION['people']);
        $app->redirect('/');
    });