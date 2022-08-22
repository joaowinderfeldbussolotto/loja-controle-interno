<?php
    namespace app\Controller\Pages;

    use \app\Utils\View;
    use \app\Model\Entity\Organization;
    use \app\Model\Entity\Sale as EntitySale;
    use \app\Db\Pagination;
    use \app\Db\Database;
    use \app\Controller\Pages\Alert;
    use \app\Model\Entity\Costumer;
    use \app\Model\Entity\Product;
    use \app\Model\Entity\SaleProducts;

    class Sale extends Page{
        private static function getSaleItems ($request, &$obPagination, $key = null){
            $items  = '';
            $totalQuantity = EntitySale::getSales(null, null, null, 'count (*) as quantity')->fetchObject()->quantity;
            if(!is_null($key)){
                if(is_int($key)){
                    $where = "sale_id = ".$key;
                }
                else{
                    $where = "UPPER(costumer_name) LIKE  '%".strtoupper($key)."%'";
                }
            }
            else{
                $where = '';
            }
            $queryParams = $request->getQueryParams();
        

            $currentPage = $queryParams['page'] ?? 1;
            
            //INSTANCIA DE PAGINAÇÃO

            $obPagination = new Pagination($totalQuantity, $currentPage, 10); // 10 por pagina


            $results = (new Database('SALES_INFO'))->select($where, null, null, '*');
            while($obSale = $results->fetch()){
                $items.= View::render('pages/sale/sales_row', [
                    'id' => $obSale['sale_id'],
                    'costumer_name' => $obSale['costumer_name'],
                    'payment_method' => $obSale['payment_description'],
                    'status' => $obSale['status'],
                    'date' => $obSale['date'],
                    'products' => $obSale['products'],
                    'total_amount' => $obSale['total_amount'],
                    'left_for_pay' => $obSale['left_for_pay'],
                ]);
            }
            return $items;
        }


        public static function getSales($request, $errorMessage = null, $key = null){
            $status = !is_null($errorMessage) ? Alert::getSuccess($errorMessage) : '';


            $content =  View::render('pages/sale/sales', [
                'items' => self::getSaleItems($request, $obPagination,$key),
                'pagination' => parent::getPagination($request,$obPagination),
                'status' => $status,
                'search_id' => !is_null($key)? $key : 'Procure por id ',
                'search_name' => !is_null($key)? $key : 'Procure por nome ',
            ]);
            
            return parent::getPage("Vendas", $content);
        }
        
        

        public static function getProducts(){
            $results = Product::getProducts();
            $products = [];
            while($obProduct = $results->fetchObject(Product::class)){
                array_push($products, [$obProduct->id,$obProduct->price, $obProduct->quantity]);
             }    
            $js = "<script> var products = [";

            $i = 0;
            foreach($products as $p){
              if($i+1 == count($products)){
                    $js.='['.$p[0].','.$p[1].','.$p[2].']';
              }
              else{
                $js.='['.$p[0].','.$p[1].','.$p[2].'],';
              }
              $i+=1;
            }
            $js.=']; </script>';
            return $js;
      }

        public static function getForm($request, $status, $title, $obSale = null, $products_html = null, $first = null){
            $content =  View::render('pages/sale/form', [ 
                'datalist' => is_null($obSale) ? Costumer::getCostumerDataList() : '<input type="text"class="form-control" name="id_costumer" value="'.$obSale->id_costumer    .'" readonly />',
                'option' => $title,
                'status' => $status,
                'payment_method' => is_null($obSale)? '1' : $obSale->payment_method,
                'products' => self::getProducts(),
                'products_sale' => !is_null($products_html) ? $products_html : '',
                'discount' => !is_null($first) ? $first['discount'] : '',
                'quantity' => !is_null($first) ? $first['quantity'] : '',
                'price' => !is_null($first) ? $first['price'] : '',
                'id_product' => !is_null($first) ? $first['id_product'] : '1'
         ]);
            return parent::getPage($title, $content);
        }

        public static function addSale($request, $errorMessage = null){
            $status = !is_null($errorMessage) ? 
            Alert::getError($errorMessage) : '';

            return self::getForm($request, $status, "Cadastrar venda", null);

      
        }

        public static function validate($request,$obSale){
            
                $obSale->save();
                
                return self::getSales($request, "Venda cadastrada com sucesso!");    

            }


        

        public static function insertSale($request){
            $postVars = $request->getPostVars();
            $obSale= new EntitySale;
            $number = count($postVars['products']);
            $salesProducts = [];
            for ($i = 0; $i < $number; $i++) {
                $saleProduct = new SaleProducts;
                $saleProduct->id_product = $postVars['products'][$i];
                $saleProduct->price = $postVars['price'][$i];
                $saleProduct->quantity = $postVars['quantity'][$i];
                $saleProduct->discount = $postVars['discount'][$i] ?? 0.0;
                array_push($salesProducts,$saleProduct);
            }
            $obSale->id_costumer = $postVars['id_costumer'];
            $obSale->payment_method = $postVars['payment_method'];
            $obSale->salesProducts = $salesProducts;

            return self::validate($request, $obSale);
        }


        public static function setEditSale($request, $id, $errorMessage = null){

            $status = !is_null($errorMessage) ? 
            Alert::getSuccess($errorMessage) : '';

            $obSale= new EntitySale;
            $obSale = EntitySale::getSaleById($id);

            if(!$obSale instanceof EntitySale ){
                $request->getRouter()->redirect('/pages/Sale/Sales');
            }

            $postVars = $request->getPostVars();
          
            $number = count($postVars['products']);
            $salesProducts = [];
            for ($i = 0; $i < $number; $i++) {
                $saleProduct = new SaleProducts;
                $saleProduct->id_product = $postVars['products'][$i];
                $saleProduct->price = $postVars['price'][$i];
                $saleProduct->quantity = $postVars['quantity'][$i];
                $saleProduct->discount = $postVars['discount'][$i] ?? 0.0;
                array_push($salesProducts,$saleProduct);
            }
            $obSale->id_costumer = $postVars['id_costumer'];
            $obSale->payment_method = $postVars['payment_method'];
            $obSale->salesProducts = $salesProducts;


            if(!$obSale->update() instanceof EntitySale){
                return self::getSales($request, "Venda atualizada com sucesso!");
            }
            else{
                $status = Alert::getError("Não foi possível editar!");
                return self::getForm($request, $status, "Erro", $obSale, $id);

            }


        }


        public static function getEditSale($request, $id, $errorMessage = null){

            $status = !is_null($errorMessage) ? 
            Alert::getSuccess($errorMessage) : '';
            $obSale = EntitySale::getSaleById($id);
            if(!$obSale instanceof EntitySale ){
                $request->getRouter()->redirect('/pages/sale/sales');
            }
            $where = 'id_sale = '.$id;            
            $i = 1;
            $products_html = '';
            $results = (new Database('SALES_PRODUCT'))->select($where, null, null, '*');
            while($obSaleProducts = $results->fetch()){
                $i = $i + 1;

                if($i <= 2){
                    $first = $obSaleProducts;
                }
                else{
                $products_html.= 
                    '<tr id="row'.
                    $i.
                    '"> <td><select onChange = "setPrice(this.value,' 
                    .$i.
                    ')" id="id_products'.
                    $i.
                    '" name="products[]" class="form-control name_list"> <option value = "'.$obSaleProducts['id_product'].'">'.$obSaleProducts['id_product'].'</option> getOptions() <td>  <input id="price' .
                    $i.
                    '" type="number"class="form-control" name="price[]" value="'.$obSaleProducts['price'].'" readonly /> </td> <td> <input id="quantity' .
                    $i.
                    '" type="number" class="form-control" min="1" max="15" name="quantity[]" value="'.$obSaleProducts['quantity'].'" required placeholder="Quantidade"/></td> <td> <input id="discount[]" type="number" class="form-control" name="discount[]" value="'.$obSaleProducts['discount'].'" placeholder="Desconto" required /> </td> <td><button type="button" name="remove" id="' .
                    $i.
                    '" class="btn btn-danger btn_remove">X</button></td></tr>';
                }


            }

            $obSale->salesProducts = $obSaleProducts;


            return self::getForm($request, $status, "Editar venda", $obSale, $products_html, $first);


           

      
        }

        public static function searchSales($request){
            $key = $request->getPostVars()['search_name'];

            if(is_null($key)){
                $key = $request->getPostVars()['search_id'];
                return self::getSales($request, null, intval($key));

            }
            return self::getSales($request, null, $key);
        }

        public static function getDeleteSale($request,$id){
            $obSale = EntitySale::getSaleById($id);

            
            if(!$obSale instanceof EntitySale ){
                $request->getRouter()->redirect('/pages/Sale/Sales');
            }

            if($obSale->delete($id)){
                return self::getSales($request, "Venda deletada com sucesso!");
            }

        }
    }
?>
