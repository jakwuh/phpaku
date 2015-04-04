<?php

namespace Aku\Core\Model;

use Aku\Core\Model\Container;
use Aku\Core\Model\Exception\ApplicationException;

class Response
{

	const DEFAULT_HTTP_RESPONSE_CODE = 200;
	const CONTROLLER_NAMESPACE = "\\App\\Controller\\";
	private $action;
	private $classname;
	private $view;
	private $response_code;

	public function setCallback($classname, $action)
	{
		$this->response_code = self::DEFAULT_HTTP_RESPONSE_CODE;
		$this->classname = $classname;
		$this->action = $action;
	}

	public function process(Container $container)
	{
		$class = self::CONTROLLER_NAMESPACE . $this->classname . "Controller";
		$method = $this->action . "Action";
		
		if (!class_exists($class))
			throw new ApplicationException("try to create unexisting controller: " . $class);
		$controller = new $class($container);
		
		if (!method_exists($controller, $method))
			throw new ApplicationException("try to call unexisting controller method: " . $method);
		$this->view = $controller->$method();
	}
	
	public function setResponseCode($code)
	{
		$this->response_code = $code;
	}

	public static function sendError($message, $http_response_code, $container = null)
	{
		if (!$container) $container = new Container();
		$response = new Response();
		$response->view = new View($container, "error");
		$response->view->set("message", $message);
		$response->setResponseCode($http_response_code);
		$response->send();
	}

	public function send()
	{
		http_response_code($this->response_code);
		$this->view->render();
	}
}