<?php

class Blog {
    private $db;
    private $response;
    public function __construct($db){
        $this->db=$db;
        $this->response['result']="success";
    }
    public function get_all(){
        $all= $this->db->get_all();
        $this->response['data']=  json_encode($all);
        return json_encode($this->response);
    }
}
