<?php

require_once __DIR__ . "/../../../../vendor/autoload.php";

use Interaxiom\Api;

$applicationPublic	= "public-key";
$applicationSecret	= "private-key";
$applicationKey		= "application-key";
$endpoint		= "api-endpoint";

$api = new Api(
	$applicationPublic,
	$applicationSecret,
	$endpoint,
	$applicationKey
);
try {
	$response = $api->get("/me");
}
catch ( Exception $ex ) {
	$response['error'] = 1;
	$response['message'] =  $ex->getMessage();
}

print_r($response);
