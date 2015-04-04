<?php 

namespace Aku\Core\Model\Form;

use Aku\Core\Model\Form\Field;

class TextField extends Field
{

	public static function getType()
	{
		return "s";
	}

	public function set($value)
	{
		if (empty($value))
			$this->error = "empty";
		else
			$this->value = $value;
		return $this->error;
	}

	public function raw()
	{
		return $this->value;
	}
}