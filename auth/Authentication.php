<?php

session_start();

class Authentication {

	private $auth_error = '{"status": 403, "error": "Permission Denied. You do not have access to this resource"}';
	private $debug;
	private $db;

	private static $token_key = "Token";

	public function __construct () {
		$this->debug = new Debugger("Authentication.php");
	}

	public function generate_token ($user) {
		if (!isset($_SESSION[self::$token_key])) {
			$token = sha1(json_encode($user) . time());
			$_SESSION[self::$token_key] = $token;
			$_SESSION[$token] = $user;
		}
	}

	public function session_active () {
		$this->debug->log(isset($_SESSION[self::$token_key]) ? ("[INFO] Currently Active [TOKEN] " . $_SESSION[self::$token_key]) : "[INFO] No TOKEN currently active", 5);
		$this->debug->log(isset($_SESSION[self::$token_key]) ? ("[INFO] Currently Active [USER] " . 
			json_encode($_SESSION[$_SESSION[self::$token_key]])) : "[INFO] No [USER] currently active", 5);

		return isset($_SESSION[self::$token_key]);
	}

	public function ignore () {
		$this->debug->log("[WARNING] Invoked authentication ignore flag. System will by pass authentication", 2);
		$_SESSION['ignore'] = 1;
	}

	public function get_token () {
		$this->debug->log("[INFO] Retrieving token for currently logged in user [TOKEN] " . json_encode($_SESSION[self::$token_key]) , 5);
		return isset ($_SESSION[self::$token_key]) ? $_SESSION[self::$token_key] : null;
	}

	public function get_user () {
		$this->debug->log("[INFO] Retrieving user data for the currently active token [USER_DATA] " . json_encode($_SESSION[$_SESSION[self::$token_key]]), 5);
		$token = isset ($_SESSION[self::$token_key]) ? $_SESSION[self::$token_key] : null;
		return $token !== null ? $_SESSION[$token] : $this->auth_error;
	}

	public function isAuthorized () {
		$ignore = isset($_SESSION['ignore']) ? $_SESSION['ignore'] : 0;
		if ($ignore) $this->debug->log("[INFO] Authentication ignore flag is set. By Passing Authentication Params", 2);
		$headers = getallheaders();
		$_SESSION['ignore'] = 0;

		$this->debug->log("[INFO] Retrieved Headers object " . json_encode($headers), 4);
		$this->debug->log("[INFO] SESSION TOKEN " . (isset($_SESSION[self::$token_key]) ? "is" : "not") . " set", 5);
		$this->debug->log("[INFO] Matching TOKEN " . (isset($_SESSION[self::$token_key]) ? $_SESSION[self::$token_key] : "NULL") 
			. "against " . (isset($headers[self::$token_key]) ? $headers[self::$token_key] : "NULL"), 5);

		if ((isset($_SESSION[self::$token_key]) && isset($headers[self::$token_key]) && $headers[self::$token_key] === $_SESSION[self::$token_key]))
			$this->debug->log("[INFO] Authentication Passed. Access is authorized", 3);

		return (isset($_SESSION[self::$token_key]) && isset($headers[self::$token_key]) 
			&& $headers[self::$token_key] === $_SESSION[self::$token_key]) || $ignore;
	}

	public function authorize_action ($table, $data, $attrs) {
		
		$authorized = false;
		switch ($_SERVER['REQUEST_METHOD']) {

			case 'POST':
				$authorized = $this->authorize_post($table, $data, $attrs);
				break;

			case 'PUT':
				$authorized = $this->authorize_put($table, $data, $attrs);
				break;

			case 'DELETE':
				$authorized = $this->authorize_delete($table, $data, $attrs);
				break;
		}

		return $authorized;
	}

	private function authorize_post ($table, $data, $attrs) {
		$authorized = true;
		foreach ($attrs as $key => $value) {
			if ($authorized && isset($value['authorize'])) {
				$user = $this->get_user();
				$authorized = ($data[$key] == $user[$key]);
			}
		}

		return $authorized; 
	}

	private function authorize_put ($table, $data, $attrs) {

		$this->db = new DbConn();
		$this->db->conn();
		$query = "select * from " . $table . " where";
		$and = " ";
		foreach ($attrs as $key => $value) {
			if (isset($value['authToken']) && $value['authToken']) {
				$query = $query . $and . $key . " = '" . $this->db->escape($data[$key]) . "'";
				$and = " and ";
			}
		}

		$result = json_decode($this->db->select($query), true);
		$authorized = count($result) > 0;
		foreach ($attrs as $key => $value) {
		 	if ($authorized && isset($value['authorize'])) {
		 		$user = $this->get_user();
		 		$authorized = ($result[0][$key] == $user[$key]);
		 	}
		}

		$this->db->close();
		return $authorized;
	}

	private function authorize_delete ($table, $data, $attrs) {
		return false;
	}

	public function destroy_token () {
		session_destroy();
	}
}