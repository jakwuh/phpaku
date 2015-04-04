<?php

namespace Aku\Core\Model;

use Aku\Core\Model\Container;
use Aku\Core\Model\ContainerAware;
use Symfony\Component\Yaml\Yaml;
class View extends ContainerAware

{

	private $template;
	private $translations;
	private $args;

	public function __construct(Container $container, $template)
	{
		$this->container = $container;
		// ERROR: file does not exist
		$this->template = $this->generateTemplatePath($template);
		$this->translations = Yaml::parse(file_get_contents($this->generateTranslationPath()));
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
		// ERROR: route does not exist
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
			return null;
	}

	public function out($key)
	{
		echo $this->get($key);
	}

	public function set($key, $value)
	{
		$this->args[$key] = $value;
		return $this;
	}

}