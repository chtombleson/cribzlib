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
class CribzCacheInterface {
    /**
    * Get
    * Get an item from the cache.
    *
    * @param string $name   Name of item to get.
    */
    function get($name);

    /**
    * Set
    * Cache an item.
    *
    * @param string $name   Name of item.
    * @param mixed  $value  Value of item.
    */
    function set($name, $value);

    /**
    * Remove
    * Remove an item from cache.
    *
    * @param string $name   Name of item to remove.
    */
    function remove($name);

    /**
    * Purge
    * Purges the entire cache.
    */
    function purge();

    /**
    * Is Cached
    * Check if an item is cached.
    *
    * @param string $name   Name of item to check.
    */
    function isCached($name);
}
?>
