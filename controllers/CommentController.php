<?php

class CommentController {
	
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
}