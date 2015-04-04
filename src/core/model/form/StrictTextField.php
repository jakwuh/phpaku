<?php 

namespace Aku\Core\Model\Form;

use Aku\Core\Model\Form\Field;

class StrictTextField extends Field
{

	public static function getType()
	{
		return "s";
	}
	
	public function set($value)
	{
		$this->value = $value;
		return $this->error;
	}

	public function raw()
	{
		return $this->value;
	}
}