<?php

namespace Aku\Core\Model\Form;

use Aku\Core\Model\Form\Field;
use Aku\Core\Model\Model;
use Aku\Core\Model\Request;
use Aku\Core\Model\Exception\ApplicationException;
use Aku\Core\Controller\CSRFGuard;

abstract class Form
{
	public $errors;
	public $errors_count;
	public $csrf; 	
	protected $fields;
	protected $args;

	function __construct()
	{
		$this->fields = array();
		$this->errors_count = 0;
		$this->errors = array();
		$this->args = array();
		$this->csrf = null;
	}

	public function setCSRF(CSRFGuard $csrf_guard)
	{
		$this->csrf = $csrf_guard->generateCSRFToken(static::getName());
	}

	public function get($key)
	{
		if (array_key_exists($key, $this->fields))
			return $this->fields[$key]->get();
		else if (array_key_exists($key, $this->args))
			return $this->args[$key];
		else
			return null;
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
		if (array_key_exists($key, $this->fields)) {
			$error = $this->fields[$key]->set($value);
			if ($error) $this->errors_count++;
		} else {
			$this->args[$key] = $value;
		}
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

	public function addError($message)
	{
		$this->errors[] = $message;
		$this->errors_count++;
	}

	public static function getCSRFName()
	{
		return "csrf_" . static::getName();
	}

	abstract public static function getName();

}