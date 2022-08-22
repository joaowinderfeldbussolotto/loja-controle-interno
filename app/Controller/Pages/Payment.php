<?php
    namespace app\Controller\Pages;

    use \app\Utils\View;
    use \app\Model\Entity\Costumer;
    use \app\Model\Entity\Payment as EntityPayment;
    use \app\Db\Pagination;
    use \app\Db\Database;
    use \app\Controller\Pages\Alert;
    class Payment extends Page{
        private static function getPaymentItems ($request, &$obPagination, $key = null){
            $items  = '';
            $totalQuantity = EntityPayment::getPayments(null, null, null, 'count (*) as quantity')->fetchObject()->quantity;
            !is_null($key) ? $where = "id = ".$key : $where = '';
            $queryParams = $request->getQueryParams();
            $currentPage = $queryParams['page'] ?? 1;
            //INSTANCIA DE PAGINAÇÃO
            $obPagination = new Pagination($totalQuantity, $currentPage, 10); // 10 por pagina

            //$results = EntityPayment::getPayments($where, 'date ASC',$obPagination->getLimit());
            $results = (new Database('VIEW_ALL_PAYMENT_INFO'))->select($where, null, null, '*');
            while($obPayment = $results->fetch()){
                $items.= View::render('pages/payment/payment_row', [
                    'id' => $obPayment['payment_id'],
                    'costumer_id' => $obPayment['costumer_id'],
                    'costumer_name' => $obPayment['costumer_name'],
                    'value' => $obPayment['value'],
                    'date' => $obPayment['date'],
                    'payment_description' => $obPayment['payment_description'],
                    
                ]);
            }
            return $items;
        }


        public static function getPayments($request, $errorMessage = null, $key = null){

            $status = !is_null($errorMessage) ? Alert::getSuccess($errorMessage) : '';


            $content =  View::render('pages/payment/payments', [
                'items' => self::getPaymentItems($request, $obPagination,$key),
                'pagination' => parent::getPagination($request,$obPagination),
                'status' => $status,
                'search' => !is_null($key)? $key : 'Procure por nome '
            ]);
            
            return parent::getPage("Produtos", $content);
        }

        public static function getForm($request, $status, $title, $obPayment = null){
            $content =  View::render('pages/payment/form', [ 
                'datalist' => Costumer::getCostumerDataList(),
                'option' => $title,
                'status' => $status,
                'costumer_id' => $obPayment->id_costumer ?? '',
                'payment_method' => $obPayment->payment_method ?? '',
                'id' => $obPayment->id ?? '',
                'value' => $obPayment->value ?? '',
                'date' => !is_null ($obPayment) ? date("d-m-Y", strtotime($obPayment->date)) : '',
                'payer' => $obPayment->payer ?? '',
                'checked' => !is_null($obPayment) ? is_null($obPayment->payer) ?? 'checked' : ''

         ]);
            return parent::getPage($title, $content);
        }

        public static function addPayment($request, $errorMessage = null){
            $status = !is_null($errorMessage) ? 
            Alert::getError($errorMessage) : '';

            return self::getForm($request, $status, "Adicionar pagamento", null);


      
        }

        public static function validate($request,$obPayment){
            
                $obPayment->save();
                return self::getPayments($request, "Pagamento cadastrado com sucesso!");    

            }


        

        public static function insertPayment($request){
            $postVars = $request->getPostVars();
            $obPayment= new EntityPayment;
            $obPayment->id_costumer = $postVars['id_costumer'];
            $obPayment->payment_method = $postVars['payment_method'];
            $obPayment->value = $postVars['value'];
            $obPayment->date = $postVars['date'];
            $obPayment->payer = $postVars['payer'] ?? '';
            return self::validate($request, $obPayment);
        }


        public static function setEditPayment($request, $id, $errorMessage = null){

            $status = !is_null($errorMessage) ? 
            Alert::getSuccess($errorMessage) : '';

            $obPayment = EntityPayment::getPaymentById($id);
            if(!$obPayment instanceof EntityPayment ){
                $request->getRouter()->redirect('/pages/Payment/Payments');
            }

            $postVars = $request->getPostVars();
            $obPayment = EntityPayment::getPaymentById($id);
            $obPayment->id_costumer = $postVars['id_costumer'] ?? $obPayment->id_costumer;
            $obPayment->payment_method = $postVars['payment_method'] ?? $obPayment->payment_method;
            $obPayment->value = $postVars['value'] ?? $obPayment->value;
            $obPayment->date = $postVars['date'] ?? $obPayment->date;
            $obPayment->payer = $postVars['payer'] ??  $obPayment->payer ;
           


            if($obPayment->update()){
                return self::getPayments($request, "Pagamento atualizado com sucesso!");
            }
            else{
                $status = Alert::getError("Não foi possível editar!");
                return self::getForm($request, $status, "Editar pagamento", $obPayment);

            }


        }


        public static function getEditPayment($request, $id, $errorMessage = null){

            $status = !is_null($errorMessage) ? 
            Alert::getSuccess($errorMessage) : '';

            $obPayment = EntityPayment::getPaymentById($id);

            if(!$obPayment instanceof EntityPayment ){
                $request->getRouter()->redirect('/pages/Payment/Payments');
            }

            return self::getForm($request, $status, "Editar pgamento", $obPayment);


           

      
        }

        public static function searchPayments($request){
            $key = $request->getPostVars()['search'];
            return self::getPayments($request, null, $key);
        }

        public static function getDeletePayment($request,$id){
            $obPayment = EntityPayment::getPaymentById($id);


            
            if(!$obPayment instanceof EntityPayment ){
                $request->getRouter()->redirect('/pages/Payment/Payments');
            }

            if($obPayment->delete($id)){
            
                return self::getPayments($request, "Pagamento deletado com sucesso!");
            }

        }
    }
?>
