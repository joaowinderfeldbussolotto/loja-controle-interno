<?php
    namespace app\Http;
    
    class Request {
        /*
        * Método http
        * @var string
        */
        private $httpMethod;

        /*
        * URI da requisição
        * @var array
        */
        private $uri;
        private $router;

        /*
        * Parâmetros da URL ($_GET)
        * @var array
        */
        private $queryParams = [];

        /*
        * Variáveis recebidas no POST da página ($_POST)
        * @var array
        */
        private $postVars = [];

        /*
        * Cabeçalho da requisição
        * @var array
        */
        private $headers = [];

        /*
        * Construtor da classe
        */
        public function __construct($router){
            $this->router= $router;
            $this->queryParams  =    $_GET ?? [];
            $this->postVars     =    $_POST ?? [];
            $this->headers      =    getallheaders();
            $this->httpMethod  =    $_SERVER['REQUEST_METHOD'] ?? '';
            $this->setUri();

        }

        

        private function setUri(){
            $this->uri = $_SERVER['REQUEST_URI'] ?? '';
            //REMOVE GETS
            $xURI = explode('?', $this->uri);
            $this->uri = $xURI[0];

        }   

        /*
        * Método responsavel por retornar o método HTTP
        * @return string
        */
        public function getHttpMethod(){
            return $this->httpMethod;
        }

        /*
        * Método responsavel por retornar a URI da requisição
        * @return string
        */
        public function getUri(){
            return $this->uri;
        }

        public function getRouter(){
            return $this->router;
        }

        /*
        * Método responsavel por retornar o cabeçalho da requisição
        * @return array
        */
        public function getHeaders(){
            return $this->headers;
        }

        /*
        * Método responsavel por retornar os parâmetros da URL da requisição
        * @return array
        */
        public function getQueryParams(){
            return $this->queryParams;
        }

        /*
        * Método responsavel por retornar as variaveis POST da requisição
        * @return array
        */
        public function getPostVars(){
            return $this->postVars;
        }
    }
?>