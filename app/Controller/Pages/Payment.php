<?php

namespace app\Controller\Pages;

use \app\Utils\View;
use \app\Model\Entity\Costumer;
use \app\Model\Entity\Payment as EntityPayment;
use \app\Model\Entity\Sale as EntitySale;
use \app\Db\Pagination;
use \app\Db\Database;
use \app\Controller\Pages\Alert;

class Payment extends Page
{
   private static function getPaymentItems($request, &$obPagination, $key = null)
   {
      $items  = '';
      $totalQuantity = EntityPayment::getPayments(null, null, null, 'count (*) as quantity')->fetchObject()->quantity;
      !is_null($key) ? $where = "id_sale = " . $key : $where = '';
      $queryParams = $request->getQueryParams();
      $currentPage = $queryParams['page'] ?? 1;
      //INSTANCIA DE PAGINAÇÃO
      $obPagination = new Pagination($totalQuantity, $currentPage, 10); // 10 por pagina

      //$results = EntityPayment::getPayments($where, 'date ASC',$obPagination->getLimit());
      $results = (new Database('VIEW_ALL_PAYMENT_INFO'))->select($where, null, null, '*');
      while ($obPayment = $results->fetch()) {
         $items .= View::render('pages/payment/payment_row', [
            'id' => $obPayment['payment_id'],
            'id_sale' => $obPayment['id_sale'],
            'costumer_id' => $obPayment['costumer_id'],
            'costumer_name' => $obPayment['costumer_name'],
            'value' => $obPayment['value'],
            'date' => $obPayment['date'],
            'payment_description' => $obPayment['payment_description'],

         ]);
      }
      return $items;
   }


   public static function getPayments($request, $errorMessage = null, $key = null)
   {

      $status = !is_null($errorMessage) ? Alert::getSuccess($errorMessage) : '';
      $content =  View::render('pages/payment/payments', [
         'items' => self::getPaymentItems($request, $obPagination, $key),
         'pagination' => parent::getPagination($request, $obPagination),
         'status' => $status,
         'search' => !is_null($key) ? $key : 'Procure por cliente '
      ]);

      return parent::getPage("Produtos", $content);
   }

   public static function getForm($request, $status, $title, $obPayment = null)
   {
      $id_sale = $request->getQueryParams()['id_sale'] ?? null;
      if ($id_sale == null) {
         $id_sale = !is_null($obPayment) ? $obPayment->id_sale : '';
      }
      $content =  View::render('pages/payment/form', [
         'readonly' => $id_sale ? 'readonly' : '',
         'option' => $title,
         'status' => $status,
         'id_sale' => $id_sale,
         'payment_method' => $obPayment->payment_method ?? '',
         'id' => $obPayment->id ?? '',
         'value' => $obPayment->value ?? '',
         'date' => !is_null($obPayment) ? date("Y-m-d", strtotime($obPayment->date)) : '',
         'payer' => $obPayment->payer ?? '',
         'checked' => !is_null($obPayment) ? is_null($obPayment->payer) ?? 'checked' : ''

      ]);
      return parent::getPage($title, $content);
   }

   public static function addPayment($request, $errorMessage = null)
   {
      $status = !is_null($errorMessage) ?
         Alert::getError($errorMessage) : '';
      return self::getForm($request, $status, "Adicionar pagamento", null);
   }

   public static function validate($request, $obPayment)
   {
      if(! EntitySale::getSaleById($obPayment->id_sale) instanceof EntitySale){
         $status = Alert::getError("Venda com código: ".$obPayment->id_sale." não encontrada");
         return self::getForm($request, $status, "Cadastrar pagamento");
      }
      $obPayment->save();
      return self::getPayments($request, "Pagamento cadastrado com sucesso!");
   }

   public static function insertPayment($request)
   {
      $postVars = $request->getPostVars();
      $obPayment = new EntityPayment;
      $obPayment->id_sale = $postVars['id_sale'];
      $obPayment->payment_method = $postVars['payment_method'];
      $obPayment->value = $postVars['value'];
      $obPayment->date = $postVars['date'];
      $obPayment->payer = $postVars['payer'] ?? '';
      return self::validate($request, $obPayment);
   }


   // public static function setEditPayment($request, $id, $errorMessage = null)
   // {

   //    $status = !is_null($errorMessage) ?
   //       Alert::getSuccess($errorMessage) : '';

   //    $obPayment = EntityPayment::getPaymentById($id);
   //    if (!$obPayment instanceof EntityPayment) {
   //       $request->getRouter()->redirect('/pages/Payment/Payments');
   //    }

   //    $postVars = $request->getPostVars();
   //    $obPayment->id_sale = $postVars['id_sale'] ?? $obPayment->id_sale;
   //    $obPayment->payment_method = $postVars['payment_method'] ?? $obPayment->payment_method;
   //    $obPayment->value = $postVars['value'] ?? $obPayment->value;
   //    $obPayment->date = $postVars['date'] ?? $obPayment->date;
   //    $obPayment->payer = $postVars['payer'] ??  $obPayment->payer;

   
   //    if ($obPayment->update()) {
   //       return self::getPayments($request, "Pagamento atualizado com sucesso!");
   //    } else {
   //       $status = Alert::getError("Não foi possível editar!");
   //       return self::getForm($request, $status, "Editar pagamento", $obPayment);
   //    }
   // }


   // public static function getEditPayment($request, $id, $errorMessage = null)
   // {
   //    $status = !is_null($errorMessage) ?
   //       Alert::getSuccess($errorMessage) : '';

   //    $obPayment = EntityPayment::getPaymentById($id);
   //    if (!$obPayment instanceof EntityPayment) {
   //       $request->getRouter()->redirect('/pages/Payment/Payments');
   //    }
   //    return self::getForm($request, $status, "Editar pagamento", $obPayment);
   // }

   public static function searchPayments($request)
   {
      $key = $request->getPostVars()['search_id_sale'];
      return self::getPayments($request, null, $key);
   }

   public static function getDeletePayment($request, $id)
   {
      $obPayment = EntityPayment::getPaymentById($id);
      if (!$obPayment instanceof EntityPayment) {
         $request->getRouter()->redirect('/pages/Payment/Payments');
      }

      (new Database('SALES'))->execute('UPDATE SALES SET LEFTFORPAY = LEFTFORPAY + '.$obPayment->value.' WHERE
      id = '.$obPayment->id_sale);
    
      if ($obPayment->delete($id)) {
         return self::getPayments($request, "Pagamento deletado com sucesso!");
      }
   }
}
