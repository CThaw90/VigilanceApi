<?php

class Debugger {

	private static $log_dir = ".ignore/logs";
	private static $log_file = "log.txt";
	
	private $file_manager;
	private $class;

	public function __construct ($class) {
		$this->file_manager = new FileManager(self::$log_dir);
		$this->class = $class;
	}
	
	public function log ($message, $debug_level) {
		if ((isset($_GET['debug']) && $_GET['debug'] >= intval($debug_level)) || Properties::$log_always) {
			$timestamp = date('Y-m-d H:i:s', time());
			$this->file_manager->append_line(self::$log_file, " ( " . $this->class . " - " . $timestamp . " )" . $message);
		}
	}

	public function set_log_dir ($dir) {
		if ($this->is_valid($dir)) {
			self::$log_dir = $dir;
		}
	}

	public function set_log_file ($filename) {
		if ($this->is_valid($filename)) {
			self::$log_file = $filename;
		}
	}

	private function is_valid ($str) {
		return $str !== null && 
			gettype ($str) === "string" && 
				count($str);
	}
}