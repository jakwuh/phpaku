<?php

namespace App\Controller;

use Aku\Core\Controller\Controller;
use Aku\Core\Model\View;
use App\Model\Image;
use App\Model\ImageCollection;
use Aku\Core\Model\Exception\DatabaseException;
use Aku\Core\Model\Exception\RequestException;

class GalleryController extends Controller
{

	public function allAction()
	{
		$images = new ImageCollection();
		$loaded = $images->load($this->get("connection"));
		if (!$loaded) throw new DatabaseException("cannot fetch images");
		$view = new View($this->container, "gallery");
		$view->set("images", $images->models);
		return $view;
	}

	public function postAction()
	{
		$image = new Image();
		$params = $this->get("request")->get("post");
		if (!is_array($params) || !array_key_exists("name", $params))
			throw new RequestException("wrong file upload request");
		$image->set("path", $params["name"]);
		$image->save($this->get("connection"));
		return $this->allAction();
	}

}