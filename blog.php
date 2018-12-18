<?php
require_once 'core/input.php';
class Blog {
    protected $db;
    protected $response;
    protected $input;
    public function __construct($db){
        $this->db=$db;
        $this->input=new Input();
        $this->response['result']="success";
    }
    public function create(){
        if($this->authenticate()){
            $title=$this->input->post("title",array("required"));
            if(!$title["result"]){
                $this->response["result"]="fail";
                $this->response["errors"]=$title['error'];
                return json_encode($this->response);
            }
            $text=$this->input->post("text",array("required"));
            if(!$text["result"]){
                $this->response["result"]="fail";
                $this->response["errors"]=$title['error'];
                return json_encode($this->response);
            }
            $data=array(
                "title"=>$title["value"],
                "text"=>$text["value"],
                "deleted"=>0
            );
            $this->db->insert($data);
        }
        return json_encode($this->response);
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
                    }
                }
                else{
                    $this->response['result']="fail";
                    $this->response['errors'][]="Record not found";
                }
            }
            else{
                $this->response['result']="fail";
                $this->response['errors']=$id['error'];
            }
        }
        echo json_encode($this->response);
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
        $this->response['data']=  json_encode($blogs);
        return json_encode($this->response);
    }
    public function get_one(){
        if(!empty($_GET["id"])){
            $id=  $this->input->get("id");
            if(!$id["result"]){
                $this->response["result"]="fail";
                $this->response["errors"]=$id["error"];
                return json_encode($this->response);
            }
            $one= $this->db->get_one($id["value"]);
            if(!empty($one)){
                $blog=array(
                    "title"=>  htmlspecialchars($one["title"]),
                    "text"=>  htmlspecialchars($one["text"])
                );
                $this->response['data']=  json_encode($blog);
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
            else{
                $this->response['result']="fail";
                $this->response['errors']="Authentication failed";
            }
        }
        else{
            $this->response['result']="fail";
            $this->response['errors']="Authentication failed";
        }
        return FALSE;
    }
}
