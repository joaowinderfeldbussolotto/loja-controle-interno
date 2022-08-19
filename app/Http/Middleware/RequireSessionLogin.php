<?php
namespace App\Http\Middleware;
use \app\Session\User\Login as SessionUserLogin;
class RequireSessionLogin{
    public function handle($request, $next){
        if(!SessionUserLogin::isLogged()){
            $request->getRouter()->redirect('/login');
        }

        return $next($request);
    }
}