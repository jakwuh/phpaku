<?php

namespace App\Model;

use Aku\Core\Model\Model;
use Aku\Core\Model\Form\IntField;
use Aku\Core\Model\Form\TextField;
use Aku\Core\Model\Form\StrictTextField;
use Aku\Core\Model\Form\DateField;
use Aku\Core\Model\Form\Field;

class Article extends Model
{
	function __construct(array $fields = array())
	{
		$this->fields = self::build();
		parent::__construct($fields);
	}

	public static function getWhereFields()
	{
		return array("id");
	}

	public static function build()
	{
		$fields = array();
		$fields["id"] = new IntField("id");
		$fields["title"] = new TextField("title");
		$fields["promo_text"] = new TextField("promo_text");
		$fields["text"] = new TextField("text");
		$fields["date"] = new DateField("date");
		return $fields;
	}

	public static function getTableName()
	{
		return "article";
	}
}