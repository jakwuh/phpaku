<?php

namespace App\Controller;

use Aku\Core\Model\View;
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
		if (!$loaded);
			// throw
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

	public function updateAction()
	{
		$article = $this->load();
		$form = new ArticleForm();
		$form->bindRequest($this->get("request"));
		if (count($form->errors) != 0) {
			$view = new View($this->container, "article_edit");
			$view->set("form", $form);
			return $view;
		} else {
			$id = $article->get("id");
			$form->bindToModel($article);
			$article->set("id", $id);
			$connection = $this->container->get("connection");
			$updated = $article->update($connection);
			// ERROR::
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
		if (!$loaded);
			// throw
		return $article;
	}
}