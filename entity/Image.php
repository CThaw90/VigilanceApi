<?php

class Image {

	private static $images = array();
	private static $folder;
	private $session;
	private $debug;

	public function __construct () {
		$this->debug = new Debugger("Image.php");
	}

	public function generate_url ($folder) {
		$session = new Authentication();
		$user = $session->session_active() ? $session->get_user() : false;
		$image_name = "/no_image.png";
		if (count(self::$images)) {
			$this->debug->log("Image object has been uploaded to the cache image array", 5);
			$image_name = "/" . self::$images["name"];
		}
		else {
			$this->debug->log("No image object has been uploaded", 5);
		}

		$user_folder = $user ? $user['username'] : $session->get_cache()["username"] ;
		$this->debug->log("Setting user image folder to " . $user_folder, 5);

		self::$folder = Properties::$img_folder . $user_folder . $folder;
		return Properties::$host_name . $user_folder . $folder . $image_name;

	}

	public function prepare_image($key) {
		$this->debug->log("[INFO] Preparing image for saving with request method " . $_SERVER["REQUEST_METHOD"], 5);
		switch ($_SERVER['REQUEST_METHOD']) {

			case "POST": 
				self::$images = $_FILES[$key];
				break;

			case "PUT":
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

	private function get_image_name () {
		foreach (self::$images as $name => $value) {
			return "/" . $name;
		}

		return "/no_name.png";
	}
}