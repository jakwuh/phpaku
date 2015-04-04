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
		$loaded = $articles->load($this->get("connection"));
		if (!$loaded) throw new ApplicationException();
		$view = new View($this->container, "news");
		$view->set("articles", $articles);
		return $view;
	}

	public function showAction(Article $article  = null)
	{
		if (!$article) $article = $this->loadArticle();
		$view = new View($this->container, "article");
		$view->set("article", $article);
		return $view;
	}

	public function editAction()
	{
		return $this->renderForm($this->loadArticle());
	}

	public function addAction()
	{
		return $this->renderForm();
	}

	public function postAction()
	{
		$form = new ArticleForm();
		$form->bindRequest($this->get("request"));
		if (count($form->errors) != 0)
			return $this->renderForm(null, $form);
		
		$article = new Article();
		$form->bindToModel($article);
		$saved = $article->save($this->get("connection"));
		if (!$saved) throw new ApplicationException("cannot save article");
		return $this->allAction();
	}

	public function updateAction()
	{
		$article = $this->loadArticle();
		$form = new ArticleForm();
		$form->bindRequest($this->get("request"));
		$form->bind("id", $article->get("id"));
		if (count($form->errors) != 0)
			return $this->renderForm(null, $form);

		$form->bindToModel($article);
		$updated = $article->update($this->get("connection"));
		if (!$updated) throw new ApplicationException("cannot update article");

		return $this->showAction($this->loadArticle());
	}

	public function loadArticle($id = null)
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

	public function renderForm(Article $article =  null, ArticleForm $form = null)
	{
		if (!$form) $form = new ArticleForm();
		if ($article) {
			$form->bindFromModel($article);
			$form->bind("id", $article->get("id"));
		}
		$view = new View($this->container, "article_edit");
		$view->set("form", $form);
		return $view;
	}
}