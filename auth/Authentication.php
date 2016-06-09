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

	public function login () {
		$_SESSION['login'] = 1;
	}

	public function get_token () {
		return isset ($_SESSION['token']) ? $_SESSION['token'] : null;
	}

	public function get_user ($token) {
		return isset ($_SESSION[$token]) ? $_SESSION[$token] : null;
	}

	public function isAuthorized () {
		$login = isset($_SESSION['login']) ? $_SESSION['login'] : 0;
		$headers = getallheaders();
		$_SESSION['login'] = 0;

		return (isset($_SESSION['token']) && isset($headers['token']) 
			&& $headers['token'] === $_SESSION['token']) || $login;
	}

	public function destroy_token () {
		session_destroy();
	}
}