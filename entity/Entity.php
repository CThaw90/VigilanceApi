<?php

class Entity {

	protected $auth_error = '{"status": 403, "error": "Permission Denied. You do not have access to this resource"}';
	protected $no_auth = false;
	private $error;

	protected function create ($data) {
		$status = null;
        $data = $this->parse_request_body($data);
		if (!count($data) === 0) {
			$status = '{"status": 500, "message": "Invalid data body object"}';
		}
		else if ($this->validate_object($data, $this->attrs) && $this->isAuthorized($data, $this->attrs)) {
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
			if ($valid && !isset($data[$key])) {
				$this->error = '{"status": 500, "message": "' . $key . ' field is missing"}';
				$valid = (isset($value['postIgnore']) && $value['postIgnore']);
			}
		}

		return $valid;
	}

	protected function transform ($data, $attrs, $new) {
		$transformed_object = array();
		foreach ($attrs as $key => $value) {
			if (isset($data[$key]) && ($new || $value['canUpdate']) && (!isset($value['postIgnore']) || !$value['postIgnore'])) {
				$transformed_object[$key] = $data[$key];
			}
		}

		return $transformed_object;
	}

	protected function parse_request_body ($request) {
		$data = json_decode($request, true);
		$data = array_merge($data !== null ? $data : array(), $_POST);
		
		return $data !== null ? $data : array_merge(array(), $this->parse_form_encoded_body($request));
	}

	private function parse_form_encoded_body ($formData) {
		return array();
	}

	protected function isAuthorized ($data, $attrs) {
		$auth = new Authentication();
		$this->error = $this->no_auth || $auth->isAuthorized() && $auth->authorize_action($this->table, $data, $attrs) ? null : $this->auth_error;
		$this->no_auth = false;
		return $this->error === null;
	}

	protected function error_log () {
		return $this->error;
	}
}