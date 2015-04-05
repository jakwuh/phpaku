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

	public function link($route_name, array $args = array())
	{
		$get = function(&$var){ return $var; };
		$routes = $this->routes;
		if (!array_key_exists($route_name, $routes))
			throw new ApplicationException("try to use unexisting route: " . $route_name);
		$route = $routes[$route_name];
		$subject = array_key_exists("generator", $route) ? $route["generator"] : $route["path"];
		$callback = function($matches) use ($args, $get) { return $get($args[$matches[1]]); };
		$output = preg_replace_callback("/{(.+)}/", $callback, $subject);
		$output = PATH_BASE . $output;
		return $output;
	}

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
		$path = PATH_CONFIG . "/routing.yml";
		$content = file_get_contents($path);
		if ($content === false)
			throw new ApplicationException("try to open unexisting file: " . $path);
		$this->routes = Yaml::parse($content);
		$response = new Response();

		foreach ($this->routes as $name => $args) {
			$parameters = self::test($args["path"], $request->get("path"));
			if ($parameters) {
				if (!array_key_exists("method", $args) || in_array($request->get("method"), $args["method"])) {
					$response->setCallback($args["controller"], $args["action"]);
					$request->set("parameters", $parameters);
					$this->get("logger")->info($this, "route matched: " . $name);
					return $response;
				}
			}
		}

		throw new WrongRouteException("route not found: " . $request->get("path"));
	}
}