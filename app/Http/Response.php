<?php
    namespace app\Http;

    class Response{

        /*
        * Código de STATUS HTTP
        * @var integer
        */
        private $httpCode = 200;

        /*
        * Cabeçalho do Response
        * @var array
        */
        private $headers = [];

        /*
        * Tipo de conteúdo que está sendo retornado
        * @var string
        */
        private $contentType = [];

        /*
        * Conteúdo do Response
        * @var mixed
        */
        private $content;

        /*
        * Classe contrutora
        */
        public function __construct($httpCode, $content, $contentType = 'text/html') {
            $this->httpCode = $httpCode;
            $this->content = $content;
            $this->setContentType($contentType);
        }

        /*
        * Método responsável por alterar o content type do response
        * @param string
        */
        public function setContentType($contentType){
            $this->contentType = $contentType;
            $this->addHeader('Content-Type', $contentType);
        }

        /*
        * Método responsável por adicionar um registro no cabeçalho de response
        * @params string
        */
        public function addHeader($key, $value){
            $this->headers[$key] = $value;
        }

        /*
        * Método responsável por enviar os headers para o navegador
        */
        private function sendHeaders(){
            //STATUS
            http_response_code($this->httpCode);

            //ENVIAR HEADERS
            foreach($this->headers as $key=>$value){
                header($key.':'.$value);
            }
        }

        /*
        * Método responsável por enviar a resposta para o usuário
        *
        */
        public function sendResponse(){
            //ENVIA OS HEADERS
            $this->sendHeaders();
            //IMPRIMI CONTEUDO
            switch($this->contentType){
                case 'text/html':
                    echo $this->content;
                    exit;
            }
        }
    }
?>