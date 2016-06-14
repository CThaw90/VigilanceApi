<?php

class CommentController {
	
	private $primary_key = "comment_id";
	private $comment;
	public function __construct() {
		$this->comment = new Comment();
	}

	public function all () {
		return $this->comment->get_all();
	}

	public function get ($id) {
		return $this->comment->get_by_id($id);
	}

	public function post ($data) {
		return $this->comment->create($data);
	}

	public function put ($data) {
		return $this->comment->update($data, $this->primary_key);
	}

	public function delete ($id) {
		return $this->comment->delete($id, $this->primary_key);
	}
}