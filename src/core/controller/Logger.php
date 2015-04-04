<?php

namespace Aku\Core\Controller;

use Aku\Core\Model\Container;

class Logger
{
	public static function log(\Exception $e)
	{
		$file = fopen(PATH_ROOT . "/src/errors.log", "a");
		$time = new \DateTime();
		$string =
			"[" . $time->format("d-m-y H:i:s") . "] " .
			$e->getFile() . " : " . $e->getLine() . ": " . $e->getMessage() . "\n";

		fwrite($file, $string);
		fclose($file);
	}
}