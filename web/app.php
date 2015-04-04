<?php 

use Aku\Core\Controller\Kernel;
use Aku\Core\Model\Request;

define("PATH_ROOT", dirname(__DIR__));
define("PATH_CONFIG", dirname(__DIR__) . "/config");
define("PATH_BASE", $_SERVER["BASE"]);

require_once PATH_ROOT . "/vendor/autoload.php";

$kernel = new Kernel();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();

