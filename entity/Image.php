<?php

class Image {

	private static $images = array();
	private static $folder;

	private $auth_error = '{"status": 403, "error": "Permission Denied. You do not have access to this resource"}';
	private $entity_map = array (
		"organization" => array ("data" => "img_src", "id" => "organization_id", "table" => "organization"),
		"topfive" => array("data" => "img_src", "id" => "topfive_id", "table" => "topfive"), 
		"school" => array("data" => "img_src", "id" => "school_id", "table" => "school"),
		"user" => array("data" => "img_src", "id" => "credential_id", "table" => "credential"),
		"post" => array("data" => "media", "id" => "post_id", "table" => "post")
	);

	private $session;
	private $debug;
	private $url;

	public function __construct () {
		$this->debug = new Debugger("Image.php");
	}

	public function render () {
		$session = new Authentication();
		if (!$session->isAuthorized()) {
			# return $this->auth_error;
		}

		$this->debug->log("Authentication passed for Image Controller", 5);
		$url_array = array();
		preg_match("/^\/vigilance\/api\/images\/(.*?jpg|.*?png|.*?jpeg|.*?gif)/", $_SERVER['REQUEST_URI'], $url_array);

		$img_path = $url_array[1];
		$this->debug->log("Extracted image path '" . $img_path . "' from request uri", 5);
		$image_object = false;
		if (preg_match("/.*?\.jpg($|\?.*)/", $img_path) ||  preg_match("/.*?\.jpeg($|\?.*)/", $img_path)) {
			$image_object = @imagecreatefromjpeg(Properties::$img_folder . "/" . $img_path);
			header("Content-Type: image/jpeg");
			if ($image_object) {
				$this->debug->log("Successfully created a .jpg image object from extracted file path", 5);
				imagejpeg($image_object);
				imagedestroy($image_object);
			}
			else {
				$this->debug->log("Could not find specified image with file path '" . Properties::$img_folder . "/" . $img_path . "'", 5);
				return '{"status": 404, "message": "This file cannot be retrieved by the server"}';
			}
		}
		else if (preg_match("/.*?\.png($|\?.*)/", $img_path)) {
			$image_object = @imagecreatefrompng(Properties::$img_folder . "/" . $img_path);
			header("Content-Type: image/png");
			if ($image_object) {
				$this->debug->log("Successfully created a .png image object from extracted file path", 5);
				imagepng($image_object);
				imagedestroy($image_object);
			}
			else {
				$this->debug->log("Could not find specified image with file path '" . Properties::$img_folder . "/" . $img_path . "'", 5);
				return '{"status": 404, "message": "This file cannot be retrieved by the server"}';
			}
		}
		else if (preg_match("/.*?\.gif($|\?.*)/", $img_path)) {
			$image_object = @imagecreatefromgif(Properties::$img_folder . "/" . $img_path);
			header("Content-Type: image/gif");
			if ($image_object) {
				$this->debug->log("Successfully created a .gif image object from extracted file path", 5);
				imagegif($image_object);
				imagedestroy($image_object);
			}
			else {
				$this->debug->log("Could not find specified image with file path '" . Properties::$img_folder . "/" . $img_path . "'", 5);
				return '{"status": 404, "message": "This file cannot be retrieved by the server"}';
			}
		}
		else {
			$this->debug->log("This image file type is not supported", 5);
			return '{"status": 500, "message": "Invalid image file type. Supported image types (gif, jpeg, jpg, png)"}';
		}
	}

	public function generate_url ($folder) {
		$session = new Authentication();
		$user = $session->session_active() ? $session->get_user() : false;
		$image_name = "/default.jpg";
		if (count(self::$images)) {
			$this->debug->log("Image object has been uploaded to the cache image array", 5);
			$image_name = "/" . self::$images["name"];
			$user_folder = $user ? $user['username'] : $session->get_cache()["username"] ;
			$this->debug->log("Setting user image folder to " . $user_folder, 5);
			self::$folder = Properties::$img_folder . $user_folder . $folder;
			$this->url = Properties::$host_name . $user_folder . $folder . $image_name;
		}
		else {
			$this->debug->log("No image object has been uploaded", 5);
			$this->url = Properties::$host_name . $image_name;
		}

		$this->debug->log ("[INFO] Generating '" . $this->url . " for the image url", 5);
		return $this->url;
	}

