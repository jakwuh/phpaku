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
			$this->set($key, $value);
		}
		$this->extra_statement = "";
	}

	public function set($key, $value)
	{
		if ($this->has($key))
			$this->fields[$key]->set($value);
		else
			throw new ApplicationException("try to set unexisting property: \$this->" . $key);
		return $this;
	}

	public function get($key)
	{
		if ($this->has($key))
			return $this->fields[$key]->get();
		else 
			throw new ApplicationException("try to get unexisting property: \$this->" . $key);
	}

	public function raw($key)
	{
		if ($this->has($key))
			return $this->fields[$key]->raw();
		else
			throw new ApplicationException("try to get unexisting property: \$this->" . $key);
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
			throw new ApplicationException("try to get unexisting property: \$this->" . $key);
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

	public function remove(Connection $connection)
	{
		return $connection->removeModel($this);
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function getExtraStatement()
	{
		return $this->extra_statement;
	}

	public static function getFieldsNames()
	{
		return array_keys(static::build());
	}

	abstract public static function build();

	// abstract public function getWhereStatement();

	abstract public static function getSearchFields();

	abstract public static function getTableName();
}