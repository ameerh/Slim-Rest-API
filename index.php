<?php

require 'vendor/autoload.php';

$app = new \Slim\Slim();
$db = getConnection();

$app->config(array(
    'debug' => true,
    'templates.path' => '../templates'
));
$settingValue = $app->config('templates.path'); //returns "../templates"

$app->get('/', function () {
    echo "Hello World";
});

$app->post('/service1', function () use ($app) {
	addService1($app);
});

$app->run();



function addService1($app) {
    $service1 = json_decode($app->request->getBody());
    $sql = "INSERT INTO service1 (applicant_name, applicant_cnic) VALUES (:applicant_name, :applicant_cnic)";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("applicant_name", $service1->applicant_name);
        $stmt->bindParam("applicant_cnic", $service1->applicant_cnic);
        $stmt->execute();
        $service1->id = $db->lastInsertId();
        //Save Image
	    save_image($service1->cnic_front, "images/service1", $service1->id);
        //Save Image
        $db = null;
        echo json_encode($service1);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function save_image($img, $fullpath, $id)
{
	$uniqid = uniqid();
	while(file_exists($fullpath."/".$id.".jpeg"));
	$fp = fopen($fullpath."/".$id.".jpeg",'x');
	fwrite($fp, $img);
	fclose($fp);
	return $uniqid;
}

function getConnection() {
    $dbhost="127.0.0.1";
    $dbuser="root";
    $dbpass="";
    $dbname="test";
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);  
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}

?>