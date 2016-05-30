<?php

class TopFive extends Entity {

    private $GET_TOPFIVE_BY_ID = 'select * from topfive where topfive_id = ${1}';
    private $GET_ALL = 'select * from topfive';
    private $UPDATE_TOPFIVE_BY_ID = 'topfive_id = ${1}';
    private $DELETE_TOPFIVE = 'topfive_id = ${1}';

    protected $attrs = array(
        "user_id" => false, "type" => false, "id" => false, "name" => true,
        "email" => true, "city" => true, "img_src" => true, "unique_id" => false
    );

    protected $table = "topfive";
    protected $error;
    protected $db;

    public function __construct () {
        $this->db = new DbConn();
        $this->db->conn();
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
        $data = json_decode($data, true);
        if ($data === null) {
            $status = '{"status": 500, "message": "Invalid data body object"}';
        }
        else if (isset($data['topfive_id'])) {
            $status = $this->update_by_id($data);
        }
        else {
            $status = '{"status": 500, "message": "Topfive Update failed. No update type declaration."}';
        }

        return $status;
    }

    public function delete ($id) {
        return $this->db->delete("topfive", preg_replace("/(\d+)/", $this->DELETE_TOPFIVE, $id)) ?
            '{"status": 200, "message": "Topfive deleted"}' : '{"status": 500, "message": "Topfive could not be deleted"}';
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