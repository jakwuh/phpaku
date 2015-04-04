<?php

namespace Aku\Core\Model\Form;

abstract class Field
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
		$this->error = "";;
		$this->value = $value;
		return $this->error;
	}

	public function raw()
	{
		return $this->get();
	}

	abstract public static function getType();
}