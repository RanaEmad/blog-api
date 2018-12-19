<?php
class Blog{
    public $blog_model;
    public function __construct($db) {
        $this->blog_model=$db;
    }
    public function create($data){
        return $this->blog_model->insert($data);
    }
    public function update($id,$data){
        $this->blog_model->update($id,$data);
    }
    public function get_all(){
        $all=$this->blog_model->get_all();
        $blogs=[];
        foreach ($all as $one){
            $blogs[]=array(
                "title"=>htmlspecialchars($one["title"]),
                "text"=>htmlspecialchars($one["text"])
            );
        }
        return $blogs;
    }
     public function get_one($id){
        $one=$this->blog_model->get_one($id);
        $blog=[];
        if(!empty($one)){
            $blog=array(
                "title"=>  htmlspecialchars($one["title"]),
                "text"=>  htmlspecialchars($one["text"])
            );
        }
        return $blog;
    }
    public function soft_delete($id){
        $this->blog_model->soft_delete($id);
    }
    
}