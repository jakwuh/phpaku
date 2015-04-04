<?php

namespace Aku\Core\Controller;

use Aku\Core\Model\Container;
use Aku\Core\Model\Model;
use Aku\Core\Model\Collection;

class Connection
{

	private $mysqli;

	public function __construct(Container $container)
	{
		$host = $container->get("settings")["db_host"];
		$database = $container->get("settings")["db_name"];
		$user = $container->get("settings")["db_user"];
		$password = $container->get("settings")["db_password"];
		$this->mysqli = new \mysqli($host, $user, $password, $database);
		// ERROR:
		$this->mysqli->set_charset("utf8");
		// ERROR:
	}

	public function saveModel(Model $model)
	{
		$fields = $model::getFieldsNames();
		$values = implode(",", array_map(
			function($key) use ($model){ return $model->raw($key); }, $fields));
		$table = $model::getTableName() . "(" . implode(",", $fields) . ")";
		$query = "INSERT INTO {$table} VALUES( {$values} )";

		$result = $this->mysqli->query($query);

		if ($result) {
			return true;
		}
		return false;
	}

	public function selectModel(Model $model)
	{
		$fields = implode(",", $model::getFieldsNames());
		$table = $model::getTableName();
		$condition = $model->getWhereStatement();
		$extra = $model->getExtraStatement();
		$query = "SELECT {$fields} FROM {$table} WHERE {$condition} {$extra}";

		$result = $this->mysqli->query($query);

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
		$values = implode(",", array_map(
			function($key) use ($model){ return $key . "=" . $model->raw($key); }, $fields));
		$table = $model::getTableName();
		$condition = $model->getWhereStatement();
		$query = "UPDATE {$table} SET {$values} WHERE {$condition}";

		$result = $this->mysqli->query($query);
		if ($result) {
			return true;
		}
		return false;
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


		$result = $this->mysqli->query($query);

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