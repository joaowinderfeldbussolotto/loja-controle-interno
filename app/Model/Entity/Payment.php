<?php
namespace App\Model\Entity;
use \app\Db\Database;
use \app\Model\Entity\Sale;

class Payment{
    public $id;
    public $id_costumer;
    public $payment_method;
    public $value;
    public $date;
    public $payer;
    
    

    public function save(){

        Sale::checkIfSaleIsPayed($this->id_costumer,$this->value);

        $this->id = (new Database('PAYMENTS'))->insert([
                    'id_costumer' => $this->id_costumer,
                    'payment_method' => $this->payment_method,
                    'value' => $this->value,
                    'date' => $this->date,
                    'payer' => $this->payer,
        ]);

                return true;
        }

        public static function getPayments($where = null, $order = null, $limit = null, $fields = '*'){
            return (new Database('PAYMENTS'))->select($where,$order,$limit,$fields);
        }

       

        public static function getPaymentById($id){
            return self::getPayments('id = '.$id)->fetchObject(self::class);
        }

        public  function update(){

            return  (new Database('PAYMENTS'))->update('id = '.$this->id,[
                'id_costumer' => $this->id_costumer,
                    'payment_method' => $this->payment_method,
                    'value' => $this->value,
                    'date' => $this->date,
                    'payer' => $this->payer,
                  
              ]);
  
  
    }
    public function delete ($id){
        return (new Database('PAYMENTS'))->delete('id = '.$this->id);
    }

}