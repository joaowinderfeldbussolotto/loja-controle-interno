<?php
namespace App\Model\Entity;
use \app\Db\Database;

class User{
    public $id;
    public $name;
    public $email;
    public $CPF;
    public $login;
    public $senha;

    public static function getUserByEmail($email){
        return (new Database('USERS'))->select("email = '".$email."'")->fetchObject(self::class);
    }
    
   
}