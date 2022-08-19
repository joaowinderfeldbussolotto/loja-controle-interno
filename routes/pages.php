<?php
    use \app\Http\Response;
    use \app\Controller\Pages;

    // Rota Home
    $obRouter-> get('/',[
        'middlewares' => ['required-admin-login'],

        
        function(){
            return new Response(200, Pages\Home::getHome());
        }
    ]); 

    // Rota About
   

    // Rota DINÂMICA
   $obRouter-> get('/clientes', [
    'middlewares' => ['required-admin-login'],

    function($request){
        return new Response(200, Pages\Costumer::getCostumers($request));
    }
]);

$obRouter-> get('/cadastrarCliente', [
    'middlewares' => ['required-admin-login'],

    function($request){
        return new Response(200, Pages\Costumer::addCostumer($request));
    }
]);

$obRouter-> post('/cadastrarCliente', [
    'middlewares' => ['required-admin-login'],

    function($request){
        return new Response(200, Pages\Costumer::insertCostumer($request));
    }
]);

$obRouter-> get('/login', [
    'middlewares' => ['required-admin-logout'],
    function($request){
        return new Response(200, Pages\Login::getLogin($request));
    }
]);
$obRouter-> post('/login', [
    function($request){
        //echo password_hash('123', PASSWORD_DEFAULT); exit;
        return new Response(200, Pages\Login::setLogin($request));
    }
]);

$obRouter-> get('/logout', [
    'middlewares' => ['required-admin-login'],

    function($request){
        return new Response(200, Pages\Login::setLogout($request));
    }
]);

   $obRouter-> post('/depoimentos', [
    'middlewares' => ['required-admin-login'],

    function($request){
        
        return new Response(200, Pages\Testimony::insertTestimony($request));
    }
]);

?>
