<?php

class Option {
	
	private static $authentication_error = '{"status": 403, "message":"Permission Denied. You do not have access to this resource"}';
	private $authentication;
	private $options;
	private $debug;

	public function __construct () {
		$this->authentication = new Authentication();
		$this->debug = new Debugger("Option.php");
	}

	public function generate () {
		$this->options = array("status" => 200);
		$this->options["message"] = "Cross Domain requests are allowed to this domain";

		return $this->authentication->isAuthorized() ? json_encode($this->options) : self::$authentication_error;
	}
}