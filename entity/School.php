<?php

class School {
	
	private $GET_ALL = "select * from school;";
	private $db;

	public function __construct() {
		$this->db = new DbConn();
		$this->db->conn();
	}

	public function get_all() {
		return $this->db->select($this->GET_ALL);
	}

	function __destruct() {
		$this->db->close();
	}
}