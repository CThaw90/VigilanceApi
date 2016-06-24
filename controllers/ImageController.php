<?php

class ImageController {
	
	private $image;
	public function __construct() {
		$this->image = new Image();
	}

	public function get () {
		return $this->image->render();
	}

	public function post () {
		return $this->image->upload();
	}

	public function url ($folder) {
		return $this->image->generate_url($folder);
	}

	public function prepare ($key) {
		return $this->image->prepare_image($key);
	}

	public function save () {
		$this->image->save();
	}
}