<?php

class Authentication {

	private $get_user_by_token = 'select c.* from credential c join authentication a on c.credential_id = a.credential_id where a.token = ';
	private $auth_error = '{"status": 403, "error": "Permission Denied. You do not have access to this resource"}';
	private $check_for_auth = 'select * from authentication where credential_id = ${1}';
	private $debug;
	private $db;

	private static $token_key = "Token";
	private static $cache;

	public function __construct () {
		$this->debug = new Debugger("Authentication.php");
	}

	public function active () {
		$headers = getallheaders();
		return isset($headers[self::$token_key]);
	}

	public function generate_token ($user) {
		$token = $this->user_is_logged_in($user["credential_id"]);
		if (!$token) {
			$this->debug->log("[INFO] No token for current user exists. Generating a new token", 5);
			$token = sha1(json_encode($user) . time());
			$this->db = new DbConn();
			$this->db->conn();
			$this->db->insert("authentication", array("credential_id" => $user["credential_id"], "token" => $token));
		}

		return $token;
	}

	public function store_cache ($data) {
		self::$cache = $data;
	}

	public function get_cache () {
		return self::$cache;
	}

	public function get_user ($token) {
		$headers = getallheaders();
		$token = $token ? $token : $headers[self::$token_key];
		$this->db = new DbConn();
		$this->db->conn();
		$this->db->bypass_auth();
		$result = json_decode($this->db->select($this->get_user_by_token . "'" . $token . "'"), true);
		if (isset($result[0])) {
			$this->debug->log("[INFO] Retrieving user data for the currently active token [USER_DATA] " . $token, 5);
			return $result[0];
		}

		return $this->auth_error;
	}

	public function isAuthorized () {
		$headers = getallheaders();
		if (!isset($headers[self::$token_key])) {
			return false;
		}

		$this->debug->log("[INFO] Retrieved Headers object " . json_encode($headers), 4);
		$this->db = new DbConn();
		$this->db->conn();
		$this->db->bypass_auth();
		$result = json_decode($this->db->select($this->get_user_by_token . "'" . $headers[self::$token_key] . "'"), true);
		if (isset($result[0])) {
			$this->debug->log("[INFO] Authentication Passed. Access is authorized", 3);
			return true;
		}

		return false;
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
				$user = $this->get_user(false);
				$authorized = ($data[$key] == $user[$key]);
			}
		}

		return $authorized; 
	}

	public function authorize_put ($table, $data, $attrs) {

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
		$authorized = isset($result[0]);
		foreach ($attrs as $key => $value) {
		 	if ($authorized && isset($value['authorize'])) {
		 		$user = $this->get_user(false);
		 		$authorized = ($result[0][$key] == $user[$key]);
		 	}
		}

		$this->db->close();
		return $authorized;
	}

	private function authorize_delete ($table, $data, $attrs) {
		return $this->authorize_put($table, $data, $attrs);
	}

	private function user_is_logged_in ($id) {
		$this->db = new DbConn();
		$this->db->conn();
		$this->db->bypass_auth();
		$result = json_decode($this->db->select(preg_replace("/(\d+)/", $this->check_for_auth, $id)), true);
		if (isset($result[0])) {
			$auth = $result[0];
			$this->debug->log("[INFO] This user is already logged in with token " . $auth["token"], 5);
			return $auth["token"];
		}

		return false;
	}

	public function destroy_token () {
		$headers = getallheaders();
		if ($this->active()) {
			$this->db = new DbConn();
			$this->db->conn();
			$token = json_decode($this->db->select ("select * from authentication where token = '" . $headers[self::$token_key] . "'"), true);
			if (isset($token[0])) {
				$this->debug->log("[INFO] User token found. Destroying token " . $headers[self::$token_key], 5);
				$this->db->delete("authentication", "credential_id = " . $token[0]["credential_id"]);

				return true;
			}
		}

		$this->debug->log("[INFO] No User token found with string identifier " . $headers[self::$token_key], 5);
		return false;
	}
}