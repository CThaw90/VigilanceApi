<?php

class SchoolController {
	
	private $school;
	public function __construct() {
		$this->school = new School();
	}

	public function all () {
		return $this->school->get_all();
	}

	public function get($id) {
		$db = new DbConn();
		$db->conn();
		$db->close();
		return $id;
	}
}