<?php

class DbConn {

	private $hostname = '127.0.0.1';
	private $username = 'root';
	private $password = 'musicismylife90';
	private $database = 'whatthn2_vigilance';

	private $connection;
	public function __construct () {}	

	public function conn () {
		$this->connection = mysqli_connect($this->hostname, $this->username, $this->password, $this->database);
	}

	public function select ($query) {
		$result_set = mysqli_query($this->connection, $query);
		$result = array();
		while ($row = mysqli_fetch_array($result_set, MYSQLI_ASSOC)) {
			array_push($result, $row);
		}

		#$result = mysqli_fetch_all(mysqli_query($this->connection, $query));

		return json_encode($result);
	}

	public function close () {
		mysqli_close($this->connection);
	}
}