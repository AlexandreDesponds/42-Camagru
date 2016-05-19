<?php
    $app->get('/register', function () use ($app) {
        $app->access('onlyGuest');
        if (isset($_GET['key'])) {
            if(\app\model\People::validateEmail($_GET['key'])) {
                $app->success("Ton compte a été validé");
                $app->redirect('/login');
            }else{
                $app->error("Une erreur c'est produite");
                $app->redirect('/register');
            }
        }
        $app->render('register', array());
    });

    $app->post('/register', function () use ($app) {
        $app->access('onlyGuest');
        $people = new \app\model\People();
        if (isset($_POST['email']))
            $people->setEmail($_POST['email']);
        if (isset($_POST['pseudo']))
            $people->setPseudo($_POST['pseudo']);
        if (isset($_POST['password']))
            $people->setPassword($_POST['password']);
        $error = $people->register();
        if (!isset($error['password']) && $_POST['password'] != $_POST['password2'])
            $error['password'] = 'Les mots de passes ne sont pas semblable';
        if (empty($error)){
            mail($people->getEmail(), 'Confirmation d\'inscription', 'Bonjour '.$people->getPseudo().',<br><br> Pour confirmer ton inscription, clique sur ce lien : <a href="https://xxxx/register?key='.$people->getTokenValidated().'">https://xxx/register?key='.$people->getTokenValidated().'</a><br><br>A bient&ocirc;t :)');
            $app->render('register_done', array('email' => $people->getEmail()));
        }
        else
            $app->render('register', array('post' => $_POST, 'error' => $error));
    });
