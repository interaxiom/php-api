<?php

require_once __DIR__ . "/../../../../vendor/autoload.php";

use Interaxiom\Api;

$public_key		= "";
$private_key		= "";
$application_key	= "";
$endpoint		= "myaccount";

$api = new Api(
	$public_key,
	$private_key,
	$endpoint,
	$application_key
);
try {
	$response = $api->get('/test/apiTest', array(
		'test1' => 1,
		'test2' => 2,
		'test3' => 3,
	));
}
catch ( Exception $ex ) {
	$response['error'] = 1;
	$response['message'] =  $ex->getMessage();
}

print_r($response);
