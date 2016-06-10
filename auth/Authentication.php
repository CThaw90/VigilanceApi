<?php

session_start();

class Authentication {

	private $auth_error = '{"status": 403, "error": "Permission Denied. You do not have access to this resource"}';
	private $db;

	public function generate_token ($user) {
		if (!isset($_SESSION['token'])) {
			$token = sha1(json_encode($user) . time());
			$_SESSION['token'] = $token;
			$_SESSION[$token] = $user;
		}
	}

	public function ignore () {
		$_SESSION['ignore'] = 1;
	}

	public function get_token () {
		return isset ($_SESSION['token']) ? $_SESSION['token'] : null;
	}

	public function get_user () {
		$token = isset ($_SESSION['token']) ? $_SESSION['token'] : null;
		return $token !== null ? $_SESSION[$token] : $this->auth_error;
	}

	public function isAuthorized () {
		$ignore = isset($_SESSION['ignore']) ? $_SESSION['ignore'] : 0;
		$headers = getallheaders();
		$_SESSION['ignore'] = 0;

		return (isset($_SESSION['token']) && isset($headers['token']) 
			&& $headers['token'] === $_SESSION['token']) || $ignore;
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