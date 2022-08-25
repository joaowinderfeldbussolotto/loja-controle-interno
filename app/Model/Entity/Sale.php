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
   public $leftforpay = 0;

   public function setStatus()
   {
      if ($this->payment_method == 1 or $this->payment_method == 2) {
         $payment = new Payment;
         $payment->id_costumer =  $this->id_costumer;
         $payment->value = $this->total_amount;
         $payment->payment_method = $this->payment_method;
         $payment->payer = 'Cliente';
         $payment->date = date("d-m-Y");
         $payment->save();
         $this->leftforpay = 0;
         $this->status = 'Pago';
         return;
      }
      $this->leftforpay = $this->total_amount;
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

   public  function update()
   {
      SaleProducts::deleteAllFromSaleId($this->id);
      foreach ($this->salesProducts as $sp) {
         $sp->id_sale = $this->id;
         $sp->save($sp->id);
      };
      $this->total_amount = (new Database)->execute("SELECT SUM(TOTAL_AMOUNT) as TA FROM SALES_PRODUCT WHERE ID_SALE = " . $this->id)->fetch()['ta'];


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
      $leftforpay = (new Database)->execute("SELECT LEFTFORPAY as LEFT FROM SALES_PRODUCT WHERE ID_SALE = " . $id_sale)->fetch()['LEFT'];
      $leftforpay = $leftforpay - $value;
      $query = "UPDATE SALES SET LEFTFORPAY =  " . $leftforpay;
      if ($leftforpay <= 0) {
         $query .= "STATUS = Pago ";
      }
      $query .= "WHERE ID_SALE = " . $id_sale;
      (new Database)->execute($query);
   }

   public function delete($id)
   {

      SaleProducts::deleteAllFromSaleId($this->id);

      return (new Database('SALES'))->delete('id = ' . $this->id);
   }
}
