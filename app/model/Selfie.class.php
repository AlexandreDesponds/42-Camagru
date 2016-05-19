<?php
    namespace app\model;

    class Selfie
    {
        private $id;
        private $name;
        private $dateCreated;
        private $ipCreated;
        private $people;
        private $visible;

        public static function add($People)
        {
            $Selfie = new Selfie();
            $Selfie->setName($Selfie->generateName());
            $Selfie->setIpCreated($_SERVER['REMOTE_ADDR']);
            $Selfie->setDateCreated(date("Y-m-j H:i:s"));
            $Selfie->setPeople($People->getId());
            $Selfie->setVisible(1);
            $Selfie->setId(\app\ORM::getInstance()->store('selfie', get_object_vars($Selfie)));
            if ($Selfie->getId() > 0)
                return ($Selfie);
            return (false);
        }

        public static function remove($name, $people)
        {
            $Selfie = \app\ORM::getInstance()->findOne('selfie', array('name' => $name, 'people' => $people));
            if ($Selfie instanceof Selfie) {
                $Selfie->setVisible(0);
                return \app\ORM::getInstance()->store('selfie', get_object_vars($Selfie));
            }
            return false;
        }

        private function generateName()
        {
            $key = "";
            $chaine = "abcdefghijklmnpqrstuvwxyABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
            srand((double)microtime() * 1000000);
            for ($i = 0; $i < 50; $i++) {
                $key .= $chaine[rand() % strlen($chaine)];
            }
            return $key;
        }

        public function getId()
        {
            return $this->id;
        }

        public function setId($id)
        {
            $this->id = $id;
        }

        public function getName()
        {
            return $this->name;
        }

        public function setName($name)
        {
            $this->name = $name;
        }

        public function getDateCreated()
        {
            return $this->dateCreated;
        }

        public function setDateCreated($dateCreated)
        {
            $this->dateCreated = $dateCreated;
        }

        public function getIpCreated()
        {
            return $this->ipCreated;
        }

        public function setIpCreated($ipCreated)
        {
            $this->ipCreated = $ipCreated;
        }

        public function getPeople()
        {
            return $this->people;
        }

        public function setPeople($people)
        {
            $this->people = $people;
        }

        public function getVisible()
        {
            return $this->visible;
        }

        public function setVisible($visible)
        {
            $this->visible = $visible;
        }
    }