<?php

class Post {
	
	private $GET_ALL = "select * from post p inner join credential u on u.credential_id = p.credential_id";
	private $GET_POST_BY_ID = 'select * from post where post_id = ${1}';
	private $DELETE_POST = 'post_id = ${1}';
	private $db;

	private $error;

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
		$status = "";
		$data = json_decode($data);
		if ($data === null) {
			$status = '{"status": 500, "message": "Invalid data body object"}';
		} else if ($this->validate_object($data)) { // text, media, credential_id
			$status = $this->db->insert("post", $data) ?
				'{"status": 200, "message": "New post created"}' :
				'{"status": 500, "message": "Could not complete post insertion query"}';
		} else {
			return $this->error;
		}

		return $status;
	}

	public function delete ($id) {
		return $this->db->delete("post", preg_replace("/(\d+)/", $this->DELETE_POST, $id)) ? 
			'{"status": 200, "message": "Post deleted"}' : '{"status": 500, "message": "Post could not be deleted"}';
	}

	private function validate_object($data) {
		$valid = false;
		if (!isset($data->text)) {
			$this->error = '{"status": 500, "message": "Text field is missing"}';
		}
		else if (!isset($data->media)) {
			$this->error = '{"status": 500, "message": "Media field is missing"}';
		}
		else if (!isset($data->credential_id)) {
			$this->error = '{"status": 500, "message": "Credential id is missing"}';
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