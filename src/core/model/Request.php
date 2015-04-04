<?php

namespace Aku\Core\Model;

use Aku\Core\Model\ContainerAware;

class Request extends ContainerAware
{
	public function __construct()
	{
		$this->container = new Container();
	}

	public static function createFromGlobals()
	{
		$request = new Request();

		$request->set("method", strtolower($_SERVER["REQUEST_METHOD"]));
		$request->set("post", array_key_exists("form", $_POST) ? $_POST["form"] : array());
		$request->set("path", substr($_SERVER["REQUEST_URI"], 9));
		return $request;
	}

}