<?php

class DbConn {

	private $auth_error = '{"status": 403, "error": "Permission Denied. You do not have access to this resource"}';
	private $authenticate;
	private $connection;

	public function __construct () {
		$this->authenticate = new Authentication();
	}	

	public function conn () {
		$this->connection = mysqli_connect(Properties::$db_host, Properties::$db_user, Properties::$db_pass, Properties::$db_name);
	}

	public function select ($query) {
		$result_set = mysqli_query($this->connection, $query);
		$result = array();
		while ($row = mysqli_fetch_array($result_set, MYSQLI_ASSOC)) {
			array_push($result, $row);
		}

		return $this->authenticate->isAuthorized() ? json_encode($result) : $this->auth_error;
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

	public function escape ($string) {
		return mysqli_real_escape_string($this->connection, $string);
	}

	public function close () {
		mysqli_close($this->connection);
	}
}