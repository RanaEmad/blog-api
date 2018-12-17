<?php

class Blog {
    protected $db;
    protected $response;
    public function __construct($db){
        $this->db=$db;
        $this->response['result']="success";
    }
    public function get_all(){
        $all= $this->db->get_all();
        $this->response['data']=  json_encode($all);
        return json_encode($this->response);
    }
    public function get_one(){
        if(!empty($_GET["id"]) && is_numeric($_GET["id"])){
            $id=$_GET['id'];
            $one= $this->db->get_one($id);
            if(!empty($one)){
                $this->response['data']=  json_encode($one);
            }
            else{
                $this->response['result']="fail";
                $this->response['errors']="Record not found";
            }
        }
        else{
            $this->response['result']="fail";
            $this->response['errors']="Missing or Invalid Parameters";
        }
        return json_encode($this->response);
    }
}
