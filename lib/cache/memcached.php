<?php
/*
* Copyright (c) 2012 onwards Christopher Tombleson <chris@cribznetwork.com>
*
* Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
* documentation files (the "Software"), to deal in the Software without restriction, including without limitation
* the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
* and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
* TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
* THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
* CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
* DEALINGS IN THE SOFTWARE.
*/
/**
* @package CribzLib
* @subpackage CribzCache
* @author Christopher Tombleson <chris@cribznetwork.com>
* @copyright Copyright 2012 onwards
*/
class CribzCache_Memcached implements CribzCacheInterface {
    /**
    * Memcached
    *
    * @var memcached object
    */
    protected $memcached;

    /**
    * Cache
    *
    * @var array
    */
    private $cache = array();

    /**
    * Expires
    *
    * @var int
    */
    private $expires = 3600;

    /**
    * Prefix
    *
    * @var string
    */
    private $prefix = 'cribzcache_';

    /**
    * Construct
    *
    * @param array $options     Options for caching. Options are servers, expires, prefix.
    */
    function __construct($options) {
        $this->memcached = new Memcached();

        foreach ($options['servers'] as $server) {
            $server_parts = explode(':', $server);
            $host = $server_parts[0];
            $port = empty($server_parts[1]) ? 11211 : $server_parts[1];
            $weight = empty($server_parts[2]) ? null : $server_parts[2];

            $server = $this->memcache->addServer($host, $port, $weight);

            if (!$server) {
                throw new CribzCacheMemcachedException("Unable to connect to memcache server @ " . $host . " on port: " . $port, 0);
            }
        }

        $this->expires = $options['expires'];
        $this->prefix = $options['prefix'];
    }

    /**
    * Get
    * Gat an item from the cache.
    *
    * @param string $name   Name of item.
    * @return value of item or false.
    */
    function get($name) {
        if (!$this->isCached($name)) {
            return false;
        }

        return $this->memcache->get($this->cache[$name]);
    }

    /**
    * Is Cached
    * Check if an item is cached.
    *
    * @param string $name   Name of itme to check.
    * @return true if cached or false.
    */
    function isCached($name) {
        if (isset($this->cache[$name])) {
            return true;
        }

        return false;
    }

    /**
    * Set
    * Add an item to the cache.
    *
    * @param string $name   Name of item.
    * @param mixed  $value  Value of item.
    * @return true on success or false on failure.
    */
    function set($name, $value) {
        if (isset($this->cache[$name])) {
            $key = $this->cache[$name];
        } else {
            $key = uniqid($this->prefix, true);
        }

        $result = $this->memcache->set($key, $value, $this->expires);

        if (!$result) {
            return false;
        }

        $this->cache[$name] = $key;
        return true;
    }

    /**
    * Remove
    * Remove an item from the cache.
    *
    * @param string $name   Name of item to remove.
    * @return true on success or false on failure.
    */
    function remove($name) {
        if (!$this->isCached($name)) {
            return false;
        }

        unset($this->cache[$name]);
        return $this->memcache->delete($this->cache[$name]);
    }

    /**
    * Purge
    * Purges the whole cache.
    *
    * @return true.
    */
    function purge() {
        foreach ($this->cache as $key) {
            $this->memcache->delete($key);
        }

        $this->cache = array();
        return true;
    }
}
class CribzCacheMemcachedException extends CribzException {}
?>
