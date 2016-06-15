<?php

class OrganizationController {
	
	private $primary_key = "organization_id";
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
		return $this->organization->update($data, $this->primary_key);
	}

	public function delete($id) {
		return $this->organization->delete($id, $this->primary_key);
	}
}