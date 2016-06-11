<?php 

class Login extends Entity {
	
	private $LOGIN_CREDENTIAL = 'select * from credential where ';
	private $debug;

	protected $table = "credential";
	protected $error;
	protected $db;

	public function __construct () {
		$this->debug = new Debugger();
		$this->db = new DbConn();
		$this->db->conn();
	}

	public function authenticate ($data) {

		$authenticate = new Authentication();
		$authenticate->ignore();
		$auth = null;
		$data = $this->parse_request_body($data);
		if (isset($data['username']) && isset($data['password'])) {
			$auth = json_decode($this->db->select($this->LOGIN_CREDENTIAL . 
				" username = '" . $this->db->escape($data['username']) . "' and " . 
				" password in ('" . sha1($data['password']) . "', '" . $this->db->escape($data['password']) . "')"), true);
		}

		if (count($auth)) {
			$this->debug->log("[INFO] An authorization user entry was found", 5);
			$authenticate->generate_token($auth[0]);
			return '{"token":"' . $authenticate->get_token() . '", "user": "' . json_encode($authenticate->get_user()) . '"}';
		}

		return '{"error": "Bad Username or Password"}';
	}

	function __destruct () {
		$this->db->close();
	}
}