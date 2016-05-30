<?php

class Entity {

	private $child;
	private $error;

	protected function create ($data) {
		$status = null;
		$data = json_decode($data, true);
		if ($data === null) {
			$status = '{"status": 500, "message": "Invalid data body object"}';
		}
		else if ($this->validate_object($data, $this->attrs)) {
			$status = $this->db->insert($this->table, $this->transform($data, $this->attrs, true)) ?
				'{"status": 200, "message": "New ' . $this->table . ' created"}' :
				'{"status": 500, "message": "Could not complete ' . $this->table .' insertion query"}';
		}
		else {
			return $this->error;
		}

		return $status;
	}

	protected function validate_object($data, $attrs) {
		$valid = true;
		foreach ($attrs as $key => $value) {
			if (!isset($data[$key])) {
				$this->error = '{"status": 500, "message": "' . $key . ' field is missing"}';
				$valid = false;
			}
		}

		return $valid;
	}

	protected function transform ($data, $attrs, $new) {
		$transformed_object = array();
		foreach ($attrs as $key => $update) {
			if (isset($data[$key]) && ($new || $update)) {
				$transformed_object[$key] = $data[$key];
			}
		}

		return $transformed_object;
	}

	protected function error_log () {
		return $this->error;
	}
}