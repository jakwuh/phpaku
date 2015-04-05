<?php 

namespace Aku\Core\Model;

use Aku\Core\Model\Model;
use Aku\Core\Model\Form\IntField;
use Aku\Core\Model\Form\StrictTextField;
use Aku\Core\Model\Form\DateField;

class CSRF extends Model
{

	function __construct(array $fields = array()) {
		$this->fields = self::build();
		parent::__construct($fields);
	}

	public static function build()
	{
		$fields = array();
		$fields["id"] = new IntField("id");
		$fields["sid"] = new StrictTextField("sid");
		$fields["token"] = new StrictTextField("token");
		$fields["action"] = new StrictTextField("action");
		$fields["time"] = new DateField("time");
		return $fields;
	}

	public static function getSearchFields()
	{
		return array("sid", "action", "token");
	}

	public static function getTableName()
	{
		return "csrf";
	}

}