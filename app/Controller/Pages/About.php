<?php
    namespace app\Controller\Pages;

    use \app\Utils\View;
    use \app\Model\Entity\Organization;

    class About extends Page{
        public static function getAbout(){
            $obOrganization = new Organization;
            $content =  View::render('pages/about', [
                "name"=> $obOrganization->name,
                "description" => $obOrganization->description,
            ]);
            return parent::getPage("Sobre - João Bussolotto", $content);
        }
    }
?>
