<?php
namespace App\Model\Entity;
use \app\Db\Database;

class SaleProducts{
    public $id;
    public $id_sale;
    public $id_product;
    public $size;
		public $price;
		public $discount;
    public $total_amount;

		
  public function setTotalAmount(){
      $this->total_amount = floatval($this->price)*floatval($this->quantity) - (floatval($this->price)*floatval($this->quantity)*floatval($this->discount)/100);
  }


	public function save(){
        $this->setTotalAmount();
        $query =  'UPDATE PRODUCTS SET QUANTITY = QUANTITY -'.$this->quantity.'WHERE ID = '.$this->id_product;
        (new Database)->execute($query);
				$this->id = (new Database('SALES_PRODUCT'))->insert([
          'id_sale' => $this->id_sale,
					'id_product' => $this->id_product,
                    'quantity' => $this->quantity,
                    'discount' => $this->discount,
                    'price' => $this->price,
                    'TOTAL_AMOUNT' => $this->total_amount
                    
				]);

    


				return true;
		}

		public static function getSales($where = null, $order = null, $limit = null, $fields = '*'){
            return (new Database('SALES_PRODUCT'))->select($where,$order,$limit,$fields);
		}

		
		public static function getSalesProductById($id){
			return self::getSales('id = '.$id)->fetchObject(self::class);
		}

		public  function update(){

			
			return  (new Database('SALES_PRODUCT'))->update('id = '.$this->id,[
				'description' => $this->description,
                    'brand' => $this->brand,
                    'type' => $this->type,
                    'size' => $this->size,
                    'color' => $this->color,
                    'price' => $this->price,
                    'quantity' => $this->quantity,
			  ]);
  
  
	}

	    public static function deleteAllFromSaleID ($sale_id){
          $allsp = (new Database)->execute('SELECT * FROM SALES_PRODUCT WHERE ID_SALE = '.$sale_id);
          while($sp = $allsp->fetchObject(self::class)){
            $query =  'UPDATE PRODUCTS SET QUANTITY = QUANTITY +'.$sp->quantity.'WHERE ID = '.$sp->id_product;
            (new Database)->execute($query);
          }

          return (new Database('SALES_PRODUCT'))->delete('id_sale = '.$sale_id);
	}
		

	    public function delete ($id){
          $query =  'UPDATE PRODUCTS SET QUANTITY = QUANTITY +'.$this->quantity.'WHERE ID = '.$this->id_product;
          (new Database)->execute($query);
          return (new Database('SALES_PRODUCT'))->delete('id = '.$id);
	}




	
}