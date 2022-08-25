<?php
    namespace app\Controller\Pages;
    use app\Controller\Pages\Alert;
    use \app\Utils\View;
    use \app\Model\Entity\User;
    use \app\Session\User\Login as SessionUserLogin;
    
class Login extends Page{

public static function getLogin($request, $errorMessage = null){
    $status = !is_null($errorMessage) ? 
        Alert::getError($errorMessage) : '';
    return  View::render('pages/login/login', [
        'title' => 'Login',
        'status' => $status
    ]);
    }

    public static function setLogin($request){
        $postVars = $request->getPostVars();
        $email = $postVars['email'] ?? '';
        $password = $postVars['password'] ?? '';
        $obUser = User::getUserByEmail($email);

        if(!$obUser instanceof User || !password_verify($password, $obUser->password)){
            return self::getLogin($request, 'Email ou senha inválidos');
        }

        SessionUserLogin::login($obUser);

        $request->getRouter()->redirect('/');
    }

    public static function setLogout($request){
        SessionUserLogin::logout();
        $request->getRouter()->redirect('/login');

    }
}
