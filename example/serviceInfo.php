<?php

require_once __DIR__ . "/../../../../vendor/autoload.php";

use Interaxiom\Api;

$public_key = "public-key";
$private_key = "secret-key";
$application_key = "application-key";
$endpoint = "api-myaccount";

$api = new Api(
	$public_key,
	$private_key,
	$application_key,
	$endpoint
);
try {
	$response = $api->get('/v1/services/info', array(
		'serviceid' => '1086'
	));
}
catch ( Exception $e ) {
	$response['error'] = 1;
	$response['message'] =  $e->getMessage();
}

print_r($response);
