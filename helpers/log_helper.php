<?php
require_once 'models/database.php';
function log_actions($db_config,$action,$username=NULL){
    $data=array(
    "remote_addr"=>$_SERVER["REMOTE_ADDR"],
    "timestamp"=>date("Y-m-d H:i:s"),
    "action"=>$action,
    "username"=>$username
    );
    //insert in db
    $log_model= new Database($db_config, "logs");
    $log_model->insert($data);
}
