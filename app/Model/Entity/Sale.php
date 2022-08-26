<?php

namespace App\Model\Entity;

use \app\Db\Database;
use \app\Model\Entity\SaleProducts;
use \app\Model\Entity\Payment;

class Sale
{
   public $id;
   public $id_costumer;
   public $payment_method;
   public $status;
   public $salesProducts = [];
   public $total_amount = 0;
   public $leftforpay;

   public function setStatus()
   {
      if ($this->payment_method == 1 or $this->payment_method == 2) {
         $this->status = 'Pago';
         return;
      }
      $this->status = 'Não pago';
   }
   public function save()
   {
      $a = 0;
      foreach ($this->salesProducts as $sp) {
         $sp->setTotalAmount();
         $a += $sp->total_amount;
      }
      $this->total_amount = $a;
      $this->leftforpay = $this->total_amount;
      $this->setStatus();
      $this->id = (new Database('SALES'))->insert([
         'id_costumer' => $this->id_costumer,
         'payment_method' => $this->payment_method,
         'status' => $this->status, 
         'TOTAL_AMOUNT' => $this->total_amount,
         'LEFTFORPAY' => $this->leftforpay

      ]);

     
   
      foreach ($this->salesProducts as $sp) {
         $sp->id_sale = $this->id;
         $sp->save();
      }
      
      if($this->status == 'Pago'){
         
         $payment = new Payment;
         $payment->id_sale =  $this->id;
         $payment->value = $this->total_amount;
         $payment->payment_method = $this->payment_method;
         $payment->payer = 'Cliente';
         $payment->date = date("d-m-Y");
         $payment->save();
      }
      return true;
   }

   public static function getSales($where = null, $order = null, $limit = null, $fields = '*')
   {
      return (new Database('SALES'))->select($where, $order, $limit, $fields);
   }


   public static function getSaleById($id)
   {
      return self::getSales('id = ' . $id)->fetchObject(self::class);
   }

   public  function setLeftForPay()
   {
      $total_payed = (new Database)->execute("SELECT SUM(VALUE) as payed FROM payments WHERE ID_SALE = " . $this->id)->fetch()['payed'];
      return ($this->total_amount -  $total_payed);
   }

   public  function update()
   {
      SaleProducts::deleteAllFromSaleId($this->id);
      foreach ($this->salesProducts as $sp) {
         $sp->id_sale = $this->id;
         $sp->save($sp->id);
      };
      $this->total_amount = (new Database)->execute("SELECT SUM(TOTAL_AMOUNT) as TA FROM SALES_PRODUCT WHERE ID_SALE = " . $this->id)->fetch()['ta'];
      if ($this->payment_method == 1 or $this->payment_method == 2) {      
         $payment = new Payment;
         $id_payment = (new Database)->execute("SELECT id as id FROM payments WHERE ID_SALE = " . $this->id)->fetch()['id'];
         $payment::deleteById($id_payment);
         $payment->id_sale =  $this->id;
         $payment->value = $this->total_amount;
         $payment->payment_method = $this->payment_method;
         $payment->payer = 'Cliente';
         $payment->date = date("d-m-Y");
         $payment->save();
         self::payingSale($this->id, $this->total_amount);
      } else {
         $this->leftforpay =  $this->setLeftForPay();
      }

      $this->setStatus();


      return (new Database('SALES'))->update('id = ' . $this->id, [
         'id_costumer' => $this->id_costumer,
         'payment_method' => $this->payment_method,
         'status' => $this->status,
         'TOTAL_AMOUNT' => $this->total_amount,
         'LEFTFORPAY' => $this->leftforpay

      ]);
   }

   

   public static function payingSale($id_sale, $value)
   {
      $leftforpay = (new Database)->execute("SELECT LEFTFORPAY as LEFT FROM SALES WHERE ID = " . $id_sale)->fetch()['left'];
      
      $leftforpay = $leftforpay - $value;
      $query = "UPDATE SALES SET LEFTFORPAY =  " . $leftforpay;
      if ($leftforpay <= 0) {
         $query .= " , STATUS = 'Pago' ";
      }
      $query .= "WHERE ID = " . $id_sale;

      (new Database)->execute($query);
   }

   public function delete($id)
   {

      SaleProducts::deleteAllFromSaleId($this->id);

      return (new Database('SALES'))->delete('id = ' . $this->id);
   }
}
