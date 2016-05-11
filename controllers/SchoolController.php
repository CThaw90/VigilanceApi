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
		return $this->school->get_by_id($id);
	}

	public function post($data) {
		return $this->school->create($data);
	}

	public function delete($id) {
		return $this->school->delete($id);
	}
}