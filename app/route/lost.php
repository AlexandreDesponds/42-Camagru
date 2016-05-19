<?php
    $app->get('/lost', function () use ($app) {
        $app->access('onlyGuest');
        $app->render('lost', array());
    });

    $app->post('/lost', function () use ($app) {
        $app->access('onlyGuest');
        if (isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['pseudo']) && !empty($_POST['pseudo'])) {
            $people = \app\ORM::getInstance()->findOne('people', array('email' => $_POST['email'], 'pseudo' => $_POST['pseudo']));
            if ($people instanceof \app\model\People) {
                $newPassword = $people->changePassword();
                mail($people->getEmail(), 'Oublie de ton mot de passe', 'Bonjour '.$people->getPseudo().',<br><br> Voici ton nouveau mot de passe : '.$newPassword.'<br><br>A bient&ocirc;t :)');
            }
        }
        $app->success('Ton nouveau mot de passe a été envoyé par email');
        $app->redirect('login');
    });