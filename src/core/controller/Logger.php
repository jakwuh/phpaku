<?php

namespace Aku\Core\Controller;

use Aku\Core\Model\Container;

class Logger
{
	private $file;

	public function __construct(Container $container)
	{
		$this->file = fopen(PATH_ROOT . "/src/errors.log", "a");
	}

	public function log($object, string $string)
	{
		$time = new \DateTime();
		$string =
			"[" . $time->format("d-m-y H:i:s") . "] "
			. get_class($object) ." : " . $string . "\n";

		fwrite($this->file, $string);
	}

	public function __destruct()
	{
		fclose($this->file);
	}
}