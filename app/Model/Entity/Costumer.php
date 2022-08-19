<?php
namespace App\Model\Entity;
use \app\Db\Database;

class Costumer{
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

		public function save(){

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

		public static function getCostumers($where = null, $order = null, $limit = null, $fields = '*'){
            return (new Database('COSTUMERS'))->select($where,$order,$limit,$fields);
		}

	
}