<?php

class UserController {
	
	private $user;
	public function __construct () {
		$this->user = new User();
	}

	public function all () {
		return $this->user->get_all();
	}

	public function get ($id) {
		return $this->user->get_by_id($id);
	}
}