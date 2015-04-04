<?php

namespace Aku\Core\Model\Form;

class Field
{
	public $name;
	public $value;
	public $error;

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function get()
	{
		return $this->value;
	}

	public function set($value)
	{
		$error = null;
		$this->value = $value;
		return $error;
	}

	public function raw()
	{
		return $this->get();
	}
}