<?php

namespace App\Controller;

use Aku\Core\Controller\Controller;
use Aku\Core\Model\View;
use App\Model\Image;
use App\Model\ImageCollection;

class GalleryController extends Controller
{

	public function allAction()
	{
		$images = new ImageCollection();
		$connection = $this->container->get("connection");
		$loaded = $images->load($connection);
		if (!$loaded);
			// throw
		$view = new View($this->container, "gallery");
		$view->set("images", $images->models);
		return $view;
	}

	public function postAction()
	{
		$image = new Image();
		$connection = $this->container->get("connection");
		$params = $this->container->get("request")->get("post");
		// ERROR:
		$image->set("path", $params["name"]);
		$image->save($connection);
		return $this->allAction();
	}

}