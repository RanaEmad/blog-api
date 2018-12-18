<?php
require_once 'core/input.php';
class Blog {
    protected $db;
    protected $response;
    protected $input;
    public function __construct($db){
        $this->db=$db;
        $this->input=new Input();
    }
    public function create(){
        if($this->authenticate()){
            $title=$this->input->post("title",array("required"));
            if(!$title["result"]){
                return $this->respond_fail($title['error']);
            }
            $text=$this->input->post("text",array("required"));
            if(!$text["result"]){
                return $this->respond_fail($text['error']);
            }
            $data=array(
                "title"=>$title["value"],
                "text"=>$text["value"],
                "deleted"=>0
            );
            $this->db->insert($data);
            return $this->respond_success();
        }
        return $this->respond_fail("Authentication failed");
    }
    public function update(){
        if($this->authenticate()){
            $id=$this->input->parse("id",array("required","numeric"));
            if($id["result"]){
                $id=  $id['value'];
                $one= $this->db->get_one($id);
                if(!empty($one)){
                    $data=[];
                    parse_str(file_get_contents("php://input"),$parsed_vars);
                    if(!empty($parsed_vars["title"])){
                        $data["title"]=  htmlspecialchars(trim($parsed_vars["title"]));
                    }
                    if(!empty($parsed_vars["text"])){
                        $data["text"]=  htmlspecialchars(trim($parsed_vars["text"]));
                    }
                    if($data){
                     $this->db->update($id,$data);   
                     return $this->respond_success();
                    }
                    else{
                        return $this->respond_fail("No data to be updated");
                    }
                }
                else{
                    return $this->respond_fail("Record not found");
                }
            }
            else{
                return $this->respond_fail($id['error']);
            }
        }
        return $this->respond_fail("Authentication Failed");
    }
    public function get_all(){
        $all= $this->db->get_all();
        $blogs=[];
        foreach ($all as $one){
            $blogs[]=array(
                "title"=>htmlspecialchars($one["title"]),
                "text"=>htmlspecialchars($one["text"])
            );
        }
        return $this->respond_success($blogs);
    }
    public function get_one(){
        if(!empty($_GET["id"])){
            $id=  $this->input->get("id");
            if(!$id["result"]){
                return $this->respond_fail($id['error']);
            }
            $one= $this->db->get_one($id["value"]);
            if(!empty($one)){
                $blog=array(
                    "title"=>  htmlspecialchars($one["title"]),
                    "text"=>  htmlspecialchars($one["text"])
                );
                return $this->respond_success($blog);
            }
            else{
                return $this->respond_fail("Record not found");
            }
        }
        else{
            return $this->respond_fail("Missing or Invalid Parameters");
        }
    }
    public function delete(){
        if($this->authenticate()){
            $id=  $this->input->parse("id",array("required","numeric"));
            if($id['result']){
                $id=$id['value'];
                $one= $this->db->get_one($id);
                if(!empty($one)){
                    $this->db->soft_delete($id);
                    return $this->respond_success();
                }
                else{
                    return $this->respond_fail("Record not found");
                }
            }
            else{
                return $this->respond_fail("Missing or Invalid Parameters");
            }
        }
        return $this->respond_fail("Authentication Failed");
    }
    protected function authenticate(){
        if(!empty($_SERVER["PHP_AUTH_USER"]) && !empty($_SERVER["PHP_AUTH_PW"])){
            $username=$_SERVER["PHP_AUTH_USER"];
            $password=  $_SERVER["PHP_AUTH_PW"];
            $user= $this->db->get_user($username);
            if(!empty($user)){
                $user_password= base64_encode($user["username"].$user["token"].$user["username"]);
                if($user_password==$password){
                    return TRUE;
                }
            }
        }
        return FALSE;
    }
    protected function respond_success($data=NULL){
        header('Content-Type: application/json');
        $this->response['result']="success";
        if($data){
            $this->response['data']=$data;
        }
        return json_encode($this->response);
    }
    protected function respond_fail($errors){
        header('Content-Type: application/json');
        $this->response['result']="fail";
        $this->response['errors']=$errors;
        return json_encode($this->response);
    }
}
