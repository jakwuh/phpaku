<?php

namespace Aku\Core\Model;

use Aku\Core\Controller\Connection;
use Aku\Core\Model\Exception\ApplicationException;

abstract class Model
{

	protected $fields;
	protected $extra_statement;

	function __construct(array $fields)
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
			throw new ApplicatinoException("try to set unexisting property: \$this->" . $key);
		return $this;
	}

	public function get($key)
	{
		if ($this->has($key))
			return $this->fields[$key]->get();
		else 
			throw new ApplicatinoException("try to get unexisting property: \$this->" . $key);
	}

	public function raw($key)
	{
		if ($this->has($key))
			return $this->fields[$key]->raw();
		else
			throw new ApplicatinoException("try to get unexisting property: \$this->" . $key);
	}

	public function has($key)
	{
		return array_key_exists($key, $this->fields);
	}

	public function getType($key)
	{
		if ($this->has($key))
			return $this->fields[$key]->getType();
		else
			throw new ApplicatinoException("try to get unexisting property: \$this->" . $key);
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