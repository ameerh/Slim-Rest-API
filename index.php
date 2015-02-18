<?php

require 'vendor/autoload.php';

$app = new \Slim\Slim();

$app->config(array(
    'debug' => true,
    'templates.path' => '../templates'
));
$settingValue = $app->config('templates.path'); //returns "../templates"
$app->post('/', function () use ($app){
    $token = json_decode($app->request->getBody());
    $response = file_get_contents("https://graph.facebook.com/v2.2/me/friends?access_token=".$token->accessToken."&format=json&method=get&pretty=1&suppress_http_code=1");
    $array = json_decode($response, true);
    $friends = array();
    array_push($friends, $array["data"]);
    
    while(isset( $array["paging"]["next"])){
        $response = file_get_contents($array["paging"]["next"]);
        $array = json_decode($response, true);
        array_merge($friends, $array["data"]);
    }
    $app->contentType('application/json');
    $app->response->body(json_encode(array_values($friends[0])));  
});


$app->run();

?>