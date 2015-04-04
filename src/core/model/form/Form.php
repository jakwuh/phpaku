<?php

namespace Aku\Core\Model\Form;

use Aku\Core\Model\Form\Field;
use Aku\Core\Model\Model;
use Aku\Core\Model\Request;

class Form
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
		else ;
		// ERROR:
	}

	public function out($key)
	{
		echo $this->get($key);
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
			$error = $this->fields[$key]->set($model->get($key));
			if ($error) $errors[] = $error;
		}
	}

	public function bindRequest(Request $request)
	{
		$get = function(&$var){ return $var; };
		foreach ($this->fields as $key => $value) {
			$error = $this->fields[$key]->set($get($request->get("post")[$key]));
			if ($error) $errors[] = $error;
		}
	}
}