<?php

    namespace app;

    Class Route
    {
        private $url;
        private $method;
        private $function;

        public function __construct($url, $method, $function)
        {
            $this->url = $url;
            $this->method = $method;
            $this->function = $function;
        }

        public function execFunction()
        {
            call_user_func($this->function);
        }

        public function getUrl()
        {
            return $this->url;
        }

        public function getMethod()
        {
            return $this->method;
        }

        public function getFunction()
        {
            return $this->function;
        }


    }
