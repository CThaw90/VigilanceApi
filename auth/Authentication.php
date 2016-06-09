<?php

session_start();

class Authentication {

	public function generate_token ($user) {
		if (!isset($_SESSION['token'])) {
			$token = sha1(json_encode($user) . time());
			$_SESSION['token'] = $token;
			$_SESSION[$token] = $user;
		}

	}

	public function get_token () {
		return isset ($_SESSION['token']) ? $_SESSION['token'] : null;
	}

	public function get_user ($token) {
		return isset ($_SESSION[$token]) ? $_SESSION[$token] : null;
	}

	public function isAuthorized () {
		$headers = getallheaders();
		return isset($_SESSION['token']) && isset($headers['token']) 
			&& $headers['token'] === $_SESSION['token'];
	}

	public function destroy_token () {
		session_destroy();
	}
}