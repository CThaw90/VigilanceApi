<?php

class Comment extends Entity {
	
	private $GET_COMMENT_BY_ID = 'select * from comment where comment_id = ${1}';
	private $GET_ALL = "select * from comment";
	
	protected $DELETE_BY_ID = 'comment_id = ${1}';
	protected $UPDATE_BY_ID = 'comment_id = ${1}';
	protected $attrs = array(
		"text" => array("canUpdate" => true, "needAuth" => false),
		"credential_id" => array("canUpdate" => false, "authorize" => true),
		"post_id" => array("canUpdate" => false, "needAuth" => false),
		"comment_id" => array("canUpdate" => false, "needAuth" => false, "authToken" => true, "postIgnore" => true),
	);

	protected $table = "comment";
	protected $error;
	protected $db;

	private $debug;

	public function __construct () {
		$this->debug = new Debugger("Comment.php");
		$this->db = new DbConn();
		$this->db->conn();

		parent::__construct();
	}

	public function get_all () {
		return $this->db->select($this->GET_ALL);
	}

	public function get_by_id ($id) {
		return $this->db->select(preg_replace("/(\d+)/", $this->GET_COMMENT_BY_ID, $id));
	}

	public function create ($data) {
		return parent::create($data);
	}

	public function update ($data, $updateBy) {
		return parent::update($data, $updateBy);
	}

	public function delete($id, $deleteBy) {
		return parent::delete($id, $deleteBy);
	}

	function __destruct() {
		$this->db->close();
	}
}