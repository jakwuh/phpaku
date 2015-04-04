<?php

namespace App\Controller;

use Aku\Core\Model\View;
use Aku\Core\Model\Exception\ApplicationException;
use Aku\Core\Model\Exception\NotFoundException;
use Aku\Core\Controller\Controller;
use App\Model\ArticleCollection;
use App\Model\Article;
use App\Model\ArticleForm;

class NewsController extends Controller
{

	public function allAction()
	{
		$articles = new ArticleCollection();
		$connection = $this->container->get("connection");
		$loaded = $articles->load($connection);
		if (!$loaded)
			throw new ApplicationException();
		$view = new View($this->container, "news");
		$view->set("articles", $articles);
		return $view;
	}

	public function showAction()
	{
		$article = $this->load();
		$view = new View($this->container, "article");
		$view->set("article", $article);
		return $view;
	}

	public function editAction()
	{
		$article = $this->load();
		$form = new ArticleForm();
		$form->bindFromModel($article);
		$view = new View($this->container, "article_edit");
		$view->set("form", $form);
		return $view;
	}

	public function addAction()
	{
		$form = new ArticleForm();
		$view = new View($this->container, "article_edit");
		$view->set("form", $form);
		return $view;
	}

	public function postAction()
	{
		$form = new ArticleForm();
		$form->bindRequest($this->get("request"));
		if (count($form->errors) != 0) {
			$view = new View($this->container, "article_edit");
			$view->set("form", $form);
			return $view;
		} else {
			$article = new Article();
			$form->bindToModel($article);
			$connection = $this->container->get("connection");
			$article->save($connection);
			return $this->allAction();
		}
	}

	public function updateAction()
	{
		$article = $this->load();
		$id = $article->get("id");
		$form = new ArticleForm();
		$form->bindRequest($this->get("request"));
		$form->bind("id", $id);
		if (count($form->errors) != 0) {
			$view = new View($this->container, "article_edit");
			$view->set("form", $form);
			return $view;
		} else {
			$form->bindToModel($article);
			$article->set("id", $id);
			$connection = $this->container->get("connection");
			$updated = $article->update($connection);
			if (!$updated)
				throw new ApplicationException();
			$article = $this->load();
			$view = new View($this->container, "article");
			$view->set("article", $article);
			return $view;
		}
	}

	public function load($id = null)
	{
		$request = $this->get("request");
		$id = $id ? $id : (int) $request->get("parameters")["id"];
		$article = new Article(array("id" => $id));
		$connection = $this->get("connection");
		$loaded = $article->load($connection);
		if (!$loaded)
			throw new NotFoundException();
		return $article;
	}
}