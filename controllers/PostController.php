<?php

class PostController {

	private $post;
	public function __construct() {

		$post = new Post();
	}

	public function get($id) {
		$db = new DbConn();
		$db->conn();
		$db->close();
		return $id;
	}
}