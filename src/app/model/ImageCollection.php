<?php

namespace App\Model;

use Aku\Core\Model\Collection;

class ImageCollection extends Collection
{

	public function __construct()
	{
		parent::__construct();
		$this->extra_statement = "ORDER BY date DESC";
	}

	public static function getClassName()
	{
		return "App\\Model\\Image";
	}
}