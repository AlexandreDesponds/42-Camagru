<?php
    $app->get('/login', function () use ($app) {
        $app->access('onlyGuest');
        $app->render('login', array());
    });

    $app->post('/login', function () use ($app) {
        $app->access('onlyGuest');
        $people = \app\model\People::login($_POST['pseudo'], $_POST['password']);
        if ($people instanceof \app\model\People) {
            $_SESSION['people'] = serialize($people);
            $app->redirect('/');
        }
        if ($people == 1)
            $error = 'Ton adresse email n\'a pas encore été validée';
        else
            $error = 'Malheureusement, aucun compte n\'a été trouvé !';
        $app->render('login', array('error' => $error));
    });