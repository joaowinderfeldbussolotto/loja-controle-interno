<?php
    namespace app\Controller\Pages;
    use \app\Utils\View;

class Alert{
  
  public static function getError($message){
    return View::render('pages/login/alert',[
      'message' => $message ,
      'type' => 'danger' 
  ]);
  }
  public static function getSuccess($message){
    return View::render('pages/login/alert',[
      'message' => $message ,
      'type' => 'success' 
  ]);
  }

}