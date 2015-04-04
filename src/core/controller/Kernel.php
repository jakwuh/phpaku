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
	function __construct()
	{
		try {
			$this->container = new Container();
			$this->loadSettings();
			$this->logger = new Logger($this->container);
			$this->router = new Router($this->container);
			$this->guard = new Guard($this->container);
			$this->connection = new Connection($this->container);
			return;
		} catch (\Exception $e) {
			Logger::log($e);
			Response::sendError("default", 500, $this->container);
		}
		die();
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
			return $response;
		} catch (NotFoundException $e) {
			Logger::log($e);
			Response::sendError("not_found", 404, $this->container);
		} catch (WrongRouteException $e) {
			Logger::log($e);
			Response::sendError("wrong_route", 404, $this->container);
		} catch (\Exception $e) {
			Logger::log($e);
			Response::sendError("default", 500, $this->container);
		}
		die();
	}

	public function __get($key)
	{
		return $this->get($key);
	}

	public function __set($key, $value)
	{
		return $this->set($key, $value);
	}
}