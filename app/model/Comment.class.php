<?php

    namespace app\model;

    class Comment
    {
        private $id;
        private $people;
        private $selfie;
        private $message;

        public function __construct(){

        }

        public function send()
        {
            $selfie = \app\ORM::getInstance()->findOne('selfie', array('id' => $this->selfie));
            if ($selfie instanceof Selfie) {
                $this->selfie = $selfie->getId();
                \app\ORM::getInstance()->store('comment', get_object_vars($this));
            }
            return (0);
        }

        public function checkMessage()
        {
            $this->message = trim($this->message);
            $this->message = htmlentities($this->message);
            if (strlen($this->message) <= 0 || strlen($this->message) > 1000) {
                return 'Ton message doit contenir 1 à 1000 caractères';
            }
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

        public function getPeople()
        {
            return $this->people;
        }

        public function setPeople($people)
        {
            $this->people = $people;
        }

        public function getSelfie()
        {
            return $this->selfie;
        }

        public function setSelfie($selfie)
        {
            $this->selfie = $selfie;
        }

        public function getMessage()
        {
            return $this->message;
        }

        public function setMessage($message)
        {
            $this->message = $message;
        }
    }