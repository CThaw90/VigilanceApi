<?php

class DbConn {

	private $hostname = '127.0.0.1';
	private $username = 'root';
	private $password = 'musicismylife90';
	private $database = 'whatthn2_vigilance';

	private $connection;
	public function __construct () {
	}	

	public function conn () {
		$this->connection = mysqli_connect($this->hostname, $this->username, $this->password, $this->database);
	}

	public function close () {
		mysqli_close($this->connection);
	}
}