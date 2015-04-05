<?php

namespace Aku\Core\Controller;

use Aku\Core\Model\Container;
use Aku\Core\Model\ContainerAware;
use Aku\Core\Model\CSRF;
use Aku\Core\Model\Exception\DatabaseException;

// TODO: make cron task to clean old csrf
class CSRFGuard extends ContainerAware
{
	function __construct(Container $container) {
		if (session_status() != PHP_SESSION_ACTIVE)
			session_start();
		$this->container = $container;
	}

	public function generateCSRFToken($action)
	{
		$sid = session_id();
		$timestamp = time() + $this->get("settings")["csrf_lifetime"];
		$time = new \DateTime();
		$time->setTimestamp($timestamp);
		$token = $this->generateToken();
		$csrf = new CSRF();
		$csrf
			->set("sid", $sid)
			->set("time", $time->format(DATE_RFC2822))
			->set("action", $action)
			->set("token", $token);
		$saved = $csrf->save($this->get("connection"));
		if (!$saved) throw new DatabaseException("cannot save csrf token");
		return $token;
	}

	public function validateCSRFToken($action, $token)
	{
		$sid = session_id();
		$csrf = new CSRF(array("action" => $action, "token" => $token, "sid" => $sid));
		$loaded = $csrf->load($this->get("connection"));
		if (!$loaded) return false;
		$removed = $csrf->remove($this->get("connection"));
		if (!$removed) throw new DatabaseException("cannot remove csrf token");
		return true;
	}

	public function generateToken()
	{
		$token = sha1((string)time() . (string)sha1(uniqid(mt_rand(), true)));
		return $token;
	}
}