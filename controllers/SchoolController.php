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
}