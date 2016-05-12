<?php

class Post {
	
	private $GET_ALL = "select * from post p inner join credential u on u.credential_id = p.credential_id";
	private $GET_POST_BY_ID = 'select * from post where post_id = ${1}';
	private $UPDATE_BY_ID = 'post_id = ${1}';
	private $DELETE_POST = 'post_id = ${1}';

	private $attrs = array("text" => true, "media" => true, "credential_id" => false);
	
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
		return $this->db->select(preg_replace("/(\d+)/", $this->GET_POST_BY_ID, $id));
	}

	public function create ($data) {
		$status = "";
		$data = json_decode($data, true);
		if ($data === null) {
			$status = '{"status": 500, "message": "Invalid data body object"}';
		} else if ($this->validate_object($data)) { // text, media, credential_id
			$status = $this->db->insert("post", $this->transform($data, true)) ?
				'{"status": 200, "message": "New post created"}' :
				'{"status": 500, "message": "Could not complete post insertion query"}';
		} else {
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
		else if (isset($data['post_id'])) {
			$status = $this->update_by_id($data);
		}
		else {
			$status = '{"status": 500, "message": "Post Update failed. No update type declaration."}';
		}

		return $status;
	}

	public function delete ($id) {
		return $this->db->delete("post", preg_replace("/(\d+)/", $this->DELETE_POST, $id)) ? 
			'{"status": 200, "message": "Post deleted"}' : '{"status": 500, "message": "Post could not be deleted"}';
	}

	private function update_by_id ($data) {
		return $this->db->update("post", $this->transform($data, false), 
			preg_replace("/(\d+)/", $this->UPDATE_BY_ID, $data['post_id'])) ? 
				'{"status": 200, "message": "Post updated successfully"}' :
				'{"status": 500, "message": "Post update failed"}';
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