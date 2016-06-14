<?php

class Post extends Entity {
	
	private $GET_ALL = "select * from post p inner join credential u on u.credential_id = p.credential_id";
	private $GET_POST_BY_ID = 'select * from post where post_id = ${1}';
	private $DELETE_POST = 'post_id = ${1}';

	protected $UPDATE_BY_ID = 'post_id = ${1}';
	protected $attrs = array(
		"text" => array("canUpdate" => true, "needAuth" => false),
		"media" => array("canUpdate" => true, "needAuth" => false, "fileUpload" => true),
		"credential_id" => array("canUpdate" => true, "authorize" => true),
		"post_id" => array("canUpdate" => true, "needAuth" => false, "authToken" => true, "postIgnore" => true)
	);
	protected $table = "post";
	protected $error;
	protected $db;

	private $debug;

	public function __construct () {
		$this->debug = new Debugger("Post.php");
		$this->db = new DbConn();
		$this->db->conn();

		parent::__construct();
	}

	public function get_all () {
		return $this->db->select($this->GET_ALL);
	}

	public function get_by_id ($id) {
		return $this->db->select(preg_replace("/(\d+)/", $this->GET_POST_BY_ID, $id));
	}

	public function create ($data) {
		return parent::create($data);
	}

	public function update ($data, $updateBy) {
		return parent::update($data, $updateBy);
	}

	public function delete ($id) {
		if ($this->isAuthorized(array("post_id" => $id), $this->attrs)) {
			return $this->db->delete("post", preg_replace("/(\d+)/", $this->DELETE_POST, $id)) ? 
				'{"status": 200, "message": "Post deleted"}' : '{"status": 500, "message": "Post could not be deleted"}';
		}

		return $this->auth_error;
	}

	function __destruct() {
		$this->db->close();
	}
}