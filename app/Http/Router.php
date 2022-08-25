<?php

namespace app\Http;

use \Closure;
use \Exception;
use \ReflectionFunction;
use \App\Http\Middleware\Queue as MiddlewareQueue;

class Router
{
   /*
        * URL completa do projeto (raiz)
        * @var string
        */
   private $url = '';

   /*
        * Prefixo de todas as rotas
        * @var string
        */
   private $prefix = '';

   /*
        * Indice de rotas
        * @var array
        */
   private $routes = [];

   /*
        * Instancia de Request
        * @var Request
        */
   private $request;

   /*
        * Método construtor da classe
        */
   public function __construct($url)
   {
      $this->request = new Request($this);
      $this->url = $url;
      $this->setPrefix();
   }

   /*
        * Método responsável por difinir o prefixo das rotas
        */
   private function setPrefix()
   {
      // Informaçoes da url atual
      $parseUrl = parse_url($this->url);

      // Define o prefixo
      $this->prefix = $parseUrl['path'] ?? '';
   }

   /*
        * Método responsável por adicionar uma rota na classe
        */
   private function addRoute($method, $route, $params = [])
   {
      // Validação dos parâmetros
      foreach ($params as $key => $value) {
         if ($value instanceof Closure) {
            $params['Controller'] = $value;
            unset($params[$key]);
            continue;
         }
      }


      $params['middlewares']  = $params['middlewares'] ?? [];

      // VARIÁVEIS DA ROTA
      $params['variables'] = [];

      // PADRÃO DE VALIDAÇÃO DAS VARIÁVEIS DAS ROTAS
      $patternVariable = '/{(.*?)}/';
      if (preg_match_all($patternVariable, $route, $matches)) {
         $route = preg_replace($patternVariable, '(.*?)', $route);
         $params['variables'] = $matches[1];
      }

      //padrão de validação da url
      $patternRoute = '/^' . str_replace('/', '\/', $route) . '$/';

      //adiciona a rota a nossa classe
      $this->routes[$patternRoute][$method] = $params;
   }

   /*
        * Método responsavel por definir uma rota de GET
        */
   public function get($route, $params = [])
   {
      return $this->addRoute('GET', $route, $params);
   }

   /*
        * Método responsavel por definir uma rota de POST
        */
   public function post($route, $params = [])
   {
      return $this->addRoute('POST', $route, $params);
   }

   /*
        * Método responsavel por definir uma rota de PUT
        */
   public function put($route, $params = [])
   {
      return $this->addRoute('PUT', $route, $params);
   }

   /*
        * Método responsavel por definir uma rota de DELETE
        */
   public function delete($route, $params = [])
   {
      return $this->addRoute('DELETE', $route, $params);
   }

   /*
        * Método responsável por retornar a URI desconsiderando o prefixo
        * @return string
        */
   private function getUri()
   {
      // URI DA REQUEST
      $uri = $this->request->getUri();


      // fatiar a uri com o prefixo
      $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];

      return end($xUri);
   }


   // Retorna os dados da rota atual
   private function getRoute()
   {
      // URI
      $uri = $this->getUri();

      //Method
      $httpMethod = $this->request->getHttpMethod();

      //Valida AS ROTAS
      foreach ($this->routes as $patternRoute => $methods) {
         if (preg_match($patternRoute, $uri, $matches)) {
            //VERIFICA O METODO
            if (isset($methods[$httpMethod])) {
               // Remove a primeira opção
               unset($matches[0]);

               // Variaveis processadas
               $keys = $methods[$httpMethod]['variables'];
               $methods[$httpMethod]['variables'] = array_combine($keys, $matches);
               $methods[$httpMethod]['variables']['request'] = $this->request;

               //Retorno dos parâmetros da rota
               return $methods[$httpMethod];
            }
            // METODO NÃO PERMITIDO DEFINIDO
            throw new Exception("Método não é permitido", 405);
         }
      }
      //URL NÃO ENCONTRADA
      throw new Exception("<h1>URL não encontrada</h1> <a href=" . URL . ">Volte ao menu</a>", 404);
      header('location:' . URL);
   }

   /*
        * Método responsável por executar a rota atual
        */
   public function run()
   {
      try {
         //obtém a rota atual
         $route = $this->getRoute();

         // Verfica o controlador
         if (!isset($route['Controller'])) {
            throw new Exception("A URL não pode ser processada", 500);
         }

         // Argumentos da função
         $args = [];

         // Reflection
         $reflection = new ReflectionFunction($route['Controller']);
         foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();
            $args[$name] = $route['variables'][$name] ?? '';
         }
         // Retorna a execução da fila de middlewares
         return (new MiddlewareQueue($route['middlewares'], $route['Controller'], $args))->next($this->request);
      } catch (Exception $e) {
         return new Response($e->getCode(), $e->getMessage());
      }
   }

   public function getCurrentUrl()
   {
      return $this->url . $this->getUri();
   }

   public function redirect($route)
   {
      $URL = $this->url . $route;
      header('location:' . $URL);
      exit;
   }
}
