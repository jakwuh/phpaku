<?php

namespace Aku\Core\Controller;

use Aku\Core\Controller\Router;
use Aku\Core\Controller\Guard;
use Aku\Core\Controller\Logger;
use Aku\Core\Controller\Connection;
use Aku\Core\Model\Container;
use Aku\Core\Model\ContainerAware;
use Aku\Core\Model\Request;
use Aku\Core\Model\Response;
use Aku\Core\Model\View;
use Aku\Core\Model\Exception\Exception;
use Aku\Core\Model\Exception\NotFoundException;
use Aku\Core\Model\Exception\WrongRouteException;
use Symfony\Component\Yaml\Yaml;

class Kernel extends ContainerAware
{
	public function __construct()
	{
		try {
			$this->container = new Container();
			$this->loadSettings();
			$this->logger = new Logger($this->container);
			$this->router = new Router($this->container);
			$this->guard = new Guard($this->container);
			$this->connection = new Connection($this->container);
		} catch (Exception $e) {
			$logger = new Logger(new Container());
			$logger->log($this, "Fatal Error in file " . __FILE__ . " on line " . __LINE__);
			$view = new View($this->container, "error");
			$view->set("message", "default");
			$view->render();
			die();
		}
	}

	private function loadSettings()
	{
		$settings = Yaml::parse(file_get_contents(PATH_CONFIG . "/config.yml"));
		$this->set("settings", $settings);
	}

	public function handle(Request $request)
	{
		try {
			$response = $this->router->match($request);
			$this->request = $request;
			$response->process($this->container);
		} catch (NotFoundException $e) {
			$view = new View($this->container, "error");
			$view->set("message", "not_found");
			$view->render();
			die();
		} catch (WrongRouteException $e) {
			$view = new View($this->container, "error");
			$view->set("message", "wrong_route");
			$view->render();
			die();
		} catch (Exception $e) {
			$view = new View($this->container, "error");
			$view->set("message", "default");
			$view->render();
			die();
		}
		
		return $response;
	}

	public function __get($key)
	{
		return $this->container->get($key);
	}

	public function __set($key, $value)
	{
		$this->container->set($key, $value);
		return $this;
	}
}