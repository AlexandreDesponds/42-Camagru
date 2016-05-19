<?php

    namespace app\model;

    class People
    {
        private $id;
        private $pseudo;
        private $password;
        private $email;
        private $ipCreated;
        private $ipUpdated;
        private $dateCreated;
        private $dateUpdated;
        private $tokenValidated;
        private $tokenLost;

        public function __construct()
        {

        }

        static public function login($pseudo, $password)
        {
            $people = \app\ORM::getInstance()->findOne('people', array('pseudo' => $pseudo, 'password' => People::encrypt_password($password, $pseudo)));
            if ($people instanceof People) {
                if (empty($people->getTokenValidated()))
                    return $people;
                else
                    return 1;
            }
            return (NULL);
        }

        public function register()
        {
            $error['pseudo'] = $this->checkPseudo();
            $error['password'] = $this->checkPassword();
            $error['email'] = $this->checkEmail();
            foreach ($error as $e) {
                if (!empty($e))
                    return ($error);
            }
            $this->password = People::encrypt_password($this->password, $this->pseudo);
            $this->ipCreated = $_SERVER['REMOTE_ADDR'];
            $this->ipUpdated = $_SERVER['REMOTE_ADDR'];
            $this->dateCreated = date("Y-m-j H:i:s");
            $this->dateUpdated = date("Y-m-j H:i:s");
            $this->tokenValidated = $this->generateKey();
            $this->id = \app\ORM::getInstance()->store('people', get_object_vars($this));
        }

        static public function validateEmail($key)
        {
            $people = \app\ORM::getInstance()->findOne('people', array('tokenValidated' => $key));
            if ($people instanceof People)
            {
                $people->setTokenValidated(null);
                \app\ORM::getInstance()->store('people', get_object_vars($people));
                return (true);
            }
            return (false);
        }

        public function changePassword(){
            $key = "";
            $chaine = "abcdefghijklmnpqrstuvwxyABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
            srand((double)microtime() * 1000000);
            for ($i = 0; $i < 8; $i++) {
                $key .= $chaine[rand() % strlen($chaine)];
            }
            $this->password = People::encrypt_password($key, $this->pseudo);
            $this->id = \app\ORM::getInstance()->store('people', get_object_vars($this));
            return $key;
        }


        static private function encrypt_password($password, $pseudo)
        {
            return sha1("f_t#super0b" . $password . $pseudo);
        }

        private function generateKey()
        {
            $key = "";
            $chaine = "abcdefghijklmnpqrstuvwxyABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
            srand((double)microtime() * 1000000);
            for ($i = 0; $i < 50; $i++) {
                $key .= $chaine[rand() % strlen($chaine)];
            }
            return $key . md5($this->email);
        }

        private function checkPseudo()
        {
            if (\app\ORM::getInstance()->findOne('people', array('pseudo' => $this->pseudo)) instanceof People)
                return 'Dommage, le pseudo existe déjà';
            if (!preg_match('/^([a-zA-Z0-9-_.]){3,20}$/', $this->pseudo))
                return 'Ton pseudo doit contenir 3 à 20 caractères alphanumériques';
            return;
        }

        private function checkPassword()
        {
            if (strlen($this->password) < 6 || strlen($this->password) > 40)
                return 'Ton mot de passe doit contenir 6 à 40 caractères';
            return;
        }

        private function checkEmail()
        {
            if (\app\ORM::getInstance()->findOne('people', array('email' => $this->email)) instanceof People)
                return 'Un compte est déjà lié avec cette adresse email, tente de retouver ton mot de passe';
            if (!filter_var($this->email, FILTER_VALIDATE_EMAIL))
                return 'l\'email n\'est pas valide';
            return;
        }

        public function getId()
        {
            return $this->id;
        }

        public function setId($id)
        {
            $this->id = $id;
        }

        public function getPseudo()
        {
            return $this->pseudo;
        }

        public function setPseudo($pseudo)
        {
            $this->pseudo = $pseudo;
        }

        public function setPassword($password)
        {
            $this->password = $password;
        }

        public function getEmail()
        {
            return $this->email;
        }

        public function setEmail($email)
        {
            $this->email = $email;
        }

        public function getIpCreated()
        {
            return $this->ipCreated;
        }

        public function setIpCreated($ipCreated)
        {
            $this->ipCreated = $ipCreated;
        }

        public function getIpUpdated()
        {
            return $this->ipUpdated;
        }

        public function setIpUpdated($ipUpdated)
        {
            $this->ipUpdated = $ipUpdated;
        }

        public function getDateCreated()
        {
            return $this->dateCreated;
        }

        public function setDateCreated($dateCreated)
        {
            $this->dateCreated = $dateCreated;
        }

        public function getDateUpdated()
        {
            return $this->dateUpdated;
        }

        public function setDateUpdated($dateUpdated)
        {
            $this->dateUpdated = $dateUpdated;
        }

        public function getTokenValidated()
        {
            return $this->tokenValidated;
        }

        public function setTokenValidated($tokenValidated)
        {
            $this->tokenValidated = $tokenValidated;
        }

        public function getTokenLost()
        {
            return $this->tokenLost;
        }

        public function setTokenLost($tokenLost)
        {
            $this->tokenLost = $tokenLost;
        }
    }