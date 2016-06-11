<?php

class TopFive extends Entity {

    private $GET_TOPFIVE_BY_ID = 'select * from topfive where topfive_id = ${1}';
    private $GET_ALL = 'select * from topfive';
    private $UPDATE_TOPFIVE_BY_ID = 'topfive_id = ${1}';
    private $DELETE_TOPFIVE = 'topfive_id = ${1}';

    protected $attrs = array(
        "credential_id" => array("canUpdate" => false, "authorize" => true),
        "type" => array("canUpdate" => true, "needAuth" => false),
        "id" => array("canUpdate" => false, "needAuth" => false),
        "name" => array("canUpdate" => true, "needAuth" => false),
        "email" => array("canUpdate" => true, "needAuth" => false),
        "city" => array("canUpdate" => true, "needAuth" => false),
        "img_src" => array("canUpdate" => true, "needAuth" => false),
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

    public function update ($data) {
        $status = null;
        $data = $this->parse_request_body($data);
        if ($data === null || !count($data)) {
            $status = '{"status": 500, "message": "Invalid data body object"}';
        }
        else if (isset($data['topfive_id'])) {
            $status = $this->isAuthorized($data, $this->attrs) ? $this->update_by_id($data) : $this->auth_error;
        }
        else {
            $status = '{"status": 500, "message": "Topfive Update failed. No update type declaration."}';
        }

        return $status;
    }

    public function delete ($id) {
        if ($this->isAuthorized(array("topfive_id" => $id), $this->attrs)) {
            return $this->db->delete("topfive", preg_replace("/(\d+)/", $this->DELETE_TOPFIVE, $id)) ?
                '{"status": 200, "message": "Topfive deleted"}' : '{"status": 500, "message": "Topfive could not be deleted"}';
        }

        return $this->auth_error;
    }

    private function update_by_id ($data) {
        return $this->db->update("topfive", $this->transform($data, $this->attrs, false),
            preg_replace("/(\d+)/", $this->UPDATE_TOPFIVE_BY_ID, $data['topfive_id'])) ?
            '{"status": 200, "message": "Topfive updated successfully"}' :
            '{"status": 500, "message": "Topfive update failed"}';
    }

    function __destruct() {
        $this->db->close();
    }
} 