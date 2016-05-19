<?php
    namespace app\model;

    class Image
    {
        private $_image;
        private $_filters;

        public function __construct($base64, $filters, $name){
            $this->base64ToImg($base64);
            $this->setFilter($filters);
            if (!empty($this->_filters)) {
                foreach ($this->_filters as $filter) {
                    $this->merge($filter);
                }
            }
            $this->save($name);
        }

        private function save($name){
            imagepng($this->_image, "img/selfie/".$name.".png", 0);
        }

        private function merge($filter){
            $tmp = imagecreatefrompng($this->urlToFile($filter['name']));
            imagecopyresized(
                $this->_image,
                $tmp,
                substr($filter['y'], 0, -2),
                substr($filter['x'], 0, -2),
                0,
                0,
                $filter['width'],
                $filter['height'],
                $this->getWidth($tmp),
                $this->getHeight($tmp)
            );
        }

        private function urlToFile($url){
            $tmp = explode('/', $url);
            return 'img/icone/'.end($tmp);
        }

        private function base64ToImg($base64){
            list($type, $base64) = explode(';', $base64);
            list(, $base64)      = explode(',', $base64);
            $this->_image = @imagecreatefromstring(base64_decode($base64));
            if ($this->_image === false){
                throw(new \Exception('bad file'));
            }
        }

        private function setFilter($filter){
            unset($filter['img']);
            foreach($filter as $k => $v){
                $tmp = explode('-', $k);
                $this->_filters[$tmp[1]][$tmp[2]] = $v;
            }
        }

        private function checkImg($image){
            print_r($image);
        }

        private function getWidth($image){
            return(imagesx($image));
        }

        private function getHeight($image){
            return(imagesy($image));
        }
    }