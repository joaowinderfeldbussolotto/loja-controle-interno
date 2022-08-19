<?php

namespace App\Http\Middleware;

class Queue{
    private static $map = [];
    private $middlewares = [];
    private $controller;
    private $controllerArgs = [];
    private static $default = [];
    

    public function __construct($middlewares, $controller, $controllerArgs){    
        $this->middlewares = array_merge(self::$default, $middlewares);
        $this->controller = $controller;
        $this->controllerArgs = $controllerArgs;
    }

    public function next($request){

        if(empty($this->middlewares)) return call_user_func_array ($this->controller, $this->controllerArgs);

        //MIDDLEWARE
        $middleware = array_shift($this->middlewares); //chama em ordem e remove;

        if(!isset(self::$map[$middleware])){
            throw new \Exception("Problemas ao processar middleware da requisição", 500);
        }

        //NEXT
        $queue = $this;
        $next = function($request) use ($queue){
            return $queue->next($request);
        };


        return (new self::$map[$middleware]) ->handle($request, $next);
    }

    public static function setMap($map){

        self::$map = $map;
    }

    
    public static function setDefault($default){

        self::$default = $default;
    }
}