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
    * Cache Path
    *
    * @var string
    */
    private $cachepath;

    /**
    * Memcache
    *
    * @var CribzMemcache Object
    */
    private $memcache;

    /**
    * Construct
    *
    * @param string $template   Path to template file to compile.
    * @param object $memcache   CribzMemcache object.
    * @param string $cache      Name for cache item.
    * @param string $cachepath  Path to cache directory.
    */
    function __construct($template, $memcache = null, $cache = '', $cachepath = '/tmp/cribzcache/') {
        $this->template = $template;
        $this->cache = $cache;
        $this->cachepath = $cachepath;
        $this->memcache = $memcache;
    }

    /**
    * Output
    * Display compiled template.
    *
    * @param array $data    Array of data to be passed to the compiler.
    */
    function output($data = array()) {
        $compiler = new CribzTemplateCompiler($this->template, $this->memcache, $this->cache, $this->cachepath);
        $template_path = $compiler->parse($data);

        if (!empty($this->memcache)) {
            $tpl = $this->memcache->get($template_path);
            @eval(' ?>'.$tpl.'<?php ');
        } else {
            include($template_path);
        }
    }
}
?>
