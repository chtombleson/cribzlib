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
* @subpackage   Cribz Database Cache
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzDatabaseCache {
    /**
    * Memcached
    *
    * @var CribzMemcached
    */
    private $memcached;

    /**
    * Cache
    *
    * @var array
    */
    private $cache = array();

    /**
    * Constructor
    * Create a new instance of Cribz Database Cache
    *
    * @param array $memcachedhosts      Array of memcached servers
    */
    function __construct($memcachedhosts) {
       $cribzlib = new CribzLib();
       $cribzlib->loadModule('Memcached');
       $this->memcached = new CribzMemcached();
       $this->memcached->addServers($memcachedhosts);
    }

    /**
    * Cache
    * Cache a sql query and it's result.
    *
    * @param string $sql        The Sql Query
    * @param mixed  $result     The result of the query
    *
    * @return string hash of sql query to be used to reference the query
    */
    function cache($sql, $result) {
        $hash = md5($sql);
        $this->cache[$hash] = $sql;
        $this->memcached->add($hash, $result);
        return $hash;
    }

    /**
    * Delete
    * Delete a query from the cache.
    *
    * @param string $hash   Hash of the sql query
    *
    * @return true on success or false on error
    */
    function delete($hash) {
        if ($this->is_cached($hash)) {
            unset($this->cache[$hash]);
            return $this->memcached->delete($hash);
        }
        return false;
    }

    /**
    * Get
    * The the results of a cached query.
    *
    * @param string $hash   Hash of the query you want to get the result for.
    *
    * @return mixed the result of the query or false on error
    */
    function get($hash) {
        if ($this->is_cached($hash)) {
            return $this->memcached->get($hash);
        }
        return false;
    }

    /**
    * Is Cached
    * Check to see if a hash is cached.
    *
    * @param string $hash   Hash to check
    *
    * @return true if hash exists or false if it does not exist
    */
    function is_cached($hash) {
        if (in_array($hash, array_keys($this->cache))) {
            return true;
        }
        return false;
    }
}
?>
