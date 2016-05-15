<?php

class Organization extends Entity {

	private $GET_ALL = "select * from organization";
	private $GET_ORG_BY_ID = 'select * from organization where organization_id = ${1}';
	private $UPDATE_BY_ID = 'organization_id = ${1}';
	private $DELETE_ORG = 'organization_id = ${1}';

	protected $attrs = array("name" => true, "display_name" => true, "city" => true, "email" => true, "img_src" => true);
	protected $table = "organization";
	protected $error;
	protected $db;

	public function __construct () {
		$this->db = new DbConn();
		$this->db->conn();
	}

	public function get_all () {
		return $this->db->select($this->GET_ALL);
	}

	public function get_by_id ($id) {
		return $this->db->select(preg_replace("/(\d+)/", $this->GET_ORG_BY_ID, $id));
	}

	public function create ($data) {
		return parent::create($data);
	}

	public function update ($data) {
		$status = "";
		$data = json_decode($data, true);
		if ($data === null) {
			$status ='{"status": 500, "message": "Invalid data body object"}';
		}
		else if (isset($data['organization_id'])) {
			$status = $this->update_by_id($data);
		}
		else {
			$status = '{"status": 500, "message": "Organization Update failed. No update type declaration."}';
		}

		return $status;
	}

	public function delete ($id) {
		return $this->db->delete("organization", preg_replace("/(\d+)/", $this->DELETE_ORG, $id)) ? 
			'{"status": 200, "message": "Organization deleted"}' : '{"status": 500, "message": "Organization could not be deleted"}';
	}

	private function update_by_id ($data) {
		return $this->db->update("organization", $this->transform($data, $this->attrs, false), 
			preg_replace("/(\d+)/", $this->UPDATE_BY_ID, $data['organization_id'])) ? 
				'{"status": 200, "message": "Organization updated successfully"}' :
				'{"status": 500, "message": "Organization update failed"}';
	}

	function __destruct() {
		$this->db->close();
	}
}