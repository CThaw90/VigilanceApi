<?php

class TopFiveController {

    private $topfive;
    public function __construct() {
        $this->topfive = new TopFive();
    }

    public function all () {
        return $this->topfive->get_all();
    }

    public function get ($id) {
        return $this->topfive->get_by_id($id);
    }

    public function post ($data) {
        return $this->topfive->create($data);
    }

    public function put ($data) {
        return $this->topfive->update($data);
    }

    public function delete ($id) {
        return $this->topfive->delete($id);
    }
} 