<?php

class User {
	
	private $GET_CRED_BY_ID = 'select * from credential where credential_id = ${1}';
	private $GET_ALL = "select * from credential";
	private $db;

	public function __construct () {
		$this->db = new DbConn();
		$this->db->conn();
	}

	public function get_all () {
		return $this->db->select($this->GET_ALL);
	}

	public function get_by_id ($id) {
		return $this->db->select(preg_replace("/(\d+)/", $this->GET_CRED_BY_ID, $id));
	}

	function __destruct () {
		$this->db->close();
	}
}