<?php

namespace Aku\Core\Controller;

use Aku\Core\Model\Container;
use Aku\Core\Model\ContainerAware;
use Aku\Core\Model\Model;
use Aku\Core\Model\Collection;
use Aku\Core\Model\Exception\ApplicationException;

class Connection extends ContainerAware
{

	private $mysqli;

	function __construct(Container $container)
	{
		$this->container = $container;
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
		$table = $model::getTableName() . "(". self::glueArray($fields) . ")";
		$values = array();                                                                   
		$references = self::generateReferences($model, $fields, $values);
		$types = self::generateTypes($model, $fields);
		$omits = implode( ",", array_pad(array(), count($fields), "?"));

		$stmt = $this->mysqli->prepare("INSERT INTO {$table} VALUES ({$omits})");

		if (!$stmt) throw new ApplicationException("cannot prepare statement");
		call_user_func_array(array($stmt, "bind_param"), array_merge(array($types), $references));
		
		$result = $stmt->execute();
		return $result;
	}

	public function selectModel(Model $model)
	{
		$fields = self::glueArray($model::getFieldsNames());
		$table = $model::getTableName();
		$condition = self::generateOmits($model->getSearchFields(), " AND ");
		$extra = $model->getExtraStatement();               
		$values = array();                                                                   
		$references = self::generateReferences($model, $model::getSearchFields(), $values);
		$types = self::generateTypes($model, $model::getSearchFields());

		$stmt = $this->mysqli->prepare("SELECT {$fields} FROM `{$table}` WHERE {$condition} {$extra}");
		if (!$stmt) throw new ApplicationException("cannot prepare statement");
		call_user_func_array(array($stmt, "bind_param"), array_merge(array($types), $references));
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
		$omits = self::generateOmits($fields);
		$condition = self::generateOmits($model::getSearchFields(), " AND ");
		$values = array();
		$references = self::generateReferences($model, array_merge($fields, $model::getSearchFields()), $values);
		$types = self::generateTypes($model, array_merge($fields, $model::getSearchFields()));
		
		$stmt = $this->mysqli->prepare("UPDATE `{$table}` SET {$omits} WHERE {$condition}");
		if (!$stmt) throw new ApplicationException("cannot prepare statement");
		call_user_func_array(array($stmt, "bind_param"), array_merge(array($types), $references));

		$result = $stmt->execute();
		return $result;
	}

	public function removeModel(Model $model)
	{
		$table = $model::getTableName();
		$condition = self::generateOmits($model::getSearchFields(), " AND ");
		$values = array();                                                                   
		$references = self::generateReferences($model, $model::getSearchFields(), $values);
		$types = self::generateTypes($model, $model::getSearchFields());

		$stmt = $this->mysqli->prepare("DELETE FROM {$table} WHERE {$condition}");
		if (!$stmt) throw new ApplicationException("cannot prepare statement");
		call_user_func_array(array($stmt, "bind_param"), array_merge(array($types), $references));

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

	// `field1`=?,`field2` = ?
	public static function generateOmits(array $fields, $delimiter = ",")
	{
		$omits = implode( $delimiter, array_map(function($name){ return "`{$name}`=?"; }, $fields));
		return $omits;
	}

	public static function generateReferences(Model $model, array $fields, array &$values)
	{
		foreach ($fields as $key) {$values[] = $model->raw($key); }
		$references = array();
		foreach ($values as &$value) { $references[] = &$value; }
		return $references;
	}

	public static function generateTypes($model, $fields)
	{
		foreach ($fields as $key) {
			$types[] = $model->getType($key);
		}
		$types_string = implode("", $types);
		return $types_string;
	}

	public static function glueArray(array $names, $delimiter = ",", $wrapper = "`")
	{
		$strings = array_map(function($value) use ($wrapper) { return $wrapper . $value . $wrapper; }, $names);
		return implode($delimiter, $strings);
	}

}