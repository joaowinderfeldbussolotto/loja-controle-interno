<?php
    namespace app\Controller\Pages;

    use \app\Utils\View;
    use \app\Model\Entity\Organization;
    use \app\Model\Entity\Costumer as EntityCostumer;
    use \app\Db\Pagination;
    use \app\Controller\Pages\Alert;
    class Costumer extends Page{
        private static function getCostumerItems ($request, &$obPagination, $key = null){
            $items  = '';
            $totalQuantity = EntityCostumer::getCostumers(null, null, null, 'count (*) as quantity')->fetchObject()->quantity;
            !is_null($key) ? $where = "upper(name) like UPPER('%".$key."%')" : $where = '';
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


        public static function getCostumers($request, $errorMessage = null, $key = null){

            $status = !is_null($errorMessage) ? Alert::getSuccess($errorMessage) : '';


            $content =  View::render('pages/costumer/costumers', [
                'items' => self::getCostumerItems($request, $obPagination,$key),
                'pagination' => parent::getPagination($request,$obPagination),
                'status' => $status,
                'search' => !is_null($key)? $key : 'Procure por nome '
            ]);
            
            return parent::getPage("Clientes", $content);
        }

        public static function getForm($request, $status, $title, $obCostumer = null){
            $content =  View::render('pages/costumer/form', [ 
                'option' => $title,
                'status' => $status,
                'costumer_name' => !is_null($obCostumer) ? $obCostumer->name : '',
                'cpf' => !is_null($obCostumer) ? $obCostumer->cpf : '',
                'rg' => !is_null($obCostumer) ? $obCostumer->rg : '',
                'civil_state' => !is_null($obCostumer) ? $obCostumer->civil_state : '',
                'spouse' => !is_null($obCostumer) ? $obCostumer->spouse : '',
                'filiation' => !is_null($obCostumer) ? $obCostumer->filiation : '',
                'birthday' => !is_null($obCostumer) ? date("Y-m-d", strtotime($obCostumer->birthday)) : '',  
                'cellphone_number' => !is_null($obCostumer) ?  $obCostumer->cellphone_number : '',
                'address' => !is_null($obCostumer) ? $obCostumer->address : '',
                'readonly' => !is_null($obCostumer) ? 'true' : '',
         ]);
            return parent::getPage($title, $content);
        }

        public static function addCostumer($request, $errorMessage = null){
            $status = !is_null($errorMessage) ? 
            Alert::getError($errorMessage) : '';

            return self::getForm($request, $status, "Adicionar cliente", null);


      
        }

        public static function validate($request,$obCostumer){
            if(time() < strtotime('+18 years', strtotime($obCostumer->birthday)))  {
                return self::addCostumer($request, 'Cliente deve ser maior que 18 anos');
            }
            if($obCostumer::checkIfCostumerExists($obCostumer->cpf, $obCostumer->rg)){
                return self::addCostumer($request, 'Cliente já cadastrado');
            }
            else{
                $obCostumer->save();
                return self::getCostumers($request, "Usuário cadastrado com sucesso!");    

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


        public static function setEditCostumer($request, $id, $errorMessage = null){

            $status = !is_null($errorMessage) ? 
            Alert::getSuccess($errorMessage) : '';

            $obCostumer = EntityCostumer::getCostumerById($id);
            //print_r($obCostumer);


            if(!$obCostumer instanceof EntityCostumer ){
                $request->getRouter()->redirect('/pages/costumer/costumers');
            }

            $postVars = $request->getPostVars();
            $obCostumer = new EntityCostumer;
            $obCostumer->id = $id;
            $obCostumer->name = $postVars['name'] ?? $obCostumer->name;
            $obCostumer->cpf = $postVars['cpf'] ?? $obCostumer->cpf;
            $obCostumer->rg = $postVars['rg'] ?? $obCostumer->rg;
            $obCostumer->civil_state = $postVars['estado_civil'] ?? $obCostumer->civil_state;
            $obCostumer->spouse = $postVars['spouse'] ?? $obCostumer->spouse ;
            $obCostumer->filiation = $postVars['filiation'] ?? $obCostumer->filiation;
            $obCostumer->birthday = $postVars['birthday'] ??  $obCostumer->birthday;
            $obCostumer->cellphone_number = $postVars['cellphone_number'] ?? $obCostumer->cellphone_number ;
            $obCostumer->address = $postVars['address'] ?? $obCostumer->address;

            if(!$obCostumer->update() instanceof EntityCostumer){
                return self::getCostumers($request, "Cliente atualizado com sucesso!");
            }
            else{
                $status = Alert::getError("Não foi possível editar!");
                return self::getForm($request, $status, "Editar cliente", $obCostumer);

            }


        }


        public static function getEditCostumer($request, $id, $errorMessage = null){

            $status = !is_null($errorMessage) ? 
            Alert::getSuccess($errorMessage) : '';

            $obCostumer = EntityCostumer::getCostumerById($id);

            if(!$obCostumer instanceof EntityCostumer ){
                $request->getRouter()->redirect('/pages/costumer/costumers');
            }

            return self::getForm($request, $status, "Editar cliente", $obCostumer);
        }

        public static function searchCostumers($request){
            $key = $request->getPostVars()['search_name'];
            return self::getCostumers($request, null, $key);
        }

        public static function getDeleteCostumer($request,$id){
            $obCostumer = EntityCostumer::getCostumerById($id);
            //print_r($obCostumer);


            if(!$obCostumer instanceof EntityCostumer ){
                $request->getRouter()->redirect('/pages/costumer/costumers');
            }

            if($obCostumer->delete($id)){
                return self::getCostumers($request, "Cliente deletado com sucesso!");
            }

        }
    }
?>
