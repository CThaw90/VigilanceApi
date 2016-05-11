<?php

class Comment {
	
	private $GET_COMMENT_BY_ID = 'select * from comment where comment_id = ${1}';
	private $GET_ALL = "select * from comment";
	private $DELETE_COMMENT = 'comment_id = ${1}';
	private $db;
	public function __construct () {
		$this->db = new DbConn();
		$this->db->conn();
	}

	public function get_all () {
		return $this->db->select($this->GET_ALL);
	}

	public function get_by_id ($id) {
		return $this->db->select(preg_replace("/(\d+)/", $this->GET_COMMENT_BY_ID, $id));
	}

	public function create ($data) { // post_id, credential_id, text
		$status = '';
		$data = json_decode($data);
		if ($data === null) {
			$status = '{"status": 500, "messsage": "Invalid data body object"}';
		} else if ($this->validate_object($data)) {
			$status = $this->db->insert("comment", $data) ? 
				'{"status": 200, "message": "New comment created"}' :
				'{"status": 500, "message": "Could not complete user insertion query"}';
		}
		else {
			return $this->error;
		}

		return $status;
	}

	public function delete($id) {
		return $this->db->delete("comment", preg_replace("/(\d+)/", $this->DELETE_COMMENT, $id)) ? 
			'{"status": 200, "message": "Comment deleted"}' : '{"status": 500, "message": "Comment could not be deleted"}';
	}

	private function validate_object($data) {
		$valid = false;
		if (!isset($data->post_id)) {
			$this->error = '{"status": 500, "message": "Post_id field is missing"}';
		}
		else if (!isset($data->credential_id)) {
			$this->error = '{"status": 500, "message": "Credential_id field is missing"}';
		}
		else if (!isset($data->text)) {
			$this->error = '{"status": 500, "message": "Text field is missing"}';
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