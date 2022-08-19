<?php
    require __DIR__.'/includes/app.php';
    use \app\Http\Router;

    ob_start();

    $obRouter = new Router(URL);

    include __DIR__.'/routes/pages.php';

    // Imprime o response da página
    $obRouter->run()->sendResponse();
    ob_end_flush();
?>
