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
class CribzCache_Filesystem implements CribzCacheInterface {
    /**
    * Path
    *
    * @var string
    */
    private $path;

    /**
    * Expires
    *
    * @var int
    */
    private $expires;

    /**
    * Prefix
    *
    * @var string
    */
    private $prefix;

    /**
    * Cache
    *
    * @var array
    */
    protected $cache = array();

    /**
    * Constructor
    *
    * @param array $options     Options for caching. Options are path, expires, prefix.
    */
    function __construct($options) {
        $this->path = realpath($options['path']);
        $this->expires = $options['expires'];
        $this->prefix = $options['prefix'];

        if (!file_exists($this->path) && is_writeable(dirname(dirname($this->path)))) {
            if (!mkdir($this->path)) {
                throw new CribzCacheFilesystemException("Unable to create cache directory.", 0);
            }
        }
    }

    /**
    * Get
    * Gets a cached items value.
    *
    * @param string $name   Name of item to get.
    * @return value or item, false on error or throws CribzCacheFilesystemException.
    */
    function get($name) {
        if (!$this->isCached($name)) {
            return false;
        }

        $path = $this->cache[$name]['path'];
        $serailized = $this->cache[$name]['serailized'];

        $value = file_get_contents($path);

        if (!$value) {
            throw new CribzCacheFilesystemException("Unable to read cache file.", 2);
        }

        return $serailized ? unserialize($value) : $value;
    }

    /**
    * Set
    * Add an item to the cache.
    *
    * @param string $name   Name of item.
    * @param mixed  $value  Value of item.
    * @return true or throws CribzCacheFilesystemException.
    */
    function set($name, $value) {
        if ($this->isCached($name)) {
            $key = $this->cache[$name]['key'];
        } else {
            $key = uniqid($this->prefix, true);
        }

        if (!is_string($value) || !is_numeric($value)) {
            $value = serialize($value);
            $serailized = true;
        }

        $path = $this->path . $key . '.cache';

        if (!file_get_contents($path, $value)) {
            throw new CribzCacheFilesystemException("Unable to write cache file.", 1);
        }

        $this->cache[$name] = array(
            'key' => $key,
            'path' => $path,
            'serailized' => !empty($serialized) ? true : false,
            'created' => time(),
        );

        $this->clean();

        return true;
    }

    /**
    * Remove
    * Remove an item from the cache.
    *
    * @param string $name   Name of item to remove.
    * @return true on success, false on error or throws CribzCacheFilesystemException.
    */
    function remove($name) {
        if (!$this->isCached($name)) {
            return false;
        }

        $path = $this->cache[$name]['path'];

        if (!unlink($path)) {
            throw new CribzCacheFilesystemException("Unable to remove cache file.", 3);
        }

        unset($this->cache[$name]);
        return true;
    }

    /**
    * Purge
    * Purges the whole cache.
    *
    * @return true.
    */
    function purge() {
        foreach ($this->cache as $item) {
            unlink($item['path']);
        }

        $this->cache = array();
        return true;
    }

    /**
    * Is Cached
    * Check if an item is in the cache.
    *
    * @param string $name   Name of item to check.
    * @return true if item is cached or false.
    */
    function isCached($name) {
        if (isset($this->cache[$name])) {
            return true;
        }

        return false;
    }

    /**
    * Clean
    * Clean up old cache files if the have past the expiry time.
    */
    protected function clean() {
        $time = time();

        foreach ($this->cache as $name => $item) {
            if (($item['created'] + $this->expiry) < $time) {
                unlink($item['path']);
                unset($this->cache[$name]);
            }
        }
    }
}
class CribzCacheFilesystemException extends CribzException {}
?>
