<?php

    $app->get('/selfie', function () use ($app) {
        $app->access('onlyMember');
        $icons = scandir("img/icone/");
        unset($icons[0], $icons[1]);
        $selfie = \app\ORM::getInstance()->findAll('selfie', array('people' => unserialize($_SESSION['people'])->getId(), 'visible' => 1), array('dateCreated', 'DESC'), array(0, 10));
        $app->render('/selfie', array('icons' => $icons, 'selfies' => $selfie, 'session' => $_SESSION));
    });

    $app->post('/selfie', function () use ($app) {
        $app->access('onlyMember');
        if (isset($_POST['img']))
        {
            if (isset($_POST['img-0-name'])) {
                $Selfie = \app\model\Selfie::add(unserialize($_SESSION['people']));
                if ($Selfie instanceof \app\model\Selfie) {
                    try{
                        new \app\model\Image($_POST['img'], $_POST, $Selfie->getName());
                    } catch (Exception $e) {
                        $app->error("Mauvais type de fichier");
                        $app->redirect('/selfie');
                    }
                    $app->success("Ton image a été sauvgardée");
                    $app->redirect('selfie');
                }
            }
            $app->error("Vous devez avoir un filtre au minimum");
            $app->redirect('/selfie');
        }
        $app->error("une erreur est survenue");
        $app->redirect('/selfie');
    });

    $app->get('/selfie/delete', function () use ($app) {
        $app->access('onlyMember');
        if (isset($_GET['id'])){
            $error = \app\model\Selfie::remove($_GET['id'], unserialize($_SESSION['people'])->getId());
            if ($error){
                $app->success("Ton image a été supprimée");
                $app->redirect('/selfie');
            }
        }
        $app->error("une erreur est survenue");
        $app->redirect('/selfie');
    });

    $app->get('/selfie/like', function () use ($app) {
        $app->access('onlyMember');
        if (isset($_GET['id'])){
            echo $_GET['id'];
            $like = new \app\model\Likes();
            $like->setPeople(unserialize($_SESSION['people'])->getId());
            $like->setSelfie($_GET['id']);
            $ret = $like->like();
            if ($ret == 1){
                $app->success("Tu as liké le Selfie");
            } elseif ($ret == 2){
                $app->success("Tu as unliké le Selfie");
            }
            if (isset($_GET['callback']) && !empty($_GET['callback']))
                $app->redirect('/'.$_GET['callback']);
            else
                $app->redirect('/selfie/show?id='.$_GET['id']);
        }
        $app->error("une erreur est survenue");
        $app->redirect('/selfie');
    });

    $app->get('/selfie/show', function () use ($app) {
        if (isset($_GET['id'])) {
            $selfie = \app\ORM::getInstance()->findOne('selfie', array('name' => $_GET['id'], 'visible' => 1));
            if ($selfie instanceof \app\model\Selfie) {
                $people = \app\ORM::getInstance()->findOne('people', array('id' => $selfie->getPeople()));
                $isLike = \app\ORM::getInstance()->findOne('likes', array('people' => $people->getId(), 'selfie' => $selfie->getId())) instanceof \app\model\Likes ? true : false;
                $countLike = \app\ORM::getInstance()->count('likes', array('selfie' => $selfie->getId()));
                if ($people instanceof \app\model\People) {
                    $comments = \app\ORM::getInstance()->findAll('comment', array('selfie' => $selfie->getId()), array('id', 'DESC'), array(0, 10));
                    foreach($comments as $k => $v){
                        $author = \app\ORM::getInstance()->findOne('people', array('id' => $v['people']));
                        $avatar = file_get_contents('http://avatar.teub.es/api?size=100&q='.$author->getPseudo());
                        $avatar = json_decode($avatar);
                        $comments[$k]['display'] = "<div class='avatar'><img src='".$avatar->base64."'></div>
                        <h4>".$author->getPseudo()."</h4><div class='message'>".$v['message']."</div>";
                    }
                    $app->render('show', array('pseudo' => $people->getPseudo(), 'name' => $selfie->getName(), 'comments' => $comments, 'isLike' => $isLike, 'countLike' => $countLike));
                }
            }
        } else {
            $app->error("une erreur est survenue");
            $app->redirect('/browse');
        }
    });

    $app->post('/selfie/show', function () use ($app) {
        $app->access('onlyMember');
        if (isset($_GET['id']) && isset($_POST['message'])) {
            $selfie = \app\ORM::getInstance()->findOne('selfie', array('name' => $_GET['id'], 'visible' => 1));
            if ($selfie instanceof \app\model\Selfie) {
                $comment = new \app\model\Comment();
                $comment->setMessage($_POST['message']);
                $error = $comment->checkMessage();
                if ($error) {
                    $app->error($error);
                    $app->redirect('/selfie/show?id='.$_GET['id']);
                } else {
                    $comment->setSelfie($selfie->getId());
                    $comment->setPeople(unserialize($_SESSION['people'])->getId());
                    $comment->send();
                    $author = \app\ORM::getInstance()->findOne('people', array('id' => $comment->getPeople()));
                    $people = \app\ORM::getInstance()->findOne('people', array('id' => $selfie->getPeople()));
                    mail($people->getEmail(), 'Un nouveau commentaire', 'Bonjour '.$author->getPseudo().',<br><br>'.$author->getPseudo().' a laiss&eacute; un commentaire sur un de tes selfies ! <a href="https://xxxx/selfie/show?id='.$selfie->getName().'">Voir le commentaire</a><br><br>A bient&ocirc;t :)');
                    $app->success("Ton commentaire a bien été posté");
                    $app->redirect('/selfie/show?id='.$_GET['id']);
                }
            }
        } else {
            $app->error("une erreur est survenue");
            $app->redirect('/selfie/show?id='.$_GET['id']);
        }
    });
