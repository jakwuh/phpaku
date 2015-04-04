<?php

namespace Aku\Core\Model;

use Aku\Core\Controller\Connection;

abstract class Model
{

	protected $fields;
	protected $extra_statement;

	public function __construct(array $fields)
	{
		foreach ($fields as $key => $value) {
			$this->fields[$key]->set($value);
		}
		$this->extra = "";
	}

	public function set($key, $value)
	{
		if ($this->has($key))
			$this->fields[$key]->set($value);
		else 
			// :ERROR
		return $this;
	}

	public function get($key)
	{
		if ($this->has($key))
			return $this->fields[$key]->get();
		else ;
			// ERROR:
	}

	public function raw($key)
	{
		if ($this->has($key))
			return $this->fields[$key]->raw();
		else ;
			// ERROR:
	}

	public function has($key)
	{
		return array_key_exists($key, $this->fields);
	}

	public function update(Connection $connection)
	{
		return $connection->updateModel($this);
	}

	public function load(Connection $connection)
	{
		return $connection->selectModel($this);
	}

	public function save(Connection $connection)
	{
		return $connection->saveModel($this);
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function getExtraStatement()
	{
		return $this->extra_statement;
	}

	abstract public function getWhereStatement();

	abstract public static function getFieldsNames();

	abstract public static function getTableName();
}