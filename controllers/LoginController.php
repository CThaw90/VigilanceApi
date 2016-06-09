<?php

class LoginController {
	
	private $login;
	public function __construct() {
		$this->login = new Login();
	}

	public function get () {
		return '{"error":"GET method for login is not allowed. Use POST method"}';
	}

	public function post ($data) {
		return $this->login->authenticate($data);
	}

	public function put () {
		return '{"error":"PUT method for login is not allowed. Use POST method"}';
	}

	public function delete () {
		return '{"error":"DELETE method for login is not allowed. Use POST method"}';
	}
}