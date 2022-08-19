<?php
namespace App\Http\Middleware;
use \app\Session\User\Login as SessionUserLogin;
class RequireSessionLogout{
    public function handle($request, $next){
        if(SessionUserLogin::isLogged()){
            $request->getRouter()->redirect('/');
        }

        return $next($request);
    }
}