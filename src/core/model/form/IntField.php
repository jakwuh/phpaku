<?php 

namespace Aku\Core\Model\Form;

use Aku\Core\Model\Form\Field;

class IntField extends Field
{
	public function raw()
	{
		return $this->value ? $this->value : "NULL";
	}
}