<?php
require_once 'models/database.php';
require_once 'blog.php';
require_once 'core/router.php';
$db= new Database($db_config,"articles");
$blog= new Blog($db);
$router= new Router($routes);
$method= $router->get_method();
if($method){
    echo $blog->{$router->get_method()}();
}
else{
    header('Content-Type: application/json');
    $response['result']="fail";
    $response['errors']="Requested url is not found";
    echo json_encode($response);
}
