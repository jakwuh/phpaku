<?php

namespace App\Model;

use Aku\Core\Model\Form\Form;
use Aku\Core\Model\Form\TextField;
use Aku\Core\Model\Form\IntField;
use Aku\Core\Model\Form\StrictTextField;
use Aku\Core\Controller\CSRFGuard;

class ArticleForm extends Form
{
	function __construct()
	{
		parent::__construct();
		$this->fields["title"] = new TextField("title");
		$this->fields["promo_text"] = new TextField("promo_text");
		$this->fields["text"] = new TextField("text");
	}

	public static function getName()
	{
		return "article_form";
	}
}