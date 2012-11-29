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
class CribzCache {
    /**
    * Cache
    *
    * @var Cache object.
    */
    private $cache;

    /**
    * Construct
    * Create a new instance of Cribz Cache.
    *
    * @param string $type       Type of cache (filesystem, memcached). [Optional]
    * @param array  $options    Options for caching. Options are path, servers, expires, prefix. [Optional]
    */
    function __construct($type = 'filesystem', $options = array()) {
       if (!in_array($type, array('memcached', 'filesystem'))) {
            throw new CribzCacheException("Cache type of " . $type . " does not exist. Use either filesystem or memcached.", 0);
       }

       $defaultfile_options = array(
            'path'    => '/tmp/cribzcache/',
            'expires' => 3600,
            'prefix'  => 'cribzcache_',
       );

       $defaultmem_options = array(
            'servers' => array('127.0.0.1:11211'),
            'expires' => 3600,
            'prefix'  => 'cribzcache_',
       );

       if ($type == 'filesystem') {
            $options = array_merge($options, $defaultfile_options);
       } else {
            $options = array_merge($options, $defaultmem_options);
       }

       $class = 'CribzCache_' . ucfirst($type);

       if (!class_exists($class)) {
            throw new CribzCacheException("Class: " . $class . ", does not exist.", 1);
       }

       $cache = new $class($options);
    }

    /**
    * Get
    * @see cache_interface.php
    * @see filesystem.php
    * @see memcached.php
    */
    function get($name) {
        return $this->cache->get($name);
    }

    /**
    * Set
    * @see cache_interface.php
    * @see filesystem.php
    * @see memcached.php
    */
    function set($name, $value) {
        return $this->cache->set($name, $value);
    }

    /**
    * Remove
    * @see cache_interface.php
    * @see filesystem.php
    * @see memcached.php
    */
    function remove($name) {
        return $this->cache->remove($name);
    }

    /**
    * Purge
    * @see cache_interface.php
    * @see filesystem.php
    * @see memcached.php
    */
    function purge() {
        return $this->cache->purge();
    }

    /**
    * Is Cached
    * @see cache_interface.php
    * @see filesystem.php
    * @see memcached.php
    */
    function isCached($name) {
        return $this->cache->isCached($name);
    }
}
class CribzCacheException extends CribzException {}
?>
