<?php
class Input{
    protected $field;
    protected $error;
    public function __construct() {
        $this->error="";
    }

    public function post($field, $validations=NULL){
        $response["result"]=TRUE;
        $this->field=$field;
        $value= htmlspecialchars(trim($_POST[$field]));
        if($validations){
            foreach ($validations as $one){
                $valid=$this->{$one}($value);
                if(!$valid){
                    $response["result"]=FALSE;
                    $response["error"]=  $this->error;
                    return $response;
                }
            }
        }
        $response["value"]=$value;
        return $response;
    }
    public function get($field, $validations=NULL){
        $response["result"]=TRUE;
        $this->field=$field;
        $value= htmlspecialchars(trim($_GET[$field]));
        if($validations){
            foreach ($validations as $one){
                $valid=$this->{$one}($value);
                if(!$valid){
                    $response["result"]=FALSE;
                    $response["error"]=  $this->error;
                    return $response;
                }
            }
        }
        $response["value"]=$value;
        return $response;
    }
    public function parse($field, $validations=NULL){
        parse_str(file_get_contents("php://input"),$parsed_vars);
        if(!empty($parsed_vars[$field])){
            $response["result"]=TRUE;
            $this->field=$field;
            $value= htmlspecialchars(trim($parsed_vars[$field]));
            if($validations){
                foreach ($validations as $one){
                    $valid=$this->{$one}($value);
                    if(!$valid){
                        $response["result"]=FALSE;
                        $response["error"]=  $this->error;
                        return $response;
                    }
                }
            }
            $response["value"]=$value;
        }
        else{
            $response["result"]=FALSE;
            $response["error"]=  "Missing or Invalid Parameters";
        }
        return $response;
    }
    public function required($value){
        if($value && $value!=""){
            return TRUE;
        }
        $this->error="The $this->field field is required";
        return FALSE;
    }
    public function numeric($value){
        if(is_numeric($value)){
            return TRUE;
        }
        $this->error="The $this->field field must be numeric";
        return FALSE;
    }
    public function max_length($value){
        $len=200;
        if(strlen($value)<=$len){
            return TRUE;
        }
        $this->error="The maximum allowed no of characters for the $this->field field is $len";
        return FALSE;
    }
    
}