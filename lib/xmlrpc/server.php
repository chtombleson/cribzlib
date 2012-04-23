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
* @subpackage   Cribz Xmlrpc Server
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzXmlrpcServer {

    /**
    * Server
    *
    * @var xmlrpc_server
    */
    private $server;

    /**
    * Methods
    *
    * @var array
    */
    private $methods = array();

    /**
    * Constructor
    * Create a new Xmlrpc server
    */
    function __construct() {
        $this->server = xmlrpc_server_create();
    }

    /**
    * Add Method
    * Add a method to the server.
    *
    * @param string $name       Name for method on XMLRPC Server.
    * @param mixed  $method     String with function name or array with class name and method.
    *
    * @return true on success or false on error.
    */
    function addMethod($name, $method) {
        $this->methods[] = $name;
        $register = xmlrpc_server_register_method($this->server, $name, $method);
        return $register;
    }

    /**
    * Call Method
    * Call a method on the server.
    *
    * @param string $xml        The xml data from the client.
    * @param array  $userdata   User data (Optional).
    * @param array  $options    Output Options (Optional).
    *
    * @return the response from the method.
    */
    function callMethod($xml, $userdata = null, $options = null) {
        $response = xmlrpc_server_call_method($this->server, $xml, $userdata, $options);
        return $response;
    }

    /**
    * Close
    * Close the XMLRPC server.
    *
    * @return true on success or false on error.
    */
    function close() {
        return xmlrpc_server_destroy($this->server);
    }

    /**
    * Get Methods
    * Get an array of method available on the server.
    *
    * @return array
    */
    function getMethods() {
        return $this->methods;
    }
}
?>
