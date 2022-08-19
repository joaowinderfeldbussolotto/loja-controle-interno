<?php
    namespace app\Controller\Pages;

    use \app\Utils\View;
    use \app\Model\Entity\Organization;

    class Home extends Page{
        public static function getHome(){

            $content =  View::render('pages/home', [
                "content" => '<h1> Bem vindo </h1>'

            ]);
            return parent::getPage("Home", $content);
        }
    }
?>
