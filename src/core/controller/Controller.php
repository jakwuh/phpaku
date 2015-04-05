<?php

namespace Aku\Core\Controller;

use Aku\Core\Model\ContainerAware;

class Controller extends ContainerAware
{
	public function redirect($uri)
	{
		http_response_code(302);
		header("Location: " . $uri);
		die();
	}

	public function link($route_name, array $args = array())
	{
		return $this->container->get("router")->link($route_name, $args);
	}
}