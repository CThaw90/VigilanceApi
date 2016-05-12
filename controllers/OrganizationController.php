<?php

class OrganizationController {
	
	private $organization;
	public function __construct() {
		$this->organization = new Organization();
	}

	public function all () {
		return $this->organization->get_all();
	}

	public function get($id) {
		return $this->organization->get_by_id($id);
	}

	public function post($data) {
		return $this->organization->create($data);
	}

	public function put($data) {
		return '{"status": "In Progress"}';
	}

	public function delete($id) {
		return $this->organization->delete($id);
	}
}