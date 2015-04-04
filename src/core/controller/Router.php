<?php

namespace Aku\Core\Controller;

use Aku\Core\Model\Container;
use Aku\Core\Model\ContainerAware;
use Aku\Core\Model\Request;
use Aku\Core\Model\Response;
use Aku\Core\Model\Exception\WrongRouteException;
use Symfony\Component\Yaml\Yaml;

class Router extends ContainerAware
{

	public $routes;

	public static function test($pattern, $path)
	{
		$pattern = addcslashes($pattern, "/");
		$pattern = "/^{$pattern}$/i";
		preg_match($pattern, $path, $matches);
		$output = array();
		if (count($matches) == 1)
			$output[0] = $matches[0];
		foreach ($matches as $key => $value) {
			if (!is_numeric($key))
				$output[$key] = $value;
		}
		if (empty($output))
			return null;
		else
			return $output;
	}

	public function match(Request $request)
	{
		$this->routes = Yaml::parse(file_get_contents(PATH_CONFIG . "/routing.yml"));
		
		$response = new Response();

		$found = false;
		foreach ($this->routes as $name => $args) {
			if (($parameters = self::test($args["path"], $request->get("path"))) 
				&& (!array_key_exists("method", $args) || in_array($request->get("method"), $args["method"]))) 
			{
				$response->setCallback($args["controller"], $args["action"]);
				$request->set("parameters", $parameters);
				$found = true;
				break;
			}
		}
		if (!$found) throw new WrongRouteException();
		return $response;
	}
}