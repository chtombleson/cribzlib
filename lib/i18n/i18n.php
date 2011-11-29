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
* @subpackage   Cribz I18n
* @author       Christopher Tombleson
* @copyright    Copyright 2011 onwards
*/
class CribzI18n {
    /**
    * Lang
    *
    * @var string
    */
    private $lang;

    /**
    * Lang Dir
    *
    * @var string
    */
    private $langdir;

    /**
    * Lang Def
    *
    * @var array
    */
    private $langdef = array();

    /**
    * Construct
    *
    * @param string $lang       Language to use.
    * @param string $langdir    Directory language file are in.
    */
    function __construct($lang = 'en_nz', $langdir = 'lang/') {
        $this->lang = $lang;
        $this->langdir = $langdir;
        $this->load_lang();
    }

    /**
    * Get String
    * Retrive a string from the current language file.
    *
    * @param string $name   Name of string you want to load.
    *
    * @return the language string you wanted or the name you gave as a parameter when not found.
    */
    function get_string($name) {
        if (isset($this->langdef[$name])) {
            return utf8_decode($this->langdef[$name]);
        } else {
            return $name;
        }
    }

    /**
    * Load Lang
    * Loads a language file and store in Lang Def class variable.
    */
    private function load_lang() {
        if (file_exists($this->langdir.$this->lang.'.php')) {
            require_once($this->langdir.$this->lang.'.php');
            global $i18n;
            $this->langdef = $i18n;
        }
    }

}
?>
