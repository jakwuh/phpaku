<?php

namespace Aku\Core\Model;

use Aku\Core\Model\Model;
use Aku\Core\Controller\Connection;

abstract class Collection
{
	public $models;
	protected $condition;
	protected $extra_statement;

	function __construct()
	{
		$this->condition = "";
		$this->models = array();
	}

	public function add(Model $model)
	{
		$this->models[] = $model;
	}

	public function all()
	{
		return $models;
	}

	public function get($i)
	{
		if (array_key_exists($i, $this->models))
			return $this->models[$i];
		else
			return null;
	}

	public function count()
	{
		return count($this->models);
	}

	public function load(Connection $connection)
	{
		return $connection->selectCollection($this);
	}

	public function getExtraStatement()
	{
		return $this->extra_statement;
	}

	public static function getTableName()
	{
		$name = static::getClassName();
		return $name::getTableName();
	}

	public static function getFieldsNames()
	{
		$name = static::getClassName();
		return $name::getFieldsNames();
	}

	public function getWhereStatement()
	{
		return $this->condition;
	}

	abstract public static function getClassName();
}