<?php

namespace App\Model\Entity;

use \app\Db\Database;

class Product
{
   public $id;
   public $description;
   public $brand;
   public $type;
   public $size;
   public $color;
   public $price;
   public $quantity;


   public function save()
   {

      $this->id = (new Database('PRODUCTS'))->insert([
         'id' => $this->id,
         'description' => $this->description,
         'brand' => $this->brand,
         'type' => $this->type,
         'size' => $this->size,
         'color' => $this->color,
         'price' => $this->price,
         'quantity' => $this->quantity,
      ]);

      return true;
   }

   public static function getProducts($where = null, $order = null, $limit = null, $fields = '*')
   {
      return (new Database('PRODUCTS'))->select($where, $order, $limit, $fields);
   }


   public static function getProductById($id)
   {
      return self::getProducts('id = ' . $id)->fetchObject(self::class);
   }

   public  function update()
   {


      return (new Database('PRODUCTS'))->update('id = ' . $this->id, [
         'description' => $this->description,
         'brand' => $this->brand,
         'type' => $this->type,
         'size' => $this->size,
         'color' => $this->color,
         'price' => $this->price,
         'quantity' => $this->quantity,
      ]);
   }




   public function delete($id)
   {
      return (new Database('PRODUCTS'))->delete('id = ' . $this->id);
   }
}
