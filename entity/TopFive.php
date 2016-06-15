<?php

class TopFive extends Entity {

    private $GET_TOPFIVE_BY_ID = 'select * from topfive where topfive_id = ${1}';
    private $GET_ALL = 'select * from topfive';
    

    protected $DELETE_BY_ID = 'topfive_id = ${1}';
    protected $UPDATE_BY_ID = 'topfive_id = ${1}';
    protected $attrs = array(
        "credential_id" => array("canUpdate" => false, "authorize" => true),
        "type" => array("canUpdate" => true, "needAuth" => false),
        "id" => array("canUpdate" => false, "needAuth" => false),
        "name" => array("canUpdate" => true, "needAuth" => false),
        "email" => array("canUpdate" => true, "needAuth" => false),
        "city" => array("canUpdate" => true, "needAuth" => false),
        "img_src" => array("canUpdate" => false, "needAuth" => false, "fileUpload" => true),
        "unique_id" => array("canUpdate" => false, "needAuth" => false),
        "topfive_id" => array("canUpdate" => false, "needAuth" => false, "authToken" => true, "postIgnore" => true)
    );

    protected $table = "topfive";
    protected $error;
    protected $db;

    private $debug;

    public function __construct () {
        $this->debug = new Debugger("TopFive.php");
        $this->db = new DbConn();
        $this->db->conn();

        parent::__construct();
    }

    public function get_all () {
        return $this->db->select($this->GET_ALL);
    }

    public function get_by_id ($id) {
        return $this->db->select(preg_replace("/(\d+)/", $this->GET_TOPFIVE_BY_ID, $id));
    }

    public function create ($data) {
        return parent::create($data);
    }

    public function update ($data, $updateBy) {
        return parent::update($data, $updateBy);
    }

    public function delete ($id, $deleteBy) {
        return parent::delete($id, $deleteBy);
    }

    function __destruct() {
        $this->db->close();
    }
} 