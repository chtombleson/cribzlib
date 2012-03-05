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
* @subpackage   Cribz Cache
* @author       Christopher Tombleson
* @copyright    Copyright 2011 onwards
*/
class CribzCache {
    /**
    * Cache Dir
    *
    * @var string
    */
    private $cachedir;

    /**
    * Length
    *
    * @var int
    */
    private $length;

    /**
    * Max Size
    *
    * @var int
    */
    private $maxsize;

    /**
    * Construct
    * Create a new instance of cribz cache.
    *
    * @param string $cachedir   Path to cache directory.
    * @param int    $length     How long to cache for in seconds.
    * @param int    $maxsize    Max size of cache in kilobytes.
    */
    function __construct($cachedir, $length=3600, $maxsize=5120) {
        $this->cachedir = rtrim($cachedir, "/").'/';
        $this->length = $length;
        $this->maxsize = $maxsize;
    }

    /**
    * Init
    * Intailize the cache.
    *
    * @return true
    */
    function init() {
        if (!file_exists($this->cachedir)) {
            @mkdir($this->cachedir);
        }

        $size = disk_total_space($this->cachedir);
        $size = $size / 1024;

        if ($size > $this->maxsize) {
            $this->purge(true);
        }

        return true;
    }

    /**
    * Add
    * Add item to cache
    *
    * @param string $name       Name to give cache file.
    * @param string $content    Content to cache.
    *
    * @return true on success or false on failure
    */
    function add($name, $content) {
        if (file_put_contents($this->cachedir.md5($name).'.cache', $content)) {
            return $this->cachedir.md5($name).'.cache';
        }
        return false;
    }

    /**
    * Remove
    * Remove an item from the cache.
    *
    * @param string $name   Name of item to remove.
    *
    * @return true on success or false on failure
    */
    function remove($name) {
        if (file_exists($this->cachedir.md5($name).'.cache')) {
            unlink($this->cachedir.md5($name).'.cache');
            return true;
        }
        return false;
    }

    /**
    * Purge
    * Clear the cache.
    *
    * @param bool $all  Delete all cache items default is false (Optional).
    *
    * @return true
    */
    function purge($all = false) {
        $files = glob($this->cachedir.'.*\.cache');

        if ($all) {
            foreach ($files as $file) {
                unlink($file);
            }
            return true;
        }

        $time = time() - $this->length;

        foreach ($files as $file) {
            if (fileatime($file) < $time) {
                unlink($file);
            }
        }
        return true;
    }

    /**
    * Is Cached
    * Check to see if something is cached.
    *
    * @param string $name   Name of item to check.
    *
    * @return path to cached file on success or false on failure
    */
    function is_cached($name) {
        if (file_exists($this->cachedir.md5($name).'.cache')) {
            return $this->cachedir.md5($name).'.cache';
        }
        return false;
    }
}
?>
