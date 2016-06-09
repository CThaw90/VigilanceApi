<?php

class Logout {
	
	public function logoff () {
		$auth = new Authentication();
		$user = $auth->get_user($auth->get_token());
		$auth->destroy_token();
		return '{"status": 200, "message": "User successfully logged out"}';
	}
}