<?php

class Logout {

	private $debug;

	public function __construct () {
		$this->debug = new Debugger("Logout.php");
	}
	
	public function logoff () {
		$auth = new Authentication();

		$this->debug->log("[INFO] Authorizing User for logout action", 5);
		$user = $auth->get_user($auth->get_token());

		$this->debug->log("[INFO] Sending token destroy signal", 5);
		$auth->destroy_token();

		$this->debug->log("[INFO] User successfully logged out of application", 4);
		return '{"status": 200, "message": "User successfully logged out"}';
	}
}