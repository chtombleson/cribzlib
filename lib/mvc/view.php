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
    * Tenplate Dir
    *
    * @var string
    */
    private $templatedir;

    /**
    * Cache Dir
    *
    * @var string
    */
    private $cachedir = '';

    /**
    * View Dev
    *
    * @var boolean
    */
    private $viewdev = false;

    /**
    * Constructor
    * Create an new instance of CribzView.
    *
    * @param string $templatedir    Path to directory with template for your view.
    * @param string $cachedir       Path to cache dir.
    */
    function __construct($templatedir, $cachedir = '') {
        if (file_exists($templatedir) && is_dir($templatedir)) {
            $this->templatedir = rtrim($templatedir, '/').'/';
            $this->cachedir = $cachedir;
        } else {
            throw new CribzViewException("Template Directory: {$templatedir}, does not exist or is not a directory", 1);
        }
    }

    /**
    * Render
    * Render the view.
    *
    * @param string $template    Name of template file to load.
    * @param array  $data        Data to parse to template.
    */
    function render($templatefile, $data = array()) {
        $cribzlib = new CribzLib();
        $cribzlib->loadModule('Template');

        if (file_exists($this->templatedir.$template) && is_file($this->templatedir.$template)) {
            if (!empty($this->cachedir)) {
                $template = new CribzTemplate($this->templatedir.$template, $template, $this->cachedir, $this->viewdev);
            } else {
                $template = new CribzTemplate($this->templatedir.$template, $template, '/tmp/cribzcache/', $this->viewdev);
            }

            $template->output($data);
        } else {
            throw new CribzViewException("Template file does not exist or is not a file", 2);
        }
    }
}
class CribzViewException extends CribzException {}
?>
