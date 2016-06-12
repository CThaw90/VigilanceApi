<?php

class Entity {

	protected $auth_error = '{"status": 403, "error": "Permission Denied. You do not have access to this resource"}';
	protected $no_auth = false;
	private $error;
	private $debug;

	public function __construct () {
		$this->debug = new Debugger("Entity.php");
	}

	protected function create ($data) {
		$status = null;
		$this->debug->log("[Preparing to create object with table id " . $this->table, 3);
        $data = $this->parse_request_body($data);
		if ($data === null || !count($data) === 0) {
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
		$this->debug->log("[INFO] Parsing incoming data with request body ", 5);
		$this->debug->log($request, 5);
		$data = json_decode($request, true);
		$data = array_merge($data !== null ? $data : array(), $_POST);
		$data = $data !== null && count($data) ? $data : $this->parse_form_encoded_body($request);
		
		return $data !== null && count($data) ? $data : $this->parse_www_form_encoded_body($request);
	}

	private function parse_form_encoded_body ($formData) {
		$form_data_array = explode("\r\n", $formData);
		$parsed_data = array();
		$grab_next_value_in = 0;
		$key = array();

		foreach ($form_data_array as $index => $value) {

			if (preg_match('/^------.*/', $value)) { /* Ignore this line */
				$this->debug->log("[INFO] Ignoring Form Data blob " . $value, 5);
			}
			else if (preg_match('/^Content\-Disposition:.*/', $value)) {
				preg_match ('/name="(.*)"$/', $value, $key);
				$this->debug->log("[INFO] Parsed Form Data Key " . (count($key) ? trim($key[count($key)-1]) : "NULL") . " from Content-Disposition", 5);
				$grab_next_value_in = count($key) ? 3 : 0;
			}
			
			if ($grab_next_value_in) {
				if ($grab_next_value_in === 1) {
					$parsed_data[$key[count($key) - 1]] = $value;
				}
				
				$grab_next_value_in -= 1;
			}
		}

		return $parsed_data;
	}

	private function parse_www_form_encoded_body ($formData) {
		$form_data_array = explode("&", $formData);
		$parsed_data = array();

		foreach ($form_data_array as $index => $value) {
			$key = explode("=", urldecode($value));
			$parsed_data[$key[0]] = $key[1];
		}

		return $parsed_data;
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