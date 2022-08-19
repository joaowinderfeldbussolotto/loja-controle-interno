<?php
    namespace app\Controller\Pages;

    use \app\Utils\View;
    use \app\Model\Entity\Organization;
    use \app\Model\Entity\Costumer as EntityCostumer;
    use \app\Db\Pagination;
    class Costumer extends Page{
        private static function getCostumerItems ($request, &$obPagination){
            $items  = '';
            $totalQuantity = EntityCostumer::getCostumers(null, null, null, 'count (*) as quantity')->fetchObject()->quantity;
            $where = '';
            $queryParams = $request->getQueryParams();
        

            $currentPage = $queryParams['page'] ?? 1;
            
            //INSTANCIA DE PAGINAÇÃO

            $obPagination = new Pagination($totalQuantity, $currentPage, 10); // 10 por pagina

            $results = EntityCostumer::getCostumers($where, 'id DESC',$obPagination->getLimit());
            while($obCostumer = $results->fetchObject(EntityCostumer::class)){
                $items.= View::render('pages/costumer/costumer_row', [
                    'name' => $obCostumer->name,
                    'CPF' => $obCostumer->cpf,
                    'id' => $obCostumer->id,
                    'spouse' => $obCostumer->spouse,
                    'civil_state' => $obCostumer->civil_state,
                    'RG' => $obCostumer->rg,
                    'birthday' => date("d-m-Y", strtotime($obCostumer->birthday)),
                    'filiation' => $obCostumer->filiation,
                    'address' => $obCostumer->address,
                    'cell' => $obCostumer->cellphone_number
                ]);
            }
            return $items;
        }


        public static function getCostumers($request){

            $content =  View::render('pages/costumer/costumers', [
                'items' => self::getCostumerItems($request, $obPagination),
                'pagination' => parent::getPagination($request,$obPagination)
            ]);
            
            return parent::getPage("Clientes", $content);
        }

        public static function addCostumer($request, $errorMessage = null){
            $status = !is_null($errorMessage) ? 
            View::render('pages/login/alert',[
            'message' => $errorMessage ,
            'type' => 'danger' 
        ]) : '';
    
            $content =  View::render('pages/costumer/form', [ 
                'status' => $status
        ]);
            return parent::getPage("Cadastrar clientes", $content);
        }

        public static function validate($request,$obCostumer){
            if(time() < strtotime('+18 years', strtotime($obCostumer->birthday)))  {
                return self::addCostumer($request, 'Cliente deve ser maior que 18 anos');
            }
            if($obCostumer->getCostumers("cpf = '".$obCostumer->cpf."' or rg = '".$obCostumer->rg."'", null, null, 'count(*)') != 0){
                return self::addCostumer($request, 'Cliente já cadastrado');
            }
            else{
                $obCostumer->save();
                return self::getCostumers($request);    

            }


        }

        public static function insertCostumer($request){
            $postVars = $request->getPostVars();
            $obCostumer= new EntityCostumer;
            $obCostumer->name = $postVars['name'];
            $obCostumer->cpf = $postVars['cpf'];
            $obCostumer->rg = $postVars['rg'];
            $obCostumer->civil_state = $postVars['estado_civil'];
            $obCostumer->spouse = $postVars['spouse'] ?? '';
            $obCostumer->filiation = $postVars['filiation'] ?? '';
            $obCostumer->birthday = $postVars['birthday'] ?? '';
            $obCostumer->cellphone_number = $postVars['cellphone_number'];
            $obCostumer->address = $postVars['address'] ?? '';
            return self::validate($request, $obCostumer);
        }
    }
?>
