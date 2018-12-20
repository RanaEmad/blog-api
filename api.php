<?php
require_once 'models/database.php';
require_once 'core/input.php';
require_once 'helpers/log_helper.php';
require_once 'controllers/blog.php';
class Api {
    protected $db_config;
    protected $response;
    protected $input;
    protected $log_data;
    protected $blog;
    public function __construct($db_config){
        $this->db_config=$db_config;
        $this->blog=new Blog(new Database($db_config, "articles"));
        $this->input=new Input();
        $this->log_data["username"]=NULL;
        $this->log_data["id"]=NULL;
    }
    public function create(){
        $this->log_data["function"]=__FUNCTION__;
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
            $id=$this->blog->create($data);
            $this->log_data["id"]=$id;
            return $this->respond_success(array("id"=>$id));
        }
        return $this->respond_fail("Authentication failed");
    }
    public function update(){
        $this->log_data["function"]=__FUNCTION__;
        if($this->authenticate()){
            $id=$this->input->parse("id",array("required","numeric"));
            if($id["result"]){
                $id=  $id['value'];
                $this->log_data["id"]=$id;
                $one= $this->blog->get_one($id);
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
                     $this->blog->update($id,$data);   
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
        $this->log_data["function"]=__FUNCTION__;
        $blogs=$this->blog->get_all();
        return $this->respond_success($blogs);
    }
    public function get_one(){
        $this->log_data["function"]=__FUNCTION__;
        if(!empty($_GET["id"])){
            $id=  $this->input->get("id");
            if(!$id["result"]){
                return $this->respond_fail($id['error']);
            }
            $this->log_data["id"]=$id["value"];
            $blog= $this->blog->get_one($id["value"]);
            if(!empty($blog)){
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
        $this->log_data["function"]=__FUNCTION__;
        if($this->authenticate()){
            $id=  $this->input->parse("id",array("required","numeric"));
            if($id['result']){
                $id=$id['value'];
                $this->log_data["id"]=$id;
                $one= $this->blog->get_one($id);
                if(!empty($one)){
                    $this->blog->soft_delete($id);
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
            require_once 'models/auth_model.php';
            $auth_model= new Auth_model($this->db_config, "credentials");
            $username=$_SERVER["PHP_AUTH_USER"];
            $this->log_data["username"]=$username;
            $password=  $_SERVER["PHP_AUTH_PW"];
            $user= $auth_model->get_user($username);
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
        elseif($this->log_data["function"]=="get_all"){
            $this->response['data']=[];
        }
        $log_action= $this->log_data["function"]." enpoint was executed successfully";
        if($this->log_data["id"]){
            $log_action .=" for record with id: ".$this->log_data["id"];
        }
        log_actions($this->db_config,$log_action, $this->log_data["username"]);
        return json_encode($this->response);
    }
    protected function respond_fail($errors){
        header('Content-Type: application/json');
        $this->response['result']="fail";
        $this->response['errors']=$errors;
        $log_action= $this->log_data["function"]." enpoint failed to execute with error: $errors";
        if($this->log_data["id"]){
            $log_action .=" for record with id: ".$this->log_data["id"];
        }
        log_actions($this->db_config,$log_action, $this->log_data["username"]);
        return json_encode($this->response);
    }
}
