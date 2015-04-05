<?php

namespace Aku\Core\Model;

use Symfony\Component\Yaml\Yaml;

class Container
{

	private $attributes;

	function __construct(array $attributes = array())
	{
		$this->attributes = $attributes;
		$this->loadSettings();
	}

	private function loadSettings()
	{
		$settings = Yaml::parse(file_get_contents(PATH_CONFIG . "/config.yml"));
		$this->set("settings", $settings);
	}

	public function get($key)
	{
		$key = strtolower($key);
		if ($this->has($key))
			return $this->attributes[$key];
		else
			return null;
	}

	public function set($key, $value)
	{
		$this->attributes[strtolower($key)] = $value;
		return $this;
	}

	public function has($key)
	{
		return array_key_exists(strtolower($key), $this->attributes);
	}

	public function remove($key)
	{
		$key = strtolower($key);
		if ($this->has($key))
			unset($this->attributes[$key]);
		return $this;
	}

	

}