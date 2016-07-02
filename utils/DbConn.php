<?php

class DbConn {

	private $auth_error = '{"status": 403, "error": "Permission Denied. You do not have access to this resource"}';
	private $no_auth = false;
	private $authenticate;
	private $connection;
	private $debug;

	public function __construct () {
		$this->authenticate = new Authentication();
		$this->debug = new Debugger("DbConn.php");
	}	

	public function conn () {
		$this->connection = mysqli_connect(Properties::$db_host, Properties::$db_user, Properties::$db_pass, Properties::$db_name);
	}

	public function select ($query) {
		$this->debug->log("Selecting record(s) from database with query " . $query, 5);
		$result_set = mysqli_query($this->connection, $query);
		$result = array();
		while ($row = mysqli_fetch_array($result_set, MYSQLI_ASSOC)) {
			array_push($result, $row);
		}

		$return_string = $this->no_auth || $this->authenticate->isAuthorized() ? json_encode($result) : $this->auth_error;
		$this->debug->log("[INFO] Query results from the database " . $return_string, 5);
		$this->no_auth = false;

		return $return_string;
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
		$this->debug->log("Inserting record into database with query " . $query, 5);
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
		$this->debug->log("Updating record in database with query " . $query, 5);
		return mysqli_query($this->connection, $query);
	}

	public function delete ($table, $condition) {
		$query = "delete from " . $table . " where " . $condition;
		$this->debug->log("Deleting record(s) from database with query " . $query, 5);
		return mysqli_query($this->connection, $query);
	}

	public function escape ($string) {
		return mysqli_real_escape_string($this->connection, $string);
	}

	public function bypass_auth () {
		$this->debug->log("[INFO] Setting database query bypass authentication flag to true", 5);
		$this->no_auth = true;
	}

	public function close () {
		mysqli_close($this->connection);
	}
}