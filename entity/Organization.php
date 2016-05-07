<?php

class Organization {

	private $GET_ALL = "select * from organization";
	private $db;
	public function __construct () {
		$this->db = new DbConn();
		$this->db->conn();
	}

	public function get_all () {
		return $this->db->select($this->GET_ALL);
	}

	function __destruct() {
		$this->db->close();
	}
}