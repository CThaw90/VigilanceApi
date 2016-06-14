<?php

class Course extends Entity {

	private $GET_COURSE_BY_ID = 'select * from course where course_id = ${1}';
	private $GET_ALL = "select * from course";
	
	protected $DELETE_BY_ID = 'course_id = ${1}';
	protected $UPDATE_BY_ID = 'course_id = ${1}';
	protected $attrs = array(
		"school_id" => array("canUpdate" => false, "needAuth" => false),
		"credential_id" => array("canUpdate" => false, "authorize" => true),
		"start_time" => array("canUpdate" => true, "needAuth" => false),
		"end_time" => array("canUpdate" => true, "needAuth" => false),
		"name" => array("canUpdate" => true, "needAuth" => false),
		"course_id" => array("canUpdate" => false, "needAuth" => false, "authToken" => true, "postIgnore" => true)
	);

	protected $table = "course";
	protected $error;
	protected $db;

	private $debug;

	public function __construct () {
		$this->debug = new Debugger("Course.php");
		$this->db = new DbConn();
		$this->db->conn();

		parent::__construct();
	}

	public function get_all () {
		return $this->db->select($this->GET_ALL);
	}


	public function get_by_id ($id) {
		return $this->db->select(preg_replace("/(\d+)/", $this->GET_COURSE_BY_ID, $id));
	}

	public function create ($data) {
		return parent::create($data);
	}

	public function update($data, $updateBy) {
		return parent::update($data, $updateBy);
	}

	public function delete ($id, $deleteBy) {
		return parent::delete($id, $deleteBy);
	}

	function __destruct() {
		$this->db->close();
	}
}