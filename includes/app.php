<?php
    define('ROOT', __DIR__.'/../');
    require __DIR__.'/../vendor/autoload.php';
    use \app\Utils\View;
    use WilliamCosta\DotEnv\Environment;
    use \app\Db\Database;
    use \app\Http\Middleware\Queue as MiddlewareQueue;


    Environment::load(__DIR__.'/../');

    define('URL', getenv('URL'));
   
    Database::config(getenv('DB_HOST'),getenv('DB_NAME'),getenv('DB_USER'),getenv('DB_PASS'),getenv('DB_PORT'));

    // Define o valor padrão das variáveis
    View::init([
        'URL' => URL
    ]);

    MiddlewareQueue::setMap([
        'maintenance' => \app\Http\Middleware\Maintenance::class,
        'required-admin-logout' => \app\Http\Middleware\RequireSessionLogout::class,
        'required-admin-login' => \app\Http\Middleware\RequireSessionLogin::class
    ]);

    MiddlewareQueue::setDefault([
        'maintenance'
    ]);
