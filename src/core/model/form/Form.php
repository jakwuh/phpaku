<?php

namespace Aku\Core\Model\Form;

use Aku\Core\Model\Form\Field;
use Aku\Core\Model\Model;
use Aku\Core\Model\Request;
use Aku\Core\Model\Exception\ApplicationException;

abstract class Form
{
	protected $fields;
	public $errors;

	public function __construct()
	{
		$this->fields = array();
		$this->errors = array();
	}

	public function get($key)
	{
		if (array_key_exists($key, $this->fields))
			return $this->fields[$key]->get();
		else 
			throw new ApplicationException();
	}

	public function out($key)
	{
		echo $this->get($key);
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function bind($key, $value)
	{
		$error = $this->fields[$key]->set($value);
		if ($error) $this->errors[] = $error;
	}

	public function bindToModel(Model $model)
	{
		foreach ($this->fields as $field) {
			$model->set($field->name, $field->value);
		}
	}

	public function bindFromModel(Model $model)
	{
		foreach ($this->fields as $key => $value) {
			$this->bind($key, $model->get($key));
		}
	}

	public function bindRequest(Request $request)
	{
		$get = function(&$var){ return $var; };
		foreach ($this->fields as $key => $value) {
			$this->bind($key, $get($request->get("post")[$key]));
		}
	}

	abstract public static function getName();

}