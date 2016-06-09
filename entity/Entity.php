<?php

class Entity {

	private $error;
	protected function create ($data) {
		$status = null;
        $data = json_decode($data, true);
        $data = array_merge($data !== null ? $data : array(), $_POST);
		if (!count($data) === 0) {
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

	protected function parse_request_body ($request) {
		$data = json_decode($request, true);
		$data = array_merge($data !== null ? $data : array(), $_POST);
		
		return count($data) ? $data : array_merge($data, $this->parse_form_encoded_body($request));
	}

	private function parse_form_encoded_body ($formData) {
		return array();
	}

	protected function error_log () {
		return $this->error;
	}
}