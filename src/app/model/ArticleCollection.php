<?php

namespace App\Model;

use Aku\Core\Model\Collection;

class ArticleCollection extends Collection
{

	public function __construct()
	{
		parent::__construct();
		$this->extra_statement = "ORDER BY date DESC";
	}

	public static function getClassName()
	{
		return "App\\Model\\Article";
	}
}