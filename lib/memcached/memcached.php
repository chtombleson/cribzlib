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
* @subpackage   Cribz Memcached
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzMemcached {
    
    /**
    * Memcached
    *
    * @var Memcached
    */
    private $memcached;

    /**
    * Construct
    * Create a new instance of CribzMemcached
    */
    function __construct() {
        if (!extension_loaded('memcached')) {
            throw new CribzMemcachedException("Please install the memcached php extension.", 0);
        }
        $this->memcached = new Memcached();
    }

    /**
    * Add Servers
    * Add multiple servers to the memcached server pool
    *
    * @param array $servers     Array of server connection details
    * @return true on success or false on failure.
    */
    function addServers($servers) {
        foreach ($servers as $server) {
            $host = $server['host'];
            $port = empty($server['port']) ? 11211 : $server['port'];
            $weight = empty($server['weight']) ? 0 : $server['weight'];
            if (!$this->addServer($host, $port, $weight)) {
                return false;
            }
        }
        return true;
    }

    /**
    * Add Server
    * Add a server to the memcached server pool
    *
    * @param string $server     Server name eg. memcache.example.com
    * @param int    $port       Port that the server is running on. Optional, Default is 11211
    * @param int    $weight     Weight of the server. Optional, Default is 0
    * @return true on success or false on failure.
    */
    function addServer($server, $port=11211, $weight=0) {
        return $this->memcached->addServer($server, $port, $weight);
    }

    /**
    * Add
    * Add a value to memcached.
    *
    * @param string $key        Key to be stored in memcached.
    * @param mixed  $value      Value to be stored with the key.
    * @param int    $expires    How long is the data valid for.
    * @return true on success or false on failure.
    */
    function add($key, $value, $expires=0) {
        return $this->memcached->add($key, $value, $expires);
    }

    /**
    * Append
    * Append data to a given key.
    *
    * @param string $key        Key to append data to.
    * @param mixed  $value      Value to append to key.
    * @return true on success or false on failure.
    */
    function append($key, $value) {
        return $this->memcached->append($key, $value);
    }

    /**
    * Delete
    * Delete item from memcached.
    *
    * @param string $key    Key to delete.
    * @param int    $time   The amount of time the server will wait to delete the item. Optional, Default is 0
    * @return true on success or false on failure.
    */
    function delete($key, $time = 0) {
        return $this->memcached->delete($key, $time);
    }

    /**
    * Get
    * Get item from memcached.
    *
    * @param string $key        Key to get.
    * @return the item or false on failure.
    */
    function get($key) {
        return $this->memcached->get($key);
    }

    /**
    * Set
    * Store an item in memcached.
    *
    * @param string $key        Key to store item under.
    * @param mixed  $value      Value to store.
    * @param int    $expires    How long the data is valid for. Optional, Default is 0
    * @return true on success or false on failure.
    */
    function set($key, $value, $expires=0) {
        return $this->memcached->set($key, $value, $expires);
    }

    /**
    * Get Server List
    * Get a list of all server in the memcached server pool.
    * @return array of servers in the pool.
    */
    function getServerList() {
        return $this->memcached->getServerList();
    }
}
class CribzMemcachedException extends CribzException {}
?>
