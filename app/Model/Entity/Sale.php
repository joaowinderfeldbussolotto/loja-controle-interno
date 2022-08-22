<?php
namespace App\Model\Entity;
use \app\Db\Database;
use \app\Model\Entity\SaleProducts;
use \app\Model\Entity\Payment;

class Sale{
		public $id;
		public $id_costumer;
		public $payment_method;
		public $status;
		public $salesProducts = [];
        public $total_amount = 0;
        public $left_for_pay = 0;
		
  public function setStatus(){
      if($this->payment_method ==1 or $this->payment_method==2){
          $payment = new Payment;
          $payment->id_costumer =  $this->id_costumer;
          $payment->value = $this->total_amount;
          $payment->payment_method = $this->payment_method;
          $payment->payer = 'Cliente';
          $payment->date = date("d-m-Y");
          $payment->save();
          $this->left_for_pay = 0;
          $this->status = 'Pago';
          return;
      }
      $this->left_for_pay = $this->total_amount;

      $this->status = 'Não pago';
    }
	public function save(){
        $a=0;
        foreach($this->salesProducts as $sp){
            $sp->setTotalAmount();
            $a+=$sp->total_amount;
        }
        $this->total_amount = $a;
        $this->setStatus();
				$this->id = (new Database('SALES'))->insert([
					'id_costumer' => $this->id_costumer,
                    'payment_method' => $this->payment_method,
                    'status' => $this->status,
                    'TOTAL_AMOUNT' => $this->total_amount,
                    'LEFTFORPAY' => $this->left_for_pay

				]);

                foreach($this->salesProducts as $sp){
                    $sp->id_sale = $this->id;
                    $sp->save();
                }

				return true;
		}

		public static function getSales($where = null, $order = null, $limit = null, $fields = '*'){
            return (new Database('SALES'))->select($where,$order,$limit,$fields);
		}

		
		public static function getSaleById($id){
			return self::getSales('id = '.$id)->fetchObject(self::class);
		}

		public  function update(){
            $total_amount=0 ;
            SalesProduct::deleteAllFromSaleId($this->id);
            foreach($this->salesProducts as $sp){
                $total_amount+=$total_amount;
                $sp->id_sale = $this->id;
                $sp->save($sp->id);
            };
            $this->total_amount = $total_amount;

            return  (new Database('SALES'))->update('id = '.$this->id,[
                        'id_costumer' => $this->id_costumer,
                        'payment_method' => $this->payment_method,
                        'status' => $this->status,   
                        'TOTAL_AMOUNT' => $this->total_amount,         
                        'LEFTFORPAY' => $this->left_for_pay         
       
            ]);
	}

    public static function checkIfSaleIsPayed($id,$value){
        $sales = (new Database)->execute("SELECT * FROM SALES WHERE UPPER(STATUS) != 'PAGO' AND id_costumer = ".$id." ORDER BY DATE DESC");
        while($sale = $sales->fetchObject(self::class)){
            $value = $sale->left_for_pay - $value;
            if ($value>= 0){
                (new Database)->execute("UPDATE SALES SET LEFTFORPAY =  ".$value."WHERE ID = ".$sale->id);
                break;
            }
            else{
                $query =  "UPDATE SALES SET LEFTFORPAY =  0 , STATUS = 'Pago' WHERE ID = ".$sale->id;
                (new Database)->execute($query);

            }
        }
    }


		

	public function delete ($id){
        
        SaleProducts::deleteAllFromSaleId($this->id);

		return (new Database('SALES'))->delete('id = '.$this->id);
	}




	
}