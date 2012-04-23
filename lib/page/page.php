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
* @subpackage   Cribz Page
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzPage {
    /**
    * Cribz Lib
    *
    * @var cribzlib
    */
    private $cribzlib;

    /**
    * Cache
    *
    * @var string
    */
    private $cache;

    /**
    * Cahe Path
    *
    * @var string
    */
    private $cachepath;

    /**
    * Templates
    *
    * @var array
    */
    private $templates;

    /**
    * Data
    *
    * @var array
    */
    private $data;

    /**
    * Memcache
    *
    * @var CribzMemcached Object
    */
    private $memcache;

    /**
    * Construct
    * Create new page
    *
    * @param array  $templates       Array of templates, name => path to template file.(Optional)
    * @param array  $data            Array of data for template, name => value.(Optional)
    * @param string $cache           Name to use when stored in cache.
    * @param object $memcache        CribzMemcache Object.(Optional)
    * @param string $cachepath       Path to cache directory.(Optional)
    */
    function __construct($templates = array(), $data = array(), $cache = '', $memcache = null, $cachepath = '/tmp/cribzcache/') {
        $this->cribzlib = new CribzLib();
        $this->memcache = $memcache;
        $this->cache = $cache;
        $this->cachepath = rtrim($cachepath, '/').'/';
        $this->templates = $templates;
        $this->data = $data;
    }

    /**
    * Add Template
    * Add a template to the page.
    *
    * @param string $name       Name for template eg. header, footer.
    * @param string $tempalte   Path to template file.
    *
    * @return true on added, false on error.
    */
    function addTemplate($name, $template) {
        if (file_exists($template) && !isset($this->templates[$name])) {
            $this->templates[$name] = $template;
            return true;
        }
        return false;
    }

    /**
    * Add Data
    * Add Data to be used to replace place holders in the template.
    *
    * @param string $name       Name to the data relates to.
    * @param mixed  $data       Data to add.
    *
    * @return true on added, false on error.
    */
    function addData($name, $data) {
        if (!isset($this->data[$name])) {
            $this->data[$name] = $data;
            return true;
        }
        return false;
    }

    /**
    * Render
    * Render the page.
    *
    * @return false on error.
    */
    function render() {
        $cribz_templates = $this->instTemplates();
        if (!empty($cribz_templates)) {
            foreach ($cribz_templates as $template) {
                $template->output($this->data);
            }
        }
        return false;
    }

    /**
    * Inst Templates
    * Create new instance of Cribz Temlate class foreach template.
    *
    * @return array of Cribz Template classes.
    */
    private function instTemplates() {
        $this->cribzlib->loadModule('Template');
        $templates = array();
        foreach ($this->templates as $name => $tpl) {
            $templates[$name] = new CribzTemplate($tpl, $this->memcache, $this->cache, $this->cachepath);
        }
        return $templates;
    }
}
?>
