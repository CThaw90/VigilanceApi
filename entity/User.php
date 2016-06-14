<?php

class User extends Entity {
	
	private $GET_CRED_BY_ID = 'select * from credential where credential_id = ${1}';
	private $GET_ALL = "select * from credential";
	private $DELETE_USER = 'credential_id = ${1}';

	protected $UPDATE_BY_ID = 'credential_id = ${1}';
	protected $attrs = array (
		"password" => array("canUpdate" => true, "needAuth" => true),
		"age" => array("canUpdate" => true, "needAuth" => false),
		"email" => array("canUpdate" => true, "needAuth" => false),
		"img_src" => array("canUpdate" => false, "needAuth" => false, "fileUpload" => true),
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
		$this->bypass_auth($this->parse_request_body($data));
		return parent::create($data);
	}

	public function update ($data, $updateBy) {
		return parent::update($data, $updateBy);
	}

	public function delete ($id, $deleteBy) {
		return parent::delete($id, $deleteBy);
	}

	function __destruct () {
		$this->db->close();
	}
}