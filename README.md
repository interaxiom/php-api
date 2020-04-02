[![PHP Wrapper for Interaxiom APIs](https://github.com/interaxiom/php-api/blob/master/img/logo.png)](https://packagist.org/packages/interaxiom/php-api)

This PHP package is a lightweight wrapper for Interaxiom APIs. That's the easiest way to use interaxiom.com.au APIs in your PHP applications.

```php
<?php

/**
 * # Visit https://myaccount.interaxiom.com.au/#!user/api
 * to get your credentials
 */
 
require __DIR__ . '/vendor/autoload.php';
use \Interaxiom\Api;

$api = new Api(
    $applicationKey,
    $applicationSecret,
    $endpoint,
    $consumer_key
);
echo "Welcome " . $api->get('/me')['firstname'];

?>
```

Quickstart
----------

To download this wrapper and integrate it inside your PHP application, you can use [Composer](https://getcomposer.org).

Quick integration with the following command:

    composer require interaxiom/php-api

Or add the repository in your **composer.json** file or, if you don't already have
this file, create it at the root of your project with this content:

```json
{
    "name": "Example Application",
    "description": "This is an example of Interaxiom APIs wrapper usage",
    "require": {
        "interaxiom/php-api": "dev-master"
    }
}

```

Then, you can install the Interaxiom API wrapper and dependencies with:

  php composer.phar install

This will install ``interaxiom/php-api`` to ``./vendor``, along with other dependencies
including ``autoload.php``.

How to authenticate as a user?
-----------------------

To communicate with the API endpoints, the SDK uses a token on each request to identify the
user called a consumer key. Once you have created your application, you can use this example
to request and validate your consumer key.

```php
<?php

require __DIR__ . "/vendor/autoload.php";

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

$conn = new Api($applicationKey, $applicationSecret, $endpoint);
$credentials = $conn->requestCredentials($rights, $redirection);

?>
    
<p>You have generated a new consumer key for your application using endpoint: <b><?php echo $endpoint; ?></b>.</p>
<ul>
    <li><b>Application Key:</b><?php echo $applicationKey; ?></li>
    <li><b>Application Secret:</b><?php echo $applicationSecret; ?></li>
    <li><b>Consumer Key:</b><?php echo $credentials['consumerKey']; ?></li>
</ul>
<p>Validation URL: <a href="<?php echo $credentials['validationUrl']; ?>"><?php echo $credentials['validationUrl']; ?></a></p>
```

Interaxiom Examples
-------------------

Do you want to use Interaxiom APIs? Maybe the script you want is already written in the [example](examples/README.md) part of this repository!

How to print API error details?
-------------------------------

Under the hood, ```php-api``` uses [GuzzlePHP 6](http://docs.guzzlephp.org/en/latest/quickstart.html) by default to issue API requests. If everything goes well, it will return the response directly as shown in the examples above. If there is an error like a missing endpoint or object (404), an authentication or authorization error (401 or 403) or a parameter error, the Guzzle will raise a ``GuzzleHttp\Exception\ClientException`` exception. For server-side errors (5xx), it will raise a ``GuzzleHttp\Exception\ServerException`` exception.

You can get the error details with a code like:

```php
<?php

require __DIR__ . '/vendor/autoload.php';
use \Interaxiom\Api;

$api = new Api(
    $applicationKey,
    $applicationSecret,
    $endpoint,
    $consumer_key
);

try {
  echo "Welcome " . $api->get('/me')['firstname'];
}
catch (GuzzleHttp\Exception\ClientException $e) {
    $response = $e->getResponse();
    $responseBodyAsString = $response->getBody()->getContents();
    echo $responseBodyAsString;
}

?>
```

Supported APIs
--------------

The following endpoints are available for public use:

## My Account

 * ```$endpoint = 'myaccount';```
 * Documentation: https://www.interaxiom.com.au/knowledgebase/api/
 * Community support: development@interaxiom.com.au
 * Console: https://myaccount.interaxiom.com.au/#!api/
 * Create application credentials: https://myaccount.interaxiom.com.au/#!user/api

## Related links

 * Contribute: https://github.com/interaxiom/php-api
 * Report bugs: https://github.com/interaxiom/php-api/issues