	public function prepare_image($key) {
		$this->debug->log("[INFO] Preparing image for saving with request method " . $_SERVER["REQUEST_METHOD"], 5);
		if (!isset($_FILES[$key])) {
			$this->debug->log("[INFO] No image object has been uploaded", 5);
			return;
		}
		switch ($_SERVER['REQUEST_METHOD']) {

			case "POST": 
				self::$images = $_FILES[$key];
				break;

			case "PUT":
				// TODO: Figure out how to upload raw images with PUT Requests
				break;
		}
	}

	public function save () {
		if (count(self::$images)) {
			$this->debug->log("Image found in queue. Saving image to the file system to folder " . self::$folder, 5);
			if (!file_exists(self::$folder)) 
				mkdir(self::$folder, 0755, true);
			copy(self::$images["tmp_name"], self::$folder . "/" . self::$images["name"]);
			self::$images = array();
			self::$folder = "";
		}
	}

	public function upload () {
		$this->debug->log("[INFO] uploading image with request url " . $_SERVER['REQUEST_URI'], 5);
		$error_status = '{"status": 500, "error": "Incorrect request format. Check the documentation."}';
		$status = "";

		$img_params = array();
		preg_match('/\/images\/(.*?)\/(\d{1,}).*/', $_SERVER['REQUEST_URI'], $img_params);
		if (count ($img_params) !== 3) {
			return $error_status;
		}
		$table = $img_params[1];
		$id = $img_params[2];
		$this->debug->log ("Extracted values id=" . $id . " and table=" . $table . " from the url path", 5);
		if (!isset ($this->entity_map[$table])) {
			$this->debug->log ("Table entry " . $table . " does not exist in the image entity map", 5);
			return $error_status;
		}

		$data = array ($this->entity_map[$table]["id"] => $id);
		$attrs = array (
			$this->entity_map[$table]["id"] => array ("authToken" => true), 
			"credential_id" => array ("authorize" => true)
		);
		
		if ($table === "user") {
			$attrs = array ("credential_id" => array("authorize" => true, "authToken" => true));
		}
		$authenticate = new Authentication();
		if ($authenticate->authorize_put($this->entity_map[$table]["table"], $data, $attrs)) {
			$this->debug->log("Authorization for uploading " . $table . " image passed", 5);
			if (isset($_FILES[$this->entity_map[$table]["data"]])) {
				$this->debug->log("Image found under the request object attribute " . $table, 5);
				self::$images = $_FILES[$this->entity_map[$table]["data"]];
				$data[$this->entity_map[$table]["data"]] = $this->generate_url("/" . $this->entity_map[$table]["table"]);
				$db = new DbConn();
				$db->conn();
				if ($db->update($this->entity_map[$table]["table"], $data, $this->entity_map[$table]["id"] . " = " . $id)) {
					$this->debug->log("Image url successfully uploaded to the database", 5);
					$status = '{"status": 200, "message":"Image successfully uploaded"}';
					$this->save();
				}
				else {
					$this->debug->log("Database failure occured while trying to upload the image", 5);
					$status = '{"status": 500, "message":"Image could not be uploaded"}';
				}
				
				$db->close();
				$this->save();
			}
			else {
				$this->debug->log("No image found under the request object attribute " . $table, 5);
				$status = $error_status;
			}
		}
		else {
			$this->debug->log("Authorization for uploading " . $table . " image failed", 5);
			$status = $this->auth_error;
		}

		return $status;
	}

	private function get_image_name () {
		foreach (self::$images as $name => $value) {
			return "/" . $name;
		}

		return "/no_name.png";
	}
}