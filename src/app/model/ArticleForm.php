<?php

namespace App\Model;

use Aku\Core\Model\Form\Form;
use Aku\Core\Model\Form\TextField;
use Aku\Core\Model\Form\IntField;
use Aku\Core\Model\Form\StrictTextField;

class ArticleForm extends Form
{
	public function __construct()
	{
		parent::__construct();
		$this->fields["id"] = new IntField("id");
		$this->fields["title"] = new TextField("title");
		$this->fields["promo_text"] = new TextField("promo_text");
		$this->fields["text"] = new TextField("text");
		$this->fields["author"] = new StrictTextField("author");
	}

	public static function getName()
	{
		return "article_form";
	}
}