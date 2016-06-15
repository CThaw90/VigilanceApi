<?php

class LogoutController {
	
	private $auth_error = '{"status": 403, "error": "Permission Denied. You do not have access to this resource"}';
	private $authentication;
	private $logout;
	private $debug;

	public function __construct() {
		$this->debug = new Debugger("LogoutController.php");
		$this->debug->log("[INFO] Entering Constructor of Logout Controller", 5);
		$this->authentication = new Authentication();
		$this->logout = new Logout();
	}

	public function get () {
		return '{"error":"GET method for login is not allowed. Use PUT or DELETE method"}';
	}

	public function post () {
		return '{"error":"POST method for login is not allowed. Use PUT or DELETE method"}';
	}

	public function put () {
		$this->debug->log("[INFO] Invoking logout action with REQUEST_METHOD PUT", 4);
		return $this->authentication->isAuthorized() ? $this->logout->logoff() : $this->auth_error;
	}

	public function delete () {
		$this->debug->log("[INFO] Invoking logout action with REQUEST_METHOD DELETE", 4);
		return $this->authentication->isAuthorized() ? $this->logout->logoff() : $this->auth_error;
	}
}