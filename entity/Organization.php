<?php

class Organization {

	private $GET_ALL = "select * from organization";
	private $GET_ORG_BY_ID = 'select * from organization where organization_id = ${1}';
	private $DELETE_ORG = 'organization_id = ${1}';
	private $db;
	public function __construct () {
		$this->db = new DbConn();
		$this->db->conn();
	}

	public function get_all () {
		return $this->db->select($this->GET_ALL);
	}

	public function get_by_id ($id) {
		return $this->db->select(preg_replace("/(\d+)/", $this->GET_ORG_BY_ID, $id));
	}

	public function create ($data) {
		$status = '';
		$data = json_decode($data);
		if ($data === null) {
			$status = '{"status": 500, "messsage": "Invalid data body object"}';
		} else if ($this->validate_object($data)) {
			$status = $this->db->insert("organization", $data) ? 
				'{"status": 200, "message": "New organization created"}' :
				'{"status": 500, "message": "Could not complete organization insertion query"}';
		}
		else {
			return $this->error;
		}

		return $status;
	}

	public function delete ($id) {
		return $this->db->delete("organization", preg_replace("/(\d+)/", $this->DELETE_ORG, $id)) ? 
			'{"status": 200, "message": "Organization deleted"}' : '{"status": 500, "message": "Organization could not be deleted"}';
	}

	private function validate_object($data) {
		$valid = false;
		if (!isset($data->name)) {
			$this->error = '{"status": 500, "message": "name field is missing"}';
		}
		else if (!isset($data->display_name)) {
			$this->error = '{"status": 500, "message": "display_name field is missing"}';
		}
		else if (!isset($data->email)) {
			$this->error = '{"status": 500, "message": "email field is missing"}';
		}
		else if (!isset($data->img_src)) {
			$this->error = '{"status": 500, "message": "img_src field is missing"}';
		}
		else if (!isset($data->city)) {
			$this->error = '{"status": 500, "message": "city field is missing"}';
		}
		else {
			$valid = true;
		}

		return $valid;
	}

	function __destruct() {
		$this->db->close();
	}
}