<?php
/*
*   This file is part of CribzLib.
*
*    CribzLib is free software: you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or
*    (at your option) any later version.
*
*    CribzLib is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with CribzLib.  If not, see <http://www.gnu.org/licenses/>.
*/
/**
* @package      CribzLib
* @subpackage   Cribz Restful Client
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzRestful_Client {
    /**
    * Url
    *
    * @var string
    */
    private $url;

    /**
    * Method
    *
    * @var string
    */
    private $method;

    /**
    * Port
    *
    * @var int
    */
    private $port;

    /**
    * Timeout
    *
    * @var int
    */
    private $timeout;

    /**
    * Headers
    *
    * @var array
    */
    private $headers;

    /**
    * User Agent
    *
    * @var string
    */
    private $useragent = 'Cribzlib/RestfulClient';

    /**
    * Constructor
    * Create a new instance of CribzRestful_Client
    *
    * @param string $url        URL for rest api.
    * @param string $method     HTTP method (GET, POST, DELETE).
    * @param int    $port       Port to connect to. (Optional)
    * @param int    $timeout    Timeout on connection. (Optional)
    * @param array  $header     Extra headers to be sent in curl request. (Optional)
    *
    * @return throws CribzRestful_ClientException if curl extension is not loaded.
    */
    function __construct($url, $method, $port=80, $timeout=15, $headers=array()) {
        if (!extension_loaded('curl')) {
            throw new CribzRestul_ClientException("The Curl Extension for PHP is need to make Restful calls.", 0);
        }

        $this->url = $url;
        $this->method = strtoupper($method);
        $this->port = $port;
        $this->timeout = $timeout;
        $this->headers = $headers;
    }

    /**
    * Execute
    * Call the Rest Api and return the response
    *
    * @param array $params  Query Parameters.
    *
    * @return string response on success or throws CribzRestful_ClientException on error.
    */
    function execute($params=array()) {
        if (!in_array($this->method, array('POST', 'GET', 'DELETE'))) {
            throw new CribzRestful_ClientException("Method {$this->method} is not a valid request method.", 1);
        }

        if ($this->method == 'GET') {
            if (!empty($params)) {
                $this->url .= '?'.$this->build_query_string($params);
            }
        }

        $options = array();
        $options[CURLOPT_PORT] = $this->port;
        $options[CURLOPT_USERAGENT] = $this->useragent;
        $options[CURLOPT_TIMEOUT] = $this->timeout;
        $options[CURLOPT_RETURNTRANSFER] = true;

        if (!empty($this->headers)) {
            $options[CURLOPT_HTTPHEADER] = $this->headers;
        }

        if ($this->method == 'POST' || $this->method == 'DELETE') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $params;
        }

        if ($this->method == 'DELETE') {
            $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
        }

        $curl = curl_init($this->url);
        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $header = curl_getinfo($curl);
        $errornum = curl_errno($curl);
        $errormsg = curl_error($curl);

        curl_close($curl);

        if (!empty($errornum)) {
            throw new CribzRestful_ClientException("Curl Error: {$errormsg} Error no.{$errornum}", 2);
        }

        if ($header['http_code'] != 200) {
            throw new CribzRestful_ClientException("HTTP header returned a code of {$header['http_code']}", 3);
        }

        return $response;
    }

    /**
    * Build Query String
    * Create a string to be used in a GET request
    *
    * @param array $params Query Parameters.
    *
    * @return string urlencoded query string.
    */
    private function build_query_string($params) {
        $querystr = '';

        foreach ($params as $key => $value) {
            $querystr .= $key.'='.$value.'&';
        }

        $querystr = rtrim($querystr, '&');
        return urlencode($querystr);
    }
}
class CribzRestful_ClientException extends CribzException {}
?>
