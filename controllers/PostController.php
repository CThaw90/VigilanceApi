<?php

class PostController {

	private $post;
	public function __construct() {
		$this->post = new Post();
	}

	public function all () {
		return $this->post->get_all();
	}

	public function get($id) {
		return $this->post->get_by_id($id);
	}
}