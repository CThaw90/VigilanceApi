<?php

class Course extends Entity {

	private $GET_COURSE_BY_ID = 'select * from course where course_id = ${1}';
	private $GET_ALL = "select * from course";
	private $DELETE_COURSE = 'course_id = ${1}';
	private $UPDATE_COURSE_BY_ID = 'course_id = ${1}';

	protected $attrs = array(
		"school_id" => false, "credential_id" => false, "name" => true, 
		"start_time" => true, "end_time" => true
	);
	
	protected $table = "course";
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
		return $this->db->select(preg_replace("/(\d+)/", $this->GET_COURSE_BY_ID, $id));
	}

	public function create ($data) {
		return parent::create($data);
	}

	public function update ($data) {
		$status = null;
		$data = json_decode($data, true);
		if ($data === null) {
			$status = '{"status": 500, "message": "Invalid data body object"}';
		}
		else if (isset($data['course_id'])) {
			$status = $this->update_by_id($data);
		}
		else {
			$status = '{"status": 500, "message": "Course Update failed. No update type declaration."}';
		}

		return $status;
	}

	public function delete ($id) {
		return $this->db->delete("course", preg_replace("/(\d+)/", $this->DELETE_COURSE, $id)) ? 
			'{"status": 200, "message": "Course deleted"}' : '{"status": 500, "message": "Course could not be deleted"}';
	}

	private function update_by_id ($data) {
		return $this->db->update("course", $this->transform($data, $this->attrs, false), 
			preg_replace("/(\d+)/", $this->UPDATE_COURSE_BY_ID, $data['course_id'])) ? 
				'{"status": 200, "message": "Course updated successfully"}' :
				'{"status": 500, "message": "Course update failed"}';
	}

	function __destruct() {
		$this->db->close();
	}
}