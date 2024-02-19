<?php

# Copyright (c) 2011-2020, Interaxiom.
# All rights reserved.
#
# Redistribution and use in source and binary forms, with or without
# modification, are permitted provided that the following conditions are met:
#
#   * Redistributions of source code must retain the above copyright
#     notice, this list of conditions and the following disclaimer.
#   * Redistributions in binary form must reproduce the above copyright
#     notice, this list of conditions and the following disclaimer in the
#     documentation and/or other materials provided with the distribution.
#   * Neither the name of Interaxiom nor the
#     names of its contributors may be used to endorse or promote products
#     derived from this software without specific prior written permission.
#
# THIS SOFTWARE IS PROVIDED BY INTERAXIOM AND CONTRIBUTORS ``AS IS'' AND ANY
# EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
# WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
# DISCLAIMED. IN NO EVENT SHALL INTERAXIOM AND CONTRIBUTORS BE LIABLE FOR ANY
# DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
# (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
# LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
# ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
# (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
# SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
/**
 * This file contains code about \Interaxiom\Api class
 */

namespace Interaxiom;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * Wrapper to manage login and exchanges with simpliest Interaxiom API
 *
 * This class manage how works connections to the simple Interaxiom API with
 * login method and call wrapper
 * Http connections use guzzle http client api and result of request are
 * object from this http wrapper
 *
 * @package  Interaxiom
 * @category Interaxiom
 */
 
class Api
{
    /**
     * Url to communicate with Interaxiom API
     *
     * @var array
     */
	 
    private $endpoints = [
        'private' => 'https://api.interaxiom.com.au/v1',
		'myaccount' => 'https://myaccount.interaxiom.com.au/api/v1',
    ];

    /**
     * Contain endpoint selected to choose API
     *
     * @var string
     */
	 
    private $endpoint = null;

    /**
     * Contain public key of the current application
     *
     * @var string
     */
	 
    private $application_public = null;

    /**
     * Contain secret key of the current application
     *
     * @var string
     */
	 
    private $application_secret = null;

    /**
     * Contain application key of the current application
     *
     * @var string
     */
	 
    private $application_key = null;

    /**
     * Contain delta between local timestamp and api server timestamp
     *
     * @var string
     */
	 
    private $time_delta = null;

    /**
     * Contain http client connection
     *
     * @var Client
     */
	 
    private $http_client = null;

    /**
     * Construct a new wrapper instance
     *
     * @param string $application_public    public key of your application.
     * @param string $application_secret	secret key of your application.
     * @param string $application_key		identity key of your application.
     * @param string $api_endpoint			name of api selected
     * @param Client $http_client			instance of http client
     *
     * @throws Exceptions\InvalidParameterException if one parameter is missing or with bad value
     */
	 
    public function __construct(
        $application_public,
        $application_secret,
        $api_endpoint,
        $application_key = null,
        Client $http_client = null
    ) {
        if (!isset($api_endpoint)) {
            throw new Exceptions\InvalidParameterException("Endpoint parameter is empty");
        }

        if (preg_match('/^https?:\/\/..*/',$api_endpoint))
        {
          $this->endpoint = $api_endpoint;
        }
        else
        {
          if (!array_key_exists($api_endpoint, $this->endpoints)) {
              throw new Exceptions\InvalidParameterException("Unknown provided endpoint");
          }
          else
          {
            $this->endpoint = $this->endpoints[$api_endpoint];
          }
        }

        if (!isset($http_client)) {
            $http_client = new Client([
                'timeout' => 30,
                'connect_timeout' => 5,
            ]);
        }

        $this->application_public = $application_public;
        $this->application_secret = $application_secret;
        $this->http_client = $http_client;
        $this->application_key = $application_key;
        $this->time_delta = null;
    }           

    /**
     * Calculate time delta between local machine and API's server
     *
     * @throws \GuzzleHttp\Exception\ClientException if http request is an error
     * @return int
     */
	 
    private function calculateTimeDelta()
    {
        if (!isset($this->time_delta)) {
            $response = $this->rawCall(
                'GET',
                "/auth/time",
                null,
                false
            );
            $serverTimestamp = (int)(string)$response->getBody();
            $this->time_delta = $serverTimestamp - (int)\time();
        }

        return $this->time_delta;
    }
	
    /**
     * This is the main method of this wrapper. It will
     * sign a given query and return its result.
     *
     * @param string               $method           HTTP method of request (GET,POST,PUT,DELETE)
     * @param string               $path             relative url of API request
     * @param \stdClass|array|null $content          body of the request
     * @param bool                 $is_authenticated if the request use authentication
     *
     * @return array
     * @throws \GuzzleHttp\Exception\ClientException if http request is an error
     */
	 
    protected function rawCall($method, $path, $content = null, $is_authenticated = true, $headers = null)
    {
        if ( $is_authenticated )
        {
            if (!isset($this->application_public)) {
                throw new Exceptions\InvalidParameterException("Public key parameter is empty");
            }

            if (!isset($this->application_secret)) {
                throw new Exceptions\InvalidParameterException("Secret key parameter is empty");
            }
        }

        $url  =$this->endpoint . $path;
        $request = new Request($method, $url);
        if (isset($content) && $method == 'GET') {
            $query_string = $request->getUri()->getQuery();

            $query = array();
            if (!empty($query_string)) {
                $queries = explode('&', $query_string);
                foreach ($queries as $element) {
                    $key_value_query = explode('=', $element, 2);
                    $query[$key_value_query[0]] = $key_value_query[1];
                }
            }

            $query = array_merge($query, (array)$content);

            // rewrite query args to properly dump true/false parameters
            foreach ($query as $key => $value) {
                if ($value === false) {
                    $query[$key] = "false";
                } elseif ($value === true) {
                    $query[$key] = "true";
                }
            }

            $query = \GuzzleHttp\Psr7\build_query($query);

            $url  =$request->getUri()->withQuery($query);
            $request = $request->withUri($url);
            $body ="";
        } elseif (isset($content)) {
            $body = json_encode($content, JSON_UNESCAPED_SLASHES);

            $request->getBody()->write($body);
        } else {
            $body = "";
        }
        if(!is_array($headers))
        {
            $headers = [];
        }
        $headers['Content-Type']   ='application/json; charset=utf-8';

        if ($is_authenticated) {
			
            if (!isset($this->time_delta)) {
                $this->calculateTimeDelta();
            }
            $now = time() + $this->time_delta;

            $headers['X-Interaxiom-Timestamp'] = $now;

            $headers['X-Interaxiom-Application-Key'] = $this->application_key;
            $headers['X-Interaxiom-Public-Key'] = $this->application_public;
            $headers['X-Interaxiom-Secret-Key'] = $this->application_secret;
        }

        /** @var Response $response */
        return $this->http_client->send($request, ['headers' => $headers]);
    }

    /**
     * Decode a Response object body to an Array
     *
     * @param  Response $response
     *
     * @return array
     */
	 
    private function decodeResponse(Response $response)
    {
        return json_decode($response->getBody(), true);
    }

    /**
     * Wrap call to Interaxiom APIs for GET requests
     *
     * @param string $path    path ask inside api
     * @param array  $content content to send inside body of request
     * @param array  headers  custom HTTP headers to add on the request
     * @param bool   is_authenticated   if the request need to be authenticated
     *
     * @return array
     * @throws \GuzzleHttp\Exception\ClientException if http request is an error
     */
	 
    public function get($path, $content = null, $headers = null, $is_authenticated = true)
    {
        if(preg_match('/^\/[^\/]+\.json$/', $path))
        {
          // Schema description must be access without authentication
          return $this->decodeResponse(
              $this->rawCall("GET", $path, $content, false, $headers)
          );
        }
        else
        {
          return $this->decodeResponse(
              $this->rawCall("GET", $path, $content, $is_authenticated, $headers)
          );
        }
    }

    /**
     * Wrap call to Interaxiom APIs for POST requests
     *
     * @param string $path    path ask inside api
     * @param array  $content content to send inside body of request
     * @param array  headers  custom HTTP headers to add on the request
     * @param bool   is_authenticated   if the request need to be authenticated
     *
     * @return array
     * @throws \GuzzleHttp\Exception\ClientException if http request is an error
     */
	 
    public function post($path, $content = null, $headers = null, $is_authenticated = true)
    {
        return $this->decodeResponse(
            $this->rawCall("POST", $path, $content, $is_authenticated, $headers)
        );
    }

    /**
     * Wrap call to Interaxiom APIs for PUT requests
     *
     * @param string $path    path ask inside api
     * @param array  $content content to send inside body of request
     * @param array  headers  custom HTTP headers to add on the request
     * @param bool   is_authenticated   if the request need to be authenticated
     *
     * @return array
     * @throws \GuzzleHttp\Exception\ClientException if http request is an error
     */
	 
    public function put($path, $content, $headers = null, $is_authenticated = true)
    {
        return $this->decodeResponse(
            $this->rawCall("PUT", $path, $content, $is_authenticated, $headers)
        );
    }

    /**
     * Wrap call to Interaxiom APIs for DELETE requests
     *
     * @param string $path    path ask inside api
     * @param array  $content content to send inside body of request
     * @param array  headers  custom HTTP headers to add on the request
     * @param bool   is_authenticated   if the request need to be authenticated
     *
     * @return array
     * @throws \GuzzleHttp\Exception\ClientException if http request is an error
     */
	 
    public function delete($path, $content = null, $headers = null, $is_authenticated = true)
    {
        return $this->decodeResponse(
            $this->rawCall("DELETE", $path, $content, $is_authenticated, $headers)
        );
    }

    /**
     * Get the current consumer key
     */
	 
    public function getApplicationKey()
    {
        return $this->application_key;
    }

    /**
     * Return instance of http client
     */
	 
    public function getHttpClient()
    {
        return $this->http_client;
    }
}
