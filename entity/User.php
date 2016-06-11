<?php

class User extends Entity {
	
	private $GET_CRED_BY_ID = 'select * from credential where credential_id = ${1}';
	private $UPDATE_CRED_BY_ID = 'credential_id = ${1}';

	private $GET_ALL = "select * from credential";
	private $DELETE_USER = 'credential_id = ${1}';

	protected $attrs = array (
		"password" => array("canUpdate" => true, "needAuth" => true),
		"age" => array("canUpdate" => true, "needAuth" => false),
		"email" => array("canUpdate" => true, "needAuth" => false),
		"img_src" => array("canUpdate" => true, "needAuth" => false),
		"name" => array("canUpdate" => true, "needAuth" => false),
		"user_type" => array("canUpdate" => false, "needAuth" => false),
		"username" => array("canUpdate" => true, "needAuth" => false),
		"credential_id" => array("canUpdate" => false, "authorize" => true, "authToken" => true, "postIgnore" => true)
	);

	protected $table = "credential";
	protected $error;
	protected $db;

	private $debug;

	public function __construct () {
		$this->debug = new Debugger("User.php");
		$this->db = new DbConn();
		$this->db->conn();

		parent::__construct();
	}

	public function get_all () {
		return $this->db->select($this->GET_ALL);
	}

	public function get_by_id ($id) {
		return $this->db->select(preg_replace("/(\d+)/", $this->GET_CRED_BY_ID, $id));
	}

	public function create ($data) {
		$this->no_auth = true;
		return parent::create($data);
	}

	public function update ($data) {
		$status = null;
		$data = $this->parse_request_body($data);
		if ($data === null || !count($data)) {
			$status = '{"status": 500, "message": "Invalid data body object"}';
		}
		else if (isset($data['credential_id'])) {
			$status = $this->isAuthorized($data, $this->attrs) ? $this->update_by_id($data) : $this->auth_error;
		}
		else {
			$status = '{"status": 500, "message": "User Update failed. No update type declaration."}';
		}

		return $status;
	}

	public function delete ($id) {
		if ($this->isAuthorized(array('credential_id' => $id))) {
			return $this->db->delete("credential", preg_replace("/(\d+)/", $this->DELETE_USER, $id)) ? 
				'{"status": 200, "message": "User deleted"}' : '{"status": 500, "message": "User could not be deleted"}';
		}

		return $this->auth_error;
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