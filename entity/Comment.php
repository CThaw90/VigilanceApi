<?php

class Comment extends Entity {
	
	private $GET_COMMENT_BY_ID = 'select * from comment where comment_id = ${1}';
	private $GET_ALL = "select * from comment";
	private $UPDATE_COMMENT_BY_ID = 'comment_id = ${1}';
	private $DELETE_COMMENT = 'comment_id = ${1}';

	protected $attrs = array("text" => true, "credential_id" => false, "post_id" => false);
	protected $table = "comment";
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
		return $this->db->select(preg_replace("/(\d+)/", $this->GET_COMMENT_BY_ID, $id));
	}

	public function create ($data) { // post_id, credential_id, text
		return parent::create($data);
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
		return $this->db->update("comment", $this->transform($data, $this->attrs, false),
			preg_replace("/(\d+)/", $this->UPDATE_COMMENT_BY_ID, $data['comment_id'])) ?
				'{"status": 200, "message": "Comment updated successfully"}' :
				'{"status": 500, "message": "Comment update failed"}';		
	}

	function __destruct() {
		$this->db->close();
	}
}