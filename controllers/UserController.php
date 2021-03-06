<?php

class UserController {
	
	private $primary_key = "credential_id";
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

	public function post ($data) {
		return $this->user->create($data);
	}

	public function put ($data) {
		return $this->user->update($data, $this->primary_key);
	}

	public function delete ($id) {
		return $this->user->delete($id, $this->primary_key);
	}
}