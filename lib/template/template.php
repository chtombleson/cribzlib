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
* @subpackage   Cribz Template
* @author       Christopher Tombleson
* @copyright    Copyright 2011 onwards
*/
require_once(dirname(__FILE__).'/../../cribzlib.php');
require_once(dirname(__FILE__).'/template_compiler.php');
class CribzTemplate {
    /**
    * Template
    *
    * @var string
    */
    private $template;

    /**
    * Cache
    *
    * @var string
    */
    private $cache;

    /**
    * Memcache
    *
    * @var bool
    */
    private $memcache;

    /**
    * Construct
    *
    * @param string $template   Path to template file to compile.
    * @param array  $memcache   Memcache server details for storing compiled templates.
    * @param string $cache      Path to cache directory.
    */
    function __construct($template, $memcache = array(), $cache = '/tmp/cribzcache/') {
        $this->template = $template;
        $this->cache = $cache;
        $this->memcache = $memcache;
    }

    /**
    * Output
    * Display compiled template.
    *
    * @param array $data    Array of data to be passed to the compiler.
    */
    function output($data = array()) {
        $compiler = new CribzTemplateCompiler($this->template, $this->memcache, $this->cache);
        $template_path = $compiler->parse($data);

        if (!empty($this->memcache)) {
            $cribzlib = new CribzLib();
            $cribzlib->loadModule('Memcached');

            $memcache = new CribzMemcached();
            foreach ($this->memcache as $memcache_server) {
                if (is_array($memcache_server)) {
                    $memcache->addServer($memcache_server['host'], $memcache_server['port'], $memcache_server['weight']);
                } else {
                    $memcache->addServer($this->memcache['host'], $this->memcache['port'], $this->memcache['weight']);
                }
            }

            echo $memcache->get($template_path);
        } else {
            echo file_get_contents($template_path);
        }
    }
}
?>
