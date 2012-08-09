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
* @subpackage   Cribz View
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzVeiw {

    /**
    * Twig
    *
    * @var CribzTwig
    */
    private $twig;

    /**
    * Constructor
    * Create an new instance of CribzView.
    *
    * @param string $templatedir    Path to directory with template for your view.
    * @param string $cachedir       Path to cache dir.
    */
    function __construct($templatedir, $cachedir = '', $debug=false) {
        $cribzlib = new CribzLib();
        $cribzlib->loadModule('Twig');
        $this->twig = new CribzTwig($templatedir, $cachedir, $debug);
    }

    /**
    * Render
    * Render the view.
    *
    * @param string $template    Name of template to load.
    * @param array  $data        Data to parse to template.(Optional)
    * 
    * @return string on success or throws CribzTwig Exception on error.
    */
    function render($template, $data = array()) {
        return $this->twig->render($template, $data);
    }

    /**
    * Display
    * Display the view to the browser.
    *
    * @param string $template    Name of template to load.
    * @param array  $data        Data to parse to template.(Optional)
    * 
    * @return throws CribzTwig Exception on error.
    */
    function display($template, $data = array()) {
        $this->twig->display($template, $data);
    }
}
class CribzViewException extends CribzException {}
?>
