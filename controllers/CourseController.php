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

	public function post($data) {
		return $this->course->create($data);
	}

	public function put($data) {
		return $this->course->update($data);
	}

	public function delete ($id) {
		return $this->course->delete($id);
	}
}