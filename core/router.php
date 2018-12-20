<?php
require 'config/routes.php';
/**
 * Router class parses the url and calls the corresponding endpoint
 */
class Router{
    private $routes;
    public function __construct($routes) {
        $this->routes=$routes;
    }
    public function get_method(){
        $request_method=$_SERVER["REQUEST_METHOD"];
        $url=  trim(trim($_SERVER["REQUEST_URI"]),"/");
        $url=  explode("?",$url);
        $method="";
        $method=$url[0];
        if(!empty($this->routes[$request_method]) && !empty($this->routes[$request_method][$method])){
            return $this->routes[$request_method][$method];
        }
        return FALSE;
        
    }
}