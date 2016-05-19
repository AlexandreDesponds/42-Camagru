<?php

    namespace app;

    Class Ponps
    {
        private $listRoute = array();
        private $route;
        private $method;
        private $prefix;
        private $html;

        public function __construct()
        {
            if (isset($_SESSION['error_i'])) {
                if ($_SESSION['error_i'] == 0)
                    unset ($_SESSION['error']);
                if ($_SESSION['error_i'] == 1)
                    $_SESSION['error_i'] = 0;
            }
            if (isset($_SESSION['success_i'])) {
                if ($_SESSION['success_i'] == 0)
                    unset ($_SESSION['success']);
                if ($_SESSION['success_i'] == 1)
                    $_SESSION['success_i'] = 0;
            }
        }

        public function run()
        {
            if (strpos($this->route, '?') > 0)
                $this->route = substr($this->route, 0, strpos($this->route, '?'));
            $this->findRoute();
        }

        private function findRoute()
        {
            foreach ($this->listRoute as $v) {
                if ($this->prefix . '' . $v->getUrl() == $this->route && $this->method == $v->getMethod()) {
                    $v->execFunction();
                    return;
                }
            }
            $this->error404();
        }

        public function error($msg)
        {
            $_SESSION['error'] = $msg;
            $_SESSION['error_i'] = 1;
        }

        public function success($msg)
        {
            $_SESSION['success'] = $msg;
            $_SESSION['success_i'] = 1;
        }

        public function access($access)
        {
            if ($access == 'onlyMember' && !(unserialize($_SESSION['people']) instanceof \app\model\People)) {
                $this->error('Cette page est réservée aux membres');
                $this->redirect('/login');
            }
            if ($access == 'onlyGuest' && isset($_SESSION['people']) && (unserialize($_SESSION['people']) instanceof \app\model\People)) {
                $this->error('Vous êtes déjà connecté');
                $this->redirect('/');
            }
        }

        public function render($view, $array)
        {
            $array['session'] = $_SESSION;
            new Template($array, $view);
        }

        private function error404()
        {
            $this->error('tu t\'es perdu ?');
            $this->redirect('/');
            die;
        }

        public function redirect($url)
        {
            header('Location: ' . $url);
            exit;
        }

        public function get($url, $function)
        {
            $this->addRoute($url, 'GET', $function);
        }

        public function post($url, $function)
        {
            $this->addRoute($url, 'POST', $function);
        }

        private function addRoute($url, $method, $function)
        {
            $r = new Route($url, $method, $function);
            $this->listRoute[] = $r;
        }

        public function setPrefix($prefix)
        {
            $this->prefix = $prefix;
        }

        public function setRoute($route)
        {
            $this->route = $route;
        }

        public function setMethod($method)
        {
            $this->method = $method;
        }
    }