<?php

namespace App\Controller;

use Aku\Core\Controller\Controller;
use Aku\Core\Model\View;

class HomeController extends Controller
{
	public function indexAction()
	{
		$view = new View($this->container, "home");
		return $view;
	}
}