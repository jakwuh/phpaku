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
		preg_match("/[a-z0-9]+/i", $value, $matches);
		if (empty($matches)) {
			$this->error = "wrong_value";
		} else {
			$this->value = $value;
		}
		return $this->error;
	}

	public function raw()
	{
		return $this->value;
	}
}