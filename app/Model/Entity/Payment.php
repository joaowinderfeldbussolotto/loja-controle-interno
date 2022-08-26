<?php

namespace App\Model\Entity;

use \app\Db\Database;
use \app\Model\Entity\Sale;

class Payment
{
   public $id;
   public $id_sale;
   public $payment_method;
   public $value;
   public $date;
   public $payer;



   public function save()
   {
      $this->id = (new Database('PAYMENTS'))->insert([
         'id_sale' => $this->id_sale,
         'payment_method' => $this->payment_method,
         'value' => $this->value,
         'date' => $this->date,
         'payer' => $this->payer,
      ]);
      Sale::payingSale($this->id_sale, $this->value);
      return true;
   }

   public static function getPayments($where = null, $order = null, $limit = null, $fields = '*')
   {
      return (new Database('PAYMENTS'))->select($where, $order, $limit, $fields);
   }



   public static function getPaymentById($id)
   {
      return self::getPayments('id = ' . $id)->fetchObject(self::class);
   }

   public  function update()
   {
      Sale::payingSale($this->id_sale, $this->value);
      return (new Database('PAYMENTS'))->update('id = ' . $this->id, [
         'id_sale' => $this->id_sale,
         'payment_method' => $this->payment_method,
         'value' => $this->value,
         'date' => $this->date,
         'payer' => $this->payer,
      ]);

   }
   public function delete($id)
   {

      return (new Database('PAYMENTS'))->delete('id = ' . $this->id);
   }

   public static function deleteById($id)
   {

      return (new Database('PAYMENTS'))->delete('id = ' . $id);
   }
}
