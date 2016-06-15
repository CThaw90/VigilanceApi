<?php

class FileManager {
	
	private $basedir;

	public function __construct($basedir) {
		$this->basedir = $basedir;
	}

	public function append_line($filename, $string) {
		$this->ensure_file_path_exists();
		file_put_contents($this->basedir . "/" . $filename, $string . "\n", FILE_APPEND | LOCK_EX);
	}

	private function ensure_file_path_exists () {
		if (!file_exists($this->basedir)) {
			mkdir($this->basedir, 0755, true);
		}
	}
}