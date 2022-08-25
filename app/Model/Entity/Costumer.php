<?php

namespace App\Model\Entity;

use \app\Db\Database;

class Costumer
{
   public $id;
   public $name;
   public $cpf;
   public $rg;
   public $civil_state;
   public $spouse;
   public $filiation;
   public $birthday;
   public $address;
   public $cellphone_number;

   public function save()
   {

      $this->id = (new Database('COSTUMERS'))->insert([
         'name' => $this->name,
         'CPF' => $this->cpf,
         'RG' => $this->rg,
         'CIVIL_STATE' => $this->civil_state,
         'SPOUSE' => $this->spouse,
         'FILIATION' => $this->filiation,
         'BIRTHDAY' => $this->birthday,
         'ADDRESS' => $this->address,
         'CELLPHONE_NUMBER' => $this->cellphone_number
      ]);

      return true;
   }

   public static function getCostumers($where = null, $order = null, $limit = null, $fields = '*')
   {
      return (new Database('COSTUMERS'))->select($where, $order, $limit, $fields);
   }

   public static function checkIfCostumerExists($cpf, $rg)
   {
      $query = "SELECT count (*) FROM COSTUMERS WHERE CPF = '" . $cpf . "' OR RG = '" . $rg . "'";
      $result = (new Database('COSTUMERS'))->execute($query)->fetchObject()->count;

      return $result == 0 ? false : true;
   }

   public static function getCostumerById($id)
   {
      return self::getCostumers('id = ' . $id)->fetchObject(self::class);
   }

   public  function update()
   {
      return (new Database('COSTUMERS'))->update('id = ' . $this->id, [
         'name' => $this->name,
         'CIVIL_STATE' => $this->civil_state,
         'SPOUSE' => $this->spouse,
         'FILIATION' => $this->filiation,
         'BIRTHDAY' => $this->birthday,
         'ADDRESS' => $this->address,
         'CELLPHONE_NUMBER' => $this->cellphone_number
      ]);
   }

   public function delete($id)
   {
      return (new Database('COSTUMERS'))->delete('id = ' . $this->id);
   }

   public static function getCostumerDataList()
   {
      $obCostumers = self::getCostumers();
      $datalist = ' <div class="form-group">
									<label>Cliente</label>
								<input list="costumers" class="form-control" id="id_costumer" name="id_costumer" required> <datalist id = "costumers">';
      $results = $obCostumers;
      while ($obCostumer = $results->fetchObject(Costumer::class)) {
         $datalist .= '<option value="' . $obCostumer->id . '">' . $obCostumer->name . '</option>';
      }
      $datalist .= '</datalist></input>
		</div>';
      return $datalist;
   }
}
