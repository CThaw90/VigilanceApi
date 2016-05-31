<?php

class User extends Entity {
	
	private $GET_CRED_BY_ID = 'select * from credential where credential_id = ${1}';
	private $UPDATE_CRED_BY_ID = 'credential_id = ${1}';

	private $GET_ALL = "select * from credential";
	private $DELETE_USER = 'credential_id = ${1}';

	protected $attrs = array (
		"password" => true, "age" => true, "email" => true, 
		"img_src" => true, "user_type" => true, "username" => true, 
		"name" => true
	);

	protected $table = "credential";
	protected $error;
	protected $db;

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
		return parent::create($data);
	}

	public function update ($data) {
		$status = null;
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

	public function delete ($id) {
		return $this->db->delete("credential", preg_replace("/(\d+)/", $this->DELETE_USER, $id)) ? 
			'{"status": 200, "message": "User deleted"}' : '{"status": 500, "message": "User could not be deleted"}';
	}

	private function update_by_id ($data) {
		return $this->db->update("credential", $this->transform($data, $this->attrs, false), 
			preg_replace("/(\d+)/", $this->UPDATE_CRED_BY_ID, $data['credential_id'])) ? 
				'{"status": 200, "message": "User updated successfully"}' :
				'{"status": 500, "message": "User update failed"}';
	}

	function __destruct () {
		$this->db->close();
	}
}