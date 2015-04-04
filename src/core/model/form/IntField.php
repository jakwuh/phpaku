<?php 

namespace Aku\Core\Model\Form;

use Aku\Core\Model\Form\Field;

class IntField extends Field
{
	public static function getType()
	{
		return "i";
	}

	public function raw()
	{
		return $this->value;
	}
}