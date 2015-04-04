<?php

namespace Aku\Core\Model;

use Aku\Core\Model\Container;
use Aku\Core\Model\ContainerAware;

class Response
{

	private $action;
	private $classname;
	private $view;

	public function setCallback($classname, $action)
	{
		$this->classname = $classname;
		$this->action = $action;
	}

	public function process(Container $container)
	{
		// ERROR:  throw error if callback does not exist
		$class = "\\App\\Controller\\" . $this->classname . "Controller";
		$method = $this->action . "Action";
		$controller = new $class($container);
		$this->view = $controller->$method();
	}
	
	public function send()
	{
		$this->view->render();
	}
}