<?php 

namespace Aku\Core\Model\Form;

use Aku\Core\Model\Form\Field;

class TextField extends Field
{
	public function raw()
	{
		return "\"{$this->value}\"";
	}
}