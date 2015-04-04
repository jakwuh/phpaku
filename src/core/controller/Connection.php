<?php

namespace Aku\Core\Controller;

use Aku\Core\Model\Container;
use Aku\Core\Model\Model;
use Aku\Core\Model\Collection;
use Aku\Core\Model\Exception\ApplicationException;

class Connection
{

	private $mysqli;

	function __construct(Container $container)
	{
		$host = $container->get("settings")["db_host"];
		$database = $container->get("settings")["db_name"];
		$user = $container->get("settings")["db_user"];
		$password = $container->get("settings")["db_password"];
		$this->mysqli = new \mysqli($host, $user, $password, $database);

		if ($this->mysqli->connect_errno)
			throw new ApplicationException("cannot create connection: " . $this->mysqli->connect_error);

		if (!$this->mysqli->set_charset("utf8"))
			throw new ApplicationException("cannot set charset: " . $this->mysqli->connect_error);
	}

	public function saveModel(Model $model)
	{
		$fields = $model::getFieldsNames();
		$table = $model::getTableName() . "(" . implode(",", $fields) . ")";
		$values =  array_map(function ($key) use ($model){ return $model->raw($key); }, $fields);
		$references = array();
		foreach ($values as $key => &$value) { $references[$key] = &$value; }
		$types = array_map(function($key) use ($model){ return $model->getType($key); }, $fields);
		$types_string = implode("", $types);
		$omits = "?" . implode( "", array_pad(array(), count($fields) - 1, ",?"));
		
		$stmt = $this->mysqli->prepare("INSERT INTO {$table} VALUES ({$omits})");
		if (!$stmt) throw new ApplicationException("cannot prepare statement");
		call_user_func_array(array($stmt, "bind_param"), array_merge(array($types_string), $references));
		
		$result = $stmt->execute();
		return $result;
	}

	public function selectModel(Model $model)
	{
		$fields = implode(",", $model::getFieldsNames());
		$table = $model::getTableName();
		$condition = $model->getWhereStatement();
		$extra = $model->getExtraStatement();

		$stmt = $this->mysqli->prepare("SELECT {$fields} FROM {$table} WHERE {$condition} {$extra}");
		if (!$stmt) throw new ApplicationException("cannot prepare statement");
		$stmt->execute();

		$result = $stmt->get_result();

		if ($result) {
			if ($row = $result->fetch_assoc()) {
				foreach ($row as $key => $value) {
					$model->set($key, $value);
				}
				return true;
			}
		}
		return false;
	}

	public function updateModel(Model $model)
	{
		$fields = $model::getFieldsNames();
		$table = $model::getTableName();
		$condition = $model->getWhereStatement();
		$values =  array_map(function ($key) use ($model){ return $model->raw($key); }, $fields);
		$references = array();
		foreach ($values as $key => &$value) { $references[$key] = &$value; }
		$types = array_map(function($key) use ($model){ return $model->getType($key); }, $fields);
		$types_string = implode("", $types);
		$omits = implode( ",", array_map(function($name){ return "{$name}=?"; }, $fields));
		
		$stmt = $this->mysqli->prepare("UPDATE {$table} SET {$omits} WHERE {$condition}");
		if (!$stmt) throw new ApplicationException("cannot prepare statement");
		call_user_func_array(array($stmt, "bind_param"), array_merge(array($types_string), $references));

		$result = $stmt->execute();
		return $result;
	}

	public function selectCollection(Collection $collection)
	{
		$fields = implode(",", $collection::getFieldsNames());
		$table = $collection::getTableName();
		$condition = $collection->getWhereStatement();
		$extra = $collection->getExtraStatement();
		
		if (empty($condition))
			$query = "SELECT {$fields} FROM {$table} {$extra}";
		else
			$query = "SELECT {$fields} FROM {$table} WHERE {$condition} {$extra}";

		$stmt = $this->mysqli->prepare($query);
		if (!$stmt) throw new ApplicationException("cannot prepare statement");
		$stmt->execute();

		$result = $stmt->get_result();

		if ($result) {
			$classname = $collection::getClassName();
			while ($row = $result->fetch_assoc()) {
				$collection->add(new $classname($row));
			}	
			return true;
		}
		return false;
	}
}