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
    * Twig
    *
    * @var CribzTwig
    */
    private $twig;

    /**
    * Data
    *
    * @var array
    */
    private $data = array();

    /**
    * Construct
    * Create new page
    *
    * @param string $templatedir     Path to directory where templates are stored.
    * @param string $cachepath       Path to cache directory.(Optional)
    * @param bool   $debug           Turn on Twig debugging mode.(Optional)
    */
    function __construct($templatedir, $cachepath = '', $debug = false) {
        $cribzlib = new CribzLib();
        $cribzlib->loadModule('Twig');
        $this->twig = new CribzTwig($templatedir, $cachepath, $debug);
    }

    /**
    * Add Data
    * Add Data to be used to replace place holders in the template.
    *
    * @param string $name       Name to the data relates to.
    * @param mixed  $data       Data to add.
    */
    function addData($name, $data) {
        $this->data[$name] = $data;
    }

    /**
    * Render
    * Render the page.
    *
    * @param  string $template      Template to render.
    * @param  array  $data          Additional data to be parsed to the template.(Optional)
    *
    * @return string of template on success or throws CribzTwig Exception on error.
    */
    function render($template, $data=array()) {
        return $this->twig->render($template, array_merge($this->data, $data));
    }

    /**
    * Display
    * Display a page to the broswer.
    *
    * @param  string $template      Template to render.
    * @param  array  $data          Additional data to be parsed to the template.(Optional)
    *
    * @return throws CribzTwig Exception on error.
    */
    function display($template, $data=array()) {
        $this->twig->display($template, array_merge($this->data, $data));
    }
}
class CribzPageException extends CribzException {}
?>
