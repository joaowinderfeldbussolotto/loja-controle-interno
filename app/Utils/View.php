<?php

    namespace app\Utils;

    class View
    {

        /**
         * Variáveis padrões da view
         * @var array
         * */
        private static $vars = array();

        /**
         * Método responsável por definir os dados iniciais da classe
         * @param array $vars
         * */
        public static function init($vars = []){
            self::$vars = $vars;
        }

        private static function getContent($view){
            $file = ROOT.'/resources/view/'.$view.'.html';
            return file_exists($file)? file_get_contents($file) : '';
        }

        public static function render($view, $vars = []){
            // CONTEUDO DA VIEW
            $contentView = self::getContent($view);

            // MERGE DE VARIÁVEIS DA VIEW;
            $vars = array_merge(self::$vars, $vars);

            // CHAVES DO ARRAY DE VARIÁVEIS
            $keys = array_keys($vars);
            $keys = array_map(function($item){
                return '{{'.$item.'}}';
            }, $keys);
            return str_replace($keys, array_values($vars), $contentView);
        }
    }

?>
