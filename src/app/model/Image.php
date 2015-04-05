<?php

namespace App\Model;

use Aku\Core\Model\Model;
use Aku\Core\Model\Form\IntField;
use Aku\Core\Model\Form\TextField;
use Aku\Core\Model\Form\StrictTextField;
use Aku\Core\Model\Form\DateField;

class Image extends Model
{
	function __construct(array $fields = array())
	{
		$this->fields = $this->build();
		parent::__construct($fields);
	}

	public static function getSearchFields()
	{
		return array("id");
	}

	public static function getFieldsNames()
	{
		return array_keys(self::build());
	}

	public static function build()
	{
		$fields["id"] = new IntField("id");
		$fields["author"] = new StrictTextField("author");
		$fields["date"] = new DateField("date");
		$fields["path"] = new TextField("path");
		return $fields;
	}

	public static function getTableName()
	{
		return "image";
	}
}