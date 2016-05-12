<?php

class Comment {
	
	private $GET_COMMENT_BY_ID = 'select * from comment where comment_id = ${1}';
	private $GET_ALL = "select * from comment";
	private $UPDATE_COMMENT_BY_ID = 'comment_id = ${1}';
	private $DELETE_COMMENT = 'comment_id = ${1}';

	private $attrs = array("text" => true, "comment_id" => false, "post_id" => false);
	private $error;
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
		$data = json_decode($data, true);
		if ($data === null) {
			$status = '{"status": 500, "messsage": "Invalid data body object"}';
		} else if ($this->validate_object($data)) {
			$status = $this->db->insert("comment", $this->transform($data, true)) ? 
				'{"status": 200, "message": "New comment created"}' :
				'{"status": 500, "message": "Could not complete user insertion query"}';
		}
		else {
			return $this->error;
		}

		return $status;
	}

	public function update ($data) {
		$status = "";
		$data = json_decode($data, true);
		if ($data === null) {
			$status = '{"status": 500, "message": "Invalid data body object"}';
		}
		else if (isset($data['comment_id'])) {
			$status = $this->update_by_id($data);
		}
		else {
			$status = '{"status": 500, "message": "Comment Update failed. No update type declaration."}';
		}

		return $status;
	}

	public function delete($id) {
		return $this->db->delete("comment", preg_replace("/(\d+)/", $this->DELETE_COMMENT, $id)) ? 
			'{"status": 200, "message": "Comment deleted"}' : '{"status": 500, "message": "Comment could not be deleted"}';
	}

	private function update_by_id ($data) {
		return $this->db->update("comment", $this->transform($data, false),
			preg_replace("/(\d+)/", $this->UPDATE_COMMENT_BY_ID, $data['comment_id'])) ?
				'{"status": 200, "message": "Comment updated successfully"}' :
				'{"status": 500, "message": "Comment update failed"}';		
	}

	private function validate_object($data) {
		$valid = true;
		foreach ($this->attrs as $key => $value) {
			if (!isset($data[$key])) {
				$this->error = '{"status": 500, "message": "' . $key . ' field is missing"}';
				$valid = false;
			}
		}

		return $valid;
	}

	private function transform ($data, $new) {
		$transformed_object = array();
		foreach ($this->attrs as $key => $update) {
			if (isset($data[$key]) && ($new || $update)) {
				$transformed_object[$key] = $data[$key];
			}
		}

		return $transformed_object;
	}

	function __destruct() {
		$this->db->close();
	}
}