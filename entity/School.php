<?php

class School extends Entity {
	
	private $GET_SCHOOL_BY_ID = 'select * from school where school_id = ${1}';
	private $GET_ALL = "select * from school;";
	private $UPDATE_BY_ID = 'school_id = ${1}';
	private $DELETE_SCHOOL = 'school_id = ${1}';

	protected $attrs = array(
		"name" => true, 
		"display_name" => true, 
		"email" => true, 
		"city" => true, 
		"img_src" => true
	);

	protected $table = "school";
	protected $error;
	protected $db;

	public function __construct() {
		$this->db = new DbConn();
		$this->db->conn();
	}

	public function get_all() {
		return $this->db->select($this->GET_ALL);
	}

	public function get_by_id ($id) {
		return $this->db->select(preg_replace("/(\d+)/", $this->GET_SCHOOL_BY_ID, $id));
	}

	public function create ($data) { // name, display_name, email, city, img_src
		return parent::create($data);
	}

	public function update ($data) {
		$status = "";
		$data = json_decode($data, true);
		if ($data === null) {
			$status = '{"status": 500, "message": "Invalid data body object"}';
		}
		else if (isset($data['school_id'])) {
			$status = $this->update_by_id($data);
		}
		else {
			$status = '{"status": 500, "message": "School Update failed. No update type declaration."}';
		}

		return $status;
	}

	public function delete ($id) {
		return $this->db->delete("school", preg_replace("/(\d+)/", $this->DELETE_SCHOOL, $id)) ? 
			'{"status": 200, "message": "School deleted"}' : '{"status": 500, "message": "School could not be deleted"}';
	}

	private function update_by_id ($data) {
		return $this->db->update("school", $this->transform($data, $this->attrs, false), 
			preg_replace("/(\d+)/", $this->UPDATE_BY_ID, $data['school_id'])) ? 
				'{"status": 200, "message": "School updated successfully"}' :
				'{"status": 500, "message": "School update failed"}';
	}

	function __destruct() {
		$this->db->close();
	}
}