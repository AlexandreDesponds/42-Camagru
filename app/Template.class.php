<?php

    namespace app;

    Class Template
    {
        private $html;
        private $final;
        private $array;
        private $view;

        public function __construct($array, $view)
        {
            $this->array = $array;
            $this->view = $view;
            $this->final = '';
            $this->html = file_get_contents("app/view/" . $this->view . '.html');
            $this->addTemplate();
            $this->merge();
            $this->addIf();
            $this->addIfn();
            $this->addFor();
            $this->addValue();
            echo $this->final;
        }

        private function merge()
        {
            preg_match_all('/{#(.*?)}(.*?){#END}/s', $this->html, $matchesHtml);
            preg_match_all('/{#(.*?)}/s', $this->final, $matchesFinal);
            foreach ($matchesHtml[1] as $k => $v)
                $this->final = preg_replace("/{#" . $v . "}/", $matchesHtml[2][$k], $this->final);
        }

        private function incrementIf($matches)
        {
            global $i;
            return '{%IF ' . $i++ . ' ';
        }

        private function incrementIfn($matches)
        {
            global $i;
            return '{%IFN ' . $i++ . ' ';
        }

        private function incrementFor($matches)
        {
            global $i;
            return '{*FOR ' . $i++ . ' ';
        }

        private function addIf()
        {
            $i = 0;
            $this->final = preg_replace_callback('/{\%IF /s', '\app\Template::incrementIf', $this->final);
            preg_match_all('/{\%IF (.*?) (.*?)}(.*?){\%END}/s', $this->final, $matchesIf);
            foreach ($matchesIf[2] as $k => $v) {
                $v_exp = explode(".", $v);
                $var = $this->array;
                foreach ($v_exp as $v2) {
                    if (isset($var[$v2]))
                        $var = $var[$v2];
                    else
                        $var = NULL;
                }
                if ($var == NULL)
                    $this->final = preg_replace('/{\%IF ' . $matchesIf[1][$k] . ' ' . $v . '}(.*?){\%END}/s', '', $this->final);
                else
                    $this->final = preg_replace('/{\%IF ' . $matchesIf[1][$k] . ' ' . $v . '}(.*?){\%END}/s', $matchesIf[3][$k], $this->final);
            }
        }

        private function addIfn()
        {
            $i = 0;
            $this->final = preg_replace_callback('/{\%IFN /s', '\app\Template::incrementIfn', $this->final);
            preg_match_all('/{\%IFN (.*?) (.*?)}(.*?){\%END}/s', $this->final, $matchesIf);
            foreach ($matchesIf[2] as $k => $v) {
                $v_exp = explode(".", $v);
                $var = $this->array;
                foreach ($v_exp as $v2) {
                    if (isset($var[$v2]))
                        $var = $var[$v2];
                    else
                        $var = NULL;
                }
                if ($var != NULL)
                    $this->final = preg_replace('/{\%IFN ' . $matchesIf[1][$k] . ' ' . $v . '}(.*?){\%END}/s', '', $this->final);
                else
                    $this->final = preg_replace('/{\%IFN ' . $matchesIf[1][$k] . ' ' . $v . '}(.*?){\%END}/s', $matchesIf[3][$k], $this->final);
            }
        }

        private function addFor()
        {
            $this->final = preg_replace_callback('/{\*FOR /s', '\app\Template::incrementFor', $this->final);
            preg_match_all('/{\*FOR (.*?) (.*?) AS (.*?)}(.*?){\*END}/s', $this->final, $matchesFor);
            foreach ($matchesFor[2] as $k => $v) {
                $htmlFor = "";
                $v_exp = explode(".", $v);
                $var = $this->array;
                foreach ($v_exp as $v2) {
                    if (isset($var[$v2]))
                        $var = $var[$v2];
                    else
                        $var = NULL;
                }
                if ($var !== NULL) {
                    foreach ($var as $k3 => $v3) {
                        if (is_array($v3)) {
                            preg_match('/{{' . $matchesFor[3][$k]. '.(.*?)}}/', $matchesFor[4][$k], $option);
                            $htmlFor .= preg_replace('/{{' . $matchesFor[3][$k]. '.(.*?)}}/', $var[$k3][$option[1]], $matchesFor[4][$k]);
                        } else {
                            $htmlFor .= preg_replace('/{{' . $matchesFor[3][$k] . '}}/', $v3, $matchesFor[4][$k]);
                        }
                    }
                    $this->final = preg_replace('/{\*FOR ' . $matchesFor[1][$k] . ' (.*?) AS (.*?)}(.*?){\*END}/s', $htmlFor, $this->final);
                }
            }
        }

        private function addValue()
        {
            preg_match_all('/{{(.*?)}}/s', $this->final, $matches);
            foreach ($matches[1] as $k => $v) {
                $v_exp = explode(".", $v);
                $var = $this->array;
                foreach ($v_exp as $v2) {
                    if (isset($var[$v2]))
                        $var = $var[$v2];
                    else
                        $var = NULL;
                }
                if ($var !== NULL)
                    $this->final = preg_replace('/{{' . $matches[1][$k] . '}}/', $var, $this->final);
            }
        }

        private function addTemplate()
        {
            preg_match_all('/{TEMPLATE}(.*?){END}/', $this->html, $matches);
            foreach ($matches[1] as $v)
                $this->final .= file_get_contents("app/view/" . $v . '.html');
        }

        public function getHtml()
        {
            return $this->html;
        }
    }