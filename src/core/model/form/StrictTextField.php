<?php 

namespace Aku\Core\Model\Form;

use Aku\Core\Model\Form\Field;

class StrictTextField extends Field
{
	public function raw()
	{
		return "\"{$this->value}\"";
	}
}