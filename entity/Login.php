<?php 

class Login extends Entity {
	
	private $LOGIN_CREDENTIAL = 'select * from credential where ';

	protected $table = "credential";
	protected $error;
	protected $db;

	public function __construct () {
		$this->db = new DbConn();
		$this->db->conn();
	}

	public function authenticate ($data) {
		$auth = null;
		$data = json_decode($data, true);
		if (isset($data['username']) && isset($data['password'])) {
			$auth = json_decode($this->db->select($this->LOGIN_CREDENTIAL . 
				" username = '" . $this->db->escape($data['username']) . "' and " . 
				" password in ('" . sha1($data['password']) . "', '" . $this->db->escape($data['password']) . "')"));
		}

		if (count($auth)) {
			$authenticate = new Authentication();
			$authenticate->generate_token($auth[0]);
			return '{"token":"' . $authenticate->get_token() . '"}';
		}

		return '{"error": "Bad Username or Password"}';
	}

	function __destruct () {
		$this->db->close();
	}
}