<?php

require_once __DIR__ . "/../../../../vendor/autoload.php";

use Interaxiom\Api;

$applicationPublic	= "public-key";
$applicationSecret	= "secret-key";
$applicationKey		= "application-key";
$endpoint			= "myaccount";

$api = new Api(
	$applicationPublic,
	$applicationSecret,
	$endpoint,
	$applicationKey
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
