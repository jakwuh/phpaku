<?php

namespace Aku\Core\Controller;

use Aku\Core\Controller\Router;
use Aku\Core\Controller\Guard;
use Aku\Core\Controller\CSRFGuard;
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
use Aku\Core\Model\Exception\ApplicationException;
use Aku\Core\Model\Exception\CSRFNotValidException;

class Kernel extends ContainerAware
{
	function __construct()
	{
		try {
			$this->container = new Container();
			$this->logger = new Logger($this->container);
			$this->router = new Router($this->container);
			$this->guard = new Guard($this->container);
			$this->csrf_guard = new CSRFGuard($this->container);
			$this->connection = new Connection($this->container);
			return;
		} catch (\Exception $e) {
			Logger::log($e);
			Response::sendError("default", 500, $this->container);
		}
		die();
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
		} catch (CSRFNotValidException $e) {
			Logger::log($e);
			Response::sendError("csrf_not_valid", 403, $this->container);
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