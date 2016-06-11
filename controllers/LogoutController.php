<?php

class LogoutController {
	
	private $auth_error = '{"status": 403, "error": "Permission Denied. You do not have access to this resource"}';
	private $authentication;
	private $logout;

	public function __construct() {
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
		return $this->authentication->isAuthorized() ? $this->logout->logoff() : $this->auth_error;
	}

	public function delete () {
		return $this->authentication->isAuthorized() ? $this->logout->logoff() : $this->auth_error;
	}
}