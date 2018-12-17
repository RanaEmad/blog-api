<?php
require 'config/routes.php';
class Router{
    private $routes;
    public function __construct($routes) {
        $this->routes=$routes;
    }
    public function get_method(){
        $request_method=$_SERVER["REQUEST_METHOD"];
        $url = explode('/', trim(parse_url($_SERVER["REQUEST_URI"])["path"]));
        $method="";
        if(count($url)>2){
            $method=$url[2];
            if(!empty($this->routes[$request_method]) && !empty($this->routes[$request_method][$method])){
                return $this->routes[$request_method][$method];
            }
        }
        return FALSE;
        
    }
}