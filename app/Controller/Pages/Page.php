<?php
    namespace app\Controller\Pages;

    use \App\Utils\View;

    class Page{

        private static function getHeader(){
            return View::render('pages/templates/header');
        }

        private static function getFooter(){
            return View::render('pages/templates/footer');
        }

        public static function getPage($title, $content,$header = null, $footer = null){
            return View::render('pages/page', [
                'title' => $title,
                'content' => $content,
                'URL' => $content,
                'name' => $_SESSION['login']['user']['name']

            ]);
        }
        public static function getPagination($request, $obPagination){
            $pages = $obPagination->getPages();
            if(count($pages) <= 1) return '';

            //LINKS
            $links = '';

            //URL ATUAL
            $url = $request->getRouter()->getCurrentUrl();

            $queryParams = $request->getQueryParams();

            //RENDERIZA LINKS

            foreach ($pages as $page) {
                $queryParams['page'] = $page['page'];

                $link = $url.'?'.http_build_query($queryParams);

                //VIEW
                $links .=  View::render('pages/pagination/link', [
                    'page'=> $page['page'],
                    'link'=> $link
        
                ]);
            }

            return View::render('pages/pagination/box', [
                "links"=> $links ]);
    
        }
    }
?>