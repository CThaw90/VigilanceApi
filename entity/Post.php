<?php

class Post extends Entity {
	
	private $GET_ALL = "select * from post p inner join credential u on u.credential_id = p.credential_id";
	private $GET_POST_BY_ID = 'select * from post where post_id = ${1}';
	private $UPDATE_BY_ID = 'post_id = ${1}';
	private $DELETE_POST = 'post_id = ${1}';

	protected $attrs = array(
		"text" => array("canUpdate" => true, "needAuth" => false),
		"media" => array("canUpdate" => true, "needAuth" => false),
		"credential_id" => array("canUpdate" => true, "authorize" => true),
		"post_id" => array("canUpdate" => true, "needAuth" => false, "authToken" => true)
	);
	protected $table = "post";
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
		return $this->db->select(preg_replace("/(\d+)/", $this->GET_POST_BY_ID, $id));
	}

	public function create ($data) {
		return $this->isAuthorized($data, $this->attrs) ? parent::create($data) : $this->auth_error;
	}

	public function update ($data) {
		$status = null;

		$data = json_decode($data, true);
        if ($data === null) {
			$status = '{"status": 500, "message": "Invalid data body object"}';
		}
		else if (isset($data['post_id'])) {
			$status = $this->isAuthorized($data, $this->attrs) ? $this->update_by_id($data) : $this->auth_error;
		}
		else {
			$status = '{"status": 500, "message": "Post Update failed. No update type declaration."}';
		}

		return $status;
	}

	public function delete ($id) {
		if ($this->isAuthorized(array("post_id"))) {
			return $this->db->delete("post", preg_replace("/(\d+)/", $this->DELETE_POST, $id)) ? 
				'{"status": 200, "message": "Post deleted"}' : '{"status": 500, "message": "Post could not be deleted"}';
		}

		return $this->auth_error;
	}

	private function update_by_id ($data) {
		return $this->db->update("post", $this->transform($data, $this->attrs, false), 
			preg_replace("/(\d+)/", $this->UPDATE_BY_ID, $data['post_id'])) ? 
				'{"status": 200, "message": "Post updated successfully"}' :
				'{"status": 500, "message": "Post update failed"}';
	}

	function __destruct() {
		$this->db->close();
	}
}