<?php

namespace Aku\Core\Model;

use Aku\Core\Model\Container;
use Aku\Core\Model\ContainerAware;
use Symfony\Component\Yaml\Yaml;
use Aku\Core\Model\Exception\ApplicationException;

class View extends ContainerAware
{

	private $template;
	private $translations;
	private $args;
	private $response_code;

	function __construct(Container $container, $template)
	{
		$this->container = $container;
		$this->template = $this->generateTemplatePath($template);
		$path = $this->generateTranslationPath();
		$content = file_get_contents($path);
		if ($content === false)
			throw new ApplicationException("try to open unexisting file: " . $path);
		$this->translations = Yaml::parse($content);
		$this->args = array();
	}

	public function generateTemplatePath($template)
	{
		return PATH_ROOT . $this->container->get("settings")["template_dir"] 
		. "/" . $template . "." . $this->container->get("settings")["template_ext"];
	}

	public function generateTranslationPath()
	{
		return 	PATH_ROOT . $this->container->get("settings")["translation_dir"] .
			"/" . $this->container->get("settings")["language"] . ".yml";
	}

	public function asset($name)
	{
		return PATH_BASE . "/" . $this->container->get("settings")["public_dir"] . "/" . $name;
	}

	public function echoAsset($name)
	{
		echo $this->asset($name);
	}

	public function link($route_name, array $args = array())
	{
		$get = function(&$var){ return $var; };
		$routes = $this->container->get("router")->routes;
		if (!array_key_exists($route_name, $routes))
			throw new ApplicationException("try to use unexisting route: " . $route_name);
		$route = $routes[$route_name];
		$subject = array_key_exists("generator", $route) ? $route["generator"] : $route["path"];
		$callback = function($matches) use ($args, $get) { return $get($args[$matches[1]]); };
		$output = preg_replace_callback("/{(.+)}/", $callback, $subject);
		$output = PATH_BASE . $output;
		return $output;
	}

	public function echoLink($route_name, array $args = array())
	{
		echo $this->link($route_name, $args);
	}

	public function echoImage($image)
	{
		echo $this->container->get("settings")["media_dir"] . "/" . $image->get("path");
	}

	public function echoThumbnail($image)
	{
		echo $this->container->get("settings")["media_dir"] . "/thumbnail/" . $image->get("path");
	}

	public function include_view($template, array $args = array())
	{
		$view = new View($this->container, $template);
		foreach ($args as $key => $value) {
			$view->set($key, $value);
		}
		$view->render();
	}

	public function render()
	{
		include $this->template;
	}

	public function translate($string)
	{
		$get = function (&$var) {return $var;};
		$chain = array_reverse(explode(".", $string));
		$el = $this->translations;
		while ($el && $key = array_pop($chain))
			$el = $get($el[$key]);

		return $el && is_string($el) ? $el : $string;
	}

	public function __get($key)
	{
		return $this->get($key);
	}

	public function get($key)
	{
		if (array_key_exists($key, $this->args))
			return $this->args[$key];
		else 
			throw new ApplicationException("try to access unexisting property: \$this->" . $key);
	}

	public function has($key)
	{
		return array_key_exists($key, $this->args);
	}

	public function set($key, $value)
	{
		$this->args[$key] = $value;
		return $this;
	}
}