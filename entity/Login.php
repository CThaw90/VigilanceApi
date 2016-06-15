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
		if ($authenticate->session_active() && $this->isnt_the_same_user($authenticate, $data)) {
			return '{"status": 500, "error": "A User is already logged in to this device. Logout the current user first"}';
		}
		else if (isset($data['username']) && isset($data['password'])) {
			$authenticate->ignore();
			$auth = json_decode($this->db->select($this->LOGIN_CREDENTIAL . 
				" username = '" . $this->db->escape($data['username']) . "' and " . 
				" password in ('" . sha1($data['password']) . "', '" . $this->db->escape($data['password']) . "')"), true);
		}

		if (count($auth)) {
			$this->debug->log("[INFO] An authorization user entry was found", 5);
			$authenticate->generate_token($auth[0]);
			return '{"token":"' . $authenticate->get_token() . '", "user": "' . json_encode($authenticate->get_user()) . '"}';
		}

		$this->debug->log("[WARNING] Incorrect username or password entered username=" . $data['username'] . " password=" . $data['password'], 3);
		return '{"error": "Bad Username or Password"}';
	}

	private function isnt_the_same_user ($authenticate, $data) {
		$user = $authenticate->get_user();
		$this->debug->log("[INFO] Session Username=" . $user['username'] . " Requesting Username=" . $data['username'], 4);
		$this->debug->log("[INFO] Session Password=" . $user['password'] . " Requesting Password=" . $data['password'], 4);
		return $user['username'] !== $data['username'] 
			|| $user['password'] !== $data['password'];
	}

	function __destruct () {
		$this->db->close();
	}
}