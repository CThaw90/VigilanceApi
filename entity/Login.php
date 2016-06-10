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

		$authenticate = new Authentication();
		$authenticate->ignore();
		$auth = null;
		$data = $this->parse_request_body($data);
		if (isset($data['username']) && isset($data['password'])) {
			$auth = json_decode($this->db->select($this->LOGIN_CREDENTIAL . 
				" username = '" . $this->db->escape($data['username']) . "' and " . 
				" password in ('" . sha1($data['password']) . "', '" . $this->db->escape($data['password']) . "')"), true);
		}

		print_r ($auth);
		print gettype ($auth);
		if (count($auth)) {
			print_r ($auth[0]);	
			$authenticate->generate_token($auth[0]);
			return '{"token":"' . $authenticate->get_token() . '", "user": "' . json_encode($authenticate->get_user()) . '"}';
		}

		return '{"error": "Bad Username or Password"}';
	}

	function __destruct () {
		$this->db->close();
	}
}