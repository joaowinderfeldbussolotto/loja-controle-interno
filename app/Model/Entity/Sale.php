<?php
namespace App\Model\Entity;
use \app\Db\Database;

class Sale{
		public $id;
		public $id_costumer;
		public $payment_method;
		public $status;
		public $salesProducts = [];
		
  public function setStatus(){
      if($this->payment_method ==1 or $this->payment_method==2){
          $this->status = 'Pago';
          return;
      }
      $this->status = 'Não pago';
    }
	public function save(){
        $this->setStatus();
				$this->id = (new Database('SALES'))->insert([
					'id_costumer' => $this->id_costumer,
                    'payment_method' => $this->payment_method,
                    'status' => $this->status,          
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

      foreach($this->salesProducts as $sp){
        $sp->id_sale = $this->id;
        $sp->deleteAllFromSaleId($this->id);
      };

      foreach($this->salesProducts as $sp){
        $sp->id_sale = $this->id;
        $sp->save($sp->id);
      };

			
			return  (new Database('SALES'))->update('id = '.$this->id,[
        'id_costumer' => $this->id_costumer,
                  'payment_method' => $this->payment_method,
                  'status' => $this->status,          
      ]);

     
  
  
	}


		

	public function delete ($id){
		return (new Database('SALES'))->delete('id = '.$this->id);
	}




	
}