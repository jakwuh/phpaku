<?php

namespace Aku\Core\Model;

use Aku\Core\Model\Container;

class ContainerAware
{
	protected $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function get($key)
	{
		return $this->container->get($key);
	}

	public function set($key, $value)
	{
		$this->container->set($key, $value);
		return $this;
	}
}