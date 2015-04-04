<?php

namespace Aku\Core\Model;

class Container
{

	private $attributes;

	public function __construct(array $attributes = array())
	{
		$this->attributes = $attributes;
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