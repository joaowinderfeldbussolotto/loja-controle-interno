<?php
namespace App\Session\User;

class Login{

    private static function init(){
        //VERIFICA SE A SESSAO NAO ESTA ATIVA
        if(session_status() != PHP_SESSION_ACTIVE){
            session_start();
        }
    }

    public static function login ($obUser){ 
        self::init();
        $_SESSION['login']['user'] = [
            'id' => $obUser->id,
            'name' => $obUser->name,
            'email' => $obUser->email       
        ];

        return true;
    }

    public static function isLogged(){
        self::init();

        return isset($_SESSION['login']['user']['id']);
    }

    public static function logout(){
        self::init();
        unset($_SESSION['login']['user']);
        return true;
    }

}