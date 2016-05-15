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

		return json_encode($result);
	}

	public function insert ($table, $object) {
		$query_table = "insert into " . $table;
		$query_columns = " (";
		$query_values = " (";
		$comma = "";
		foreach ($object as $key => $value) {
			$query_columns = $query_columns . $comma . $key;
			$query_values = $query_values . $comma . "'" . mysqli_real_escape_string($this->connection, $value) . "'";
			$comma = ",";
		}

		$query = $query_table . $query_columns . ") VALUES " . $query_values . ")";
		echo $query;
		return mysqli_query($this->connection, $query);
	}

	public function update ($table, $object, $condition) {
		$query_table = "update " . $table . " set";
		$query_columns = "";
		$comma = " ";
		foreach ($object as $key => $value) {
			$query_columns = $query_columns . $comma . $key . "=" . "'" . mysqli_real_escape_string($this->connection, $value) . "'";
			$comma = ", ";
		}
		$query = $query_table . $query_columns . " where " . $condition;
		return mysqli_query($this->connection, $query);
	}

	public function delete ($table, $condition) {
		return mysqli_query($this->connection, "delete from " . $table . " where " . $condition);
	}

	public function close () {
		mysqli_close($this->connection);
	}
}