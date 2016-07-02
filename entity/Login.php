<?php 

class Login extends Entity {
	
	private $LOGIN_CREDENTIAL = 'select * from credential where ';
	private $debug;

	protected $table = "credential";
	protected $error;
	protected $db;

	public function __construct () {
		$this->debug = new Debugger("Login.php");
		$this->db = new DbConn();
		$this->db->conn();

		parent::__construct();
	}

	public function authenticate ($data) {
		$authenticate = new Authentication();
		$auth = null;
		$data = $this->parse_request_body($data);
		if (isset($data['username']) && isset($data['password'])) {
			$this->db->bypass_auth();
			$auth = json_decode($this->db->select($this->LOGIN_CREDENTIAL . 
				" username = '" . $this->db->escape($data['username']) . "' and " . 
				" password in ('" . sha1($data['password']) . "', '" . $this->db->escape($data['password']) . "')"), true);
		}

		if (isset($auth[0])) {
			$this->debug->log("[INFO] An authorization user entry was found", 5);
			$token = $authenticate->generate_token($auth[0]);
			return '{"token":"' . $token . '", "user": ' . json_encode($authenticate->get_user($token)) . '}';
		}

		$data['username'] = isset($data['username']) ? $data['username'] : 'NULL';
		$data['password'] = isset($data['password']) ? $data['password'] : 'NULL';
		
		$this->debug->log("[WARNING] Incorrect username or password entered username=" . $data['username'] . " password=" . $data['password'], 3);
		return '{"error": "Bad Username or Password"}';
	}

	public function __destruct () {
		$this->db->close();
	}
}