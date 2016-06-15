<?php

class Organization extends Entity {

	private $GET_ALL = "select * from organization";
	private $GET_ORG_BY_ID = 'select * from organization where organization_id = ${1}';
	
	protected $DELETE_BY_ID = 'organization_id = ${1}';
	protected $UPDATE_BY_ID = 'organization_id = ${1}';
	protected $attrs = array(
		"name" => array("canUpdate" => true, "needAuth" => false),
		"credential_id" => array("canUpdate" => true, "authorize" => true),
		"display_name" => array("canUpdate" => true, "needAuth" => false),
		"city" => array("canUpdate" => true, "needAuth" => false),
		"email" => array("canUpdate" => true, "needAuth" => false),
		"img_src" => array("canUpdate" => false, "needAuth" => false, "fileUpload" => true),
		"organization_id" => array("canUpdate" => true, "needAuth" => false, "authToken" => true, "postIgnore" => true)
	);
	protected $table = "organization";
	protected $error;
	protected $db;

	private $debug;

	public function __construct () {
		$this->debug = new Debugger("Organization.php");
		$this->db = new DbConn();
		$this->db->conn();

		parent::__construct();
	}

	public function get_all () {
		return $this->db->select($this->GET_ALL);
	}

	public function get_by_id ($id) {
		return $this->db->select(preg_replace("/(\d+)/", $this->GET_ORG_BY_ID, $id));
	}

	public function create ($data) {
		return parent::create($data);
	}

	public function update ($data, $updateBy) {
		return parent::update($data, $updateBy);
	}

	public function delete ($id, $deleteBy) {
		return parent::delete($id, $deleteBy);
	}

	function __destruct() {
		$this->db->close();
	}
}