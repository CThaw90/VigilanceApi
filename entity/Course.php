<?php

class Course {

	private $GET_COURSE_BY_ID = 'select * from course where course_id = ${1}';
	private $GET_ALL = "select * from course";
	private $DELETE_COURSE = 'course_id = ${1}';
	private $db;
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
		$status = '';
		$data = json_decode($data);
		if ($data === null) {
			$status = '{"status": 500, "messsage": "Invalid data body object"}';
		} else if ($this->validate_object($data)) {
			$status = $this->db->insert("course", $data) ? 
				'{"status": 200, "message": "New course created"}' :
				'{"status": 500, "message": "Could not complete course insertion query"}';
		}
		else {
			return $this->error;
		}

		return $status;
	}

	public function delete ($id) {
		return $this->db->delete("course", preg_replace("/(\d+)/", $this->DELETE_COURSE, $id)) ? 
			'{"status": 200, "message": "Course deleted"}' : '{"status": 500, "message": "Course could not be deleted"}';
	}


	private function validate_object ($data) {
		$valid = false;
		if (!isset($data->school_id)) {
			$this->error = '{"status": 500, "message": "school_id field is missing"}';
		}
		else if (!isset($data->credential_id)) {
			$this->error = '{"status": 500, "message": "credential_id field is missing"}';
		}
		else if (!isset($data->name)) {
			$this->error = '{"status": 500, "message": "name field is missing"}';
		}
		else if (!isset($data->start_time)) {
			$this->error = '{"status": 500, "message": "start_time field is missing"}';
		}
		else if (!isset($data->end_time)) {
			$this->error = '{"status": 500, "message": "end_time field is missing"}';
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