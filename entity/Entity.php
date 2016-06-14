<?php

class Entity {

	protected $auth_error = '{"status": 403, "error": "Permission Denied. You do not have access to this resource"}';
	private $no_auth = false;
	private $error;
	private $debug;
	private $image;
	private $auth;

	public function __construct () {
		$this->debug = new Debugger("Entity.php");
		$this->auth = new Authentication();
	}

	protected function create ($data) {
		$this->image = new ImageController();
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

			if ($status === '{"status": 200, "message": "New ' . $this->table . ' created"}') {
				$this->debug->log("Object successfully created. Creating any queued images", 3);
				$this->image->save();
			}
		}
		else {
			return $this->error;
		}

		return $status;
	}

	protected function update ($data, $updateBy) {
		$status = "";
		$data = $this->parse_request_body($data);
		if ($data === null || !count($data)) {
			$status = '{"status": 500, "message": "Invalid data body object"}';
		}
		else if (isset($data[$updateBy])) {
			$status = $this->isAuthorized($data, $this->attrs) ? $this->update_by_id($data, $updateBy) : $this->auth_error;
		}
		else {
			$status = '{"status": 500, "message": "' . $this->table . ' update failed. No update type declaration."}';
		}

		return $status;
	}

	protected function update_by_id ($data, $updateBy) {
		return $this->db->update($this->table, $this->transform($data, $this->attrs, false),
			preg_replace("/(\d+)/", $this->UPDATE_BY_ID, $data[$updateBy])) ?
				'{"status": 200, "message": "' . $this->table . ' updated successfully"}' :
				'{"status": 500, "message": "' . $this->table . ' update failed"}';		
	}

	protected function delete ($id, $deleteBy) {
		if ($this->isAuthorized(array($deleteBy => $id), $this->attrs)) {
			return $this->db->delete($this->table, preg_replace("/(\d+)/", $this->DELETE_BY_ID, $id)) ?
				'{"status": 200, "message": "' . $this->table . ' deleted"}' : 
				'{"status": 500, "message": "' . $this->table . ' could not be deleted"}';
		}

		return $this->auth_error;
	}

	protected function validate_object($data, $attrs) {
		$valid = true;
		foreach ($attrs as $key => $value) {
			if ($valid && !isset($data[$key])) {
				$this->error = '{"status": 500, "message": "' . $key . ' field is missing"}';
				$valid = (isset($value['postIgnore']) && $value['postIgnore']) || 
						 (isset($value['fileUpload']) && $value['fileUpload']);	
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
			else if (($new || $value['canUpdate']) && (isset($value['fileUpload']) && $value['fileUpload']) && 
					 (!isset($value['postIgnore']) || !$value['postIgnore'])) {

				$this->debug->log("[INFO] Evaluating new file upload key " . $key, 5);
				$this->image->prepare($key);
				$transformed_object[$key] = $this->image->url('/' . $this->table);
			}
		}

		return $transformed_object;
	}

	protected function parse_request_body ($request) {
		$this->debug->log("[INFO] Parsing incoming data with request body ", 5);
		$data = json_decode($request, true);
		$data = array_merge($data !== null ? $data : array(), $_POST);
		$data = $data !== null && count($data) ? $data : $this->parse_form_encoded_body($request);
		
		return $data !== null && count($data) ? $data : $this->parse_www_form_encoded_body($request);
	}

	private function parse_form_encoded_body ($formData) {
		$form_data_array = explode("\r\n", $formData);
		$parsed_data = array();
		$grab_next_value_in = 0;
		$file_type = array (
			"capture" => false,
			"video" => false,
			"image" => array(
				"jpeg" => false,
				"jpg" => false,
				"png" => false,
				"gif" => false
			),
			"text" => false,
			"pdf" => false
		);

		$key = array();
		foreach ($form_data_array as $index => $value) {

			if (preg_match('/^Content\-Disposition:.*/', $value)) {
				preg_match ('/name="(.*?)";?/', $value, $key);
				$this->debug->log("[INFO] Parsed Form Data Key " . (count($key) ? trim($key[count($key)-1]) : "NULL") . " from " . $value, 5);
				if (preg_match ('/filename/', $value)) {
					$temp_key = trim($key[count($key) - 1]);
					preg_match('/filename="(.*?)";?/', $value, $key);
					$parsed_data[$temp_key] = trim($key[count($key) - 1]); 
					$grab_next_value_in = count($key) ? 4 : 0;

					$this->debug->log("[INFO] Multipart/form-data file detected in " . $value, 3);
					$this->debug->log("[INFO] Storing value " . $parsed_data[$temp_key] . " into key " . $temp_key, 5);
				}
				else {
					$grab_next_value_in = count($key) ? 3 : 0;
					$file_type["text"] = true;
				}
			}
			else if ($grab_next_value_in === 1) {
				
				if ($file_type["text"]) {
					$this->debug->log("[INFO] Storing value " . $value . " into key index " . $key[count($key) - 1], 5);
					$parsed_data[$key[count($key) - 1]] = $value;
					$file_type["text"] = false;
				}
				else if ($file_type["image"]["jpeg"] || $file_type["image"]["jpg"]) {
					$this->debug->log("[INFO] Image file JPEG/JPG detected.", 5);
					$file_type["image"]["jpeg"] = $file_type["image"]["jpg"] = false;
					
				}
				else if ($file_type["image"]["png"]) {
					$this->debug->log("[INFO] Image file PNG detected.", 5);
					// $file_type["image"]["png"] = false;				
					$file_type["capture"] = true;
				}
				else if ($file_type["image"]["gif"]) {
					$this->debug->log("[INFO] Image file GIF detected.", 5);
					$file_type["image"]["gif"];
				}
			}
			else if (preg_match("/^Content\-Type:/", $value)) {
				preg_match ("/:\s(.*)/", $value, $key);

				switch ($key[count($key) - 1]) {
					case "image/jpeg":
						$file_type["image"]["jpeg"] = true;
						break;

					case "image/jpg":
						$file_type["image"]["jpg"] = true;
						break;

					case "image/png":
						$file_type["image"]["png"] = true;
						break;

					case "image/gif":
						$file_type["image"]["gif"] = true;
						break;

					default:
						$this->debug->log("[WARNING] File type " . $key[count($key) - 1] . " not supported ");
						break;
				}
			}
			else if ($file_type["capture"]) {

				if ($file_type["image"]["png"]) {
					file_put_contents(".ignore/images/test.png", $value);
				}
			}
			else {
				$this->debug->log("Skipping over file portion with data type " . gettype($value) . " and value " . $value, 5);
			}

			$grab_next_value_in -= $grab_next_value_in ? 1 : 0;
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

	protected function bypass_auth ($args) {
		$this->auth->store_cache($args);
		$this->no_auth = true;
	}

	protected function isAuthorized ($data, $attrs) {
		$this->error = $this->no_auth || $this->auth->isAuthorized() && $this->auth->authorize_action($this->table, $data, $attrs) ? null : $this->auth_error;
		$this->no_auth = false;
		return $this->error === null;
	}

	protected function error_log () {
		return $this->error;
	}
}