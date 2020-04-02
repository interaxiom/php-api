<?php

require_once __DIR__ . "/../../../../vendor/autoload.php";

use Interaxiom\Api;

$applicationKey     = "applicationKey";
$applicationSecret  = "applicationSecret";
$consumer_key       = "consumer_key";
$endpoint           = "myaccount";

$api = new Api(
	$applicationKey,
	$applicationSecret,
	$endpoint,
	$consumer_key
);
try {
	$response = $api->get("/me");
}
catch ( Exception $ex ) {
	$response['error'] = 1;
	$response['message'] =  $ex->getMessage();
}

print_r($response);
