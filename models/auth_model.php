<?php
class Auth_model extends Database{
     public function get_user($username){
        $query = "SELECT * FROM ".$this->table." WHERE username='".$username."' ;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result=$stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
}

