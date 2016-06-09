<?php

class LogoutController {
	
	private $logout;
	public function __construct() {
		$this->logout = new Logout();
	}

	public function get () {
		return '{"error":"GET method for login is not allowed. Use PUT or DELETE method"}';
	}

	public function post () {
		return '{"error":"POST method for login is not allowed. Use PUT or DELETE method"}';
	}

	public function put () {
		return $this->logout->logoff();
	}

	public function delete () {
		return $this->logout->logoff();
	}
}