<?php

class OrganizationController {
	
	private $prganization;
	public function __construct() {
		$this->organization = new Organization();
	}

	public function all () {
		return $this->organization->get_all();
	}

	public function get($id) {
		$db = new DbConn();
		$db->conn();
		$db->close();
		return $id;
	}
}