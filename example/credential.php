<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Interaxiom\Api;

$applicationKey = "applicationKey";
$applicationSecret = "applicationSecret";
$redirection = "https://myaccount.interaxiom.com.au/#!api/";
$endpoint = 'myaccount';

$rights = array(
    array(
        'method' => 'DELETE',
        'path' => '/*'
	),
    array(
        'method' => 'GET',
        'path' => '/*'
	),
    array(
        'method' => 'POST',
        'path' => '/*'
	),
    array(
        'method' => 'PUT',
        'path' => '/*'
	)
);

$api = new Api($applicationKey, $applicationSecret, $endpoint);
$credentials = $api->requestCredentials($rights, $redirection);

?>
    
<p>You have generated a new consumer key for your application on endpoint: <b><?php echo $endpoint; ?></b>.</p>
<ul>
    <li><b>Application Key:</b><?php echo $applicationKey; ?></li>
    <li><b>Application Secret:</b><?php echo $applicationSecret; ?></li>
    <li><b>Consumer Key:</b><?php echo $credentials['consumerKey']; ?></li>
</ul>
<p>Validation URL: <a href="<?php echo $credentials['validationUrl']; ?>"><?php echo $credentials['validationUrl']; ?></a></p>
