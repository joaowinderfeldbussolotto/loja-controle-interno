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

$obRouter-> post('/clientes', [
    'middlewares' => ['required-admin-login'],

    function($request){
        return new Response(200, Pages\Costumer::searchCostumers($request));
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

$obRouter-> get('/cliente/{id}/edit', [
    'middlewares' => ['required-admin-login'],

    function($request, $id){
        return new Response(200, Pages\Costumer::getEditCostumer($request, $id));
    }
]);

$obRouter-> post('/cliente/{id}/edit', [
    'middlewares' => ['required-admin-login'],

    function($request, $id){
        return new Response(200, Pages\Costumer::setEditCostumer($request, $id));
    }
]);

$obRouter-> get('/cliente/{id}/delete', [
    'middlewares' => ['required-admin-login'],

    function($request, $id){
        return new Response(200, Pages\Costumer::getDeleteCostumer($request, $id));
    }
]);



   // Rota DINÂMICA
   $obRouter-> get('/produtos', [
    'middlewares' => ['required-admin-login'],

    function($request){
        return new Response(200, Pages\Product::getProducts($request));
    }
]);

$obRouter-> post('/produtos', [
    'middlewares' => ['required-admin-login'],

    function($request){
        return new Response(200, Pages\Product::searchProducts($request));
    }
]);


$obRouter-> get('/cadastrarProduto', [
    'middlewares' => ['required-admin-login'],

    function($request){
        return new Response(200, Pages\Product::addProduct($request));
    }
]);

$obRouter-> post('/cadastrarProduto', [
    'middlewares' => ['required-admin-login'],

    function($request){
        return new Response(200, Pages\Product::insertProduct($request));
    }
]);

$obRouter-> get('/produto/{id}/edit', [
    'middlewares' => ['required-admin-login'],

    function($request, $id){
        return new Response(200, Pages\Product::getEditProduct($request, $id));
    }
]);

$obRouter-> post('/produto/{id}/edit', [
    'middlewares' => ['required-admin-login'],

    function($request, $id){
        return new Response(200, Pages\Product::setEditProduct($request, $id));
    }
]);

$obRouter-> get('/produto/{id}/delete', [
    'middlewares' => ['required-admin-login'],

    function($request, $id){
        return new Response(200, Pages\Product::getDeleteProduct($request, $id));
    }
]);


$obRouter->get('/cadastrarVenda', [
    'middlewares' => ['required-admin-login'],

    function($request){
        return new Response(200, Pages\Sale::addSale($request));
    }
]);

$obRouter->post('/cadastrarVenda', [
    'middlewares' => ['required-admin-login'],

    function($request){
        return new Response(200, Pages\Sale::insertSale($request));
    }
]);


$obRouter-> get('/vendas', [
    'middlewares' => ['required-admin-login'],

    function($request){
        return new Response(200, Pages\Sale::getSales($request));
    }
]);

$obRouter-> post('/vendas', [
    'middlewares' => ['required-admin-login'],

    function($request){
        return new Response(200, Pages\Sale::searchSales($request));
    }
]);

$obRouter-> get('/venda/{id}/edit', [
    'middlewares' => ['required-admin-login'],

    function($request, $id){
        return new Response(200, Pages\Sale::getEditSale($request, $id));
    }
]);

$obRouter-> post('/venda/{id}/edit', [
    'middlewares' => ['required-admin-login'],

    function($request, $id){
        return new Response(200, Pages\Sale::setEditSale($request, $id));
    }
]);

$obRouter-> get('/venda/{id}/delete', [
    'middlewares' => ['required-admin-login'],

    function($request, $id){
        return new Response(200, Pages\Sale::getDeleteSale($request, $id));
    }
]);


$obRouter->get('/cadastrarPagamento', [
    'middlewares' => ['required-admin-login'],

    function($request){
        return new Response(200, Pages\Payment::addPayment($request));
    }
]);

$obRouter->post('/cadastrarPagamento', [
    'middlewares' => ['required-admin-login'],

    function($request){
        return new Response(200, Pages\Payment::insertPayment($request));
    }
]);


$obRouter-> get('/pagamentos', [
    'middlewares' => ['required-admin-login'],

    function($request){
        return new Response(200, Pages\Payment::getPayments($request));
    }
]);

$obRouter-> post('/pagamentos', [
    'middlewares' => ['required-admin-login'],

    function($request){
        return new Response(200, Pages\Payment::searchPayments($request));
    }
]);

$obRouter-> get('/pagamento/{id}/edit', [
    'middlewares' => ['required-admin-login'],

    function($request, $id){
        return new Response(200, Pages\Payment::getEditPayment($request, $id));
    }
]);

$obRouter-> post('/pagamento/{id}/edit', [
    'middlewares' => ['required-admin-login'],

    function($request, $id){
        return new Response(200, Pages\Payment::setEditPayment($request, $id));
    }
]);

$obRouter-> get('/pagamento/{id}/delete', [
    'middlewares' => ['required-admin-login'],

    function($request, $id){
        return new Response(200, Pages\Payment::getDeletePayment($request, $id));
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




?>
