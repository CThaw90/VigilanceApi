<?php

class PostController {

	private $primary_key = "post_id";
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

	public function post($data) {
		return $this->post->create($data);
	}

	public function put($data) {
		return $this->post->update($data, $this->primary_key);
	}

	public function delete ($id) {
		return $this->post->delete($id, $this->primary_key);
	}
}