<?php

class CourseController {
	
	private $course;
	public function __construct() {
		$this->course = new Course();
	}

	public function all () {
		return $this->course->get_all();
	}

	public function get($id) {
		return $this->course->get_by_id($id);
	}
}