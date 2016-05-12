<?php

class User {
	
	private $GET_CRED_BY_ID = 'select * from credential where credential_id = ${1}';
	private $UPDATE_CRED_BY_ID = 'where credential_id = ${1}';

	private $GET_ALL = "select * from credential";
	private $DELETE_USER = 'credential_id = ${1}';

	private $attrs = array ("password", "age", "email", "img_src", "user_type", "username", "name");
	private $error;
	private $db;

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
		$data = json_decode($data, true);
		if ($data === null) {
			$status = '{"status": 500, "messsage": "Invalid data body object"}';
		} else if ($this->validate_object($data)) {
			$status = $this->db->insert("credential", $this->transform($data)) ? 
				'{"status": 200, "message": "New user created"}' :
				'{"status": 500, "message": "Could not complete user insertion query"}';
		}
		else {
			return $this->error;
		}

		return $status;
	}

	public function update ($data) {
		$status = "";
		$data = json_decode($data, true);
		if ($data === null) {
			$status = '{"status": 500, "message": "Invalid data body object"}';
		}
		else if (isset($data['credential_id'])) {
			$status = $this->update_by_id($data);
		}
		else {
			$status = '{"status": 500, "message": "User Update failed. No update type declaration."}';
		}

		return $status;
	}

	private function update_by_id ($data) {
		return $this->db->update("credential", $this->transform($data), 
			preg_replace("/(\d+)/", $this->UPDATE_CRED_BY_ID, $data['credential_id'])) ? 
				'{"status": 200, "message": "User updated successfully"}' :
				'{"status": 500, "message": "User update failed"}';
	}

	public function delete ($id) {
		return $this->db->delete("credential", preg_replace("/(\d+)/", $this->DELETE_USER, $id)) ? 
			'{"status": 200, "message": "User deleted"}' : '{"status": 500, "message": "User could not be deleted"}';
	}

	private function validate_object($data) {
		$valid = true;
		for ($key = 0; $key < count($this->attrs) && $valid; $key++) {
			if (!isset($data[$this->attrs[$key]])) {
				$this->error = '{"status": 500, "message": "' . $this->attrs[$key] . ' field is missing"}';
				$valid = false;
			}
		}

		return $valid;
	}

	private function transform ($data) {
		$transformed_object = array();
		for ($key = 0; $key < count($this->attrs); $key++) {
			if (isset($data[$this->attrs[$key]])) {
				$transformed_object[$this->attrs[$key]] = $data[$this->attrs[$key]];
			}
		}

		return $transformed_object;
	}

	function __destruct () {
		$this->db->close();
	}
}