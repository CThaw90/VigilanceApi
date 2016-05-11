<?php

class User {
	
	private $GET_CRED_BY_ID = 'select * from credential where credential_id = ${1}';
	private $GET_ALL = "select * from credential";
	private $DELETE_USER = 'credential_id = ${1}';
	private $db;

	private $error;

	public function __construct () {
		$this->db = new DbConn();
		$this->db->conn();
	}

	public function get_all () {
		return $this->db->select($this->GET_ALL);
	}

	public function get_by_id ($id) {
		return $this->db->select(preg_replace("/(\d+)/", $this->GET_CRED_BY_ID, $id));
	}

	public function create ($data) {
		$status = '';
		$data = json_decode($data);
		if ($data === null) {
			$status = '{"status": 500, "messsage": "Invalid data body object"}';
		} else if ($this->validate_object($data)) {
			$status = $this->db->insert("credential", $data) ? 
				'{"status": 200, "message": "New user created"}' :
				'{"status": 500, "message": "Could not complete user insertion query"}';
		}
		else {
			return $this->error;
		}

		return $status;
	}

	public function delete ($id) {
		return $this->db->delete("credential", preg_replace("/(\d+)/", $this->DELETE_USER, $id)) ? 
			'{"status": 200, "message": "User deleted"}' : '{"status": 500, "message": "User could not be deleted"}';
	}

	private function validate_object($data) {
		$valid = false;
		if (!isset($data->email)) {
			$this->error = '{"status": 500, "message": "Email field is missing"}';
		}
		else if (!isset($data->user_type)) {
			$this->error = '{"status": 500, "message": "User Type field is missing"}';
		}
		else if (!isset($data->age)) {
			$this->error = '{"status": 500, "message": "Age field is missing"}';
		}
		else if (!isset($data->username)) {
			$this->error = '{"status": 500, "message": "Username field is missing"}';
		}
		else if (!isset($data->password)) {
			$this->error = '{"status": 500, "message": "Password field is missing"}';
		}
		else {
			$valid = true;
		}

		return $valid;
	}

	function __destruct () {
		$this->db->close();
	}
}