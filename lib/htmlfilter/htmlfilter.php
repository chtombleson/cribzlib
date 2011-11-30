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
* @subpackage   Cribz Html Filter
* @author       Christopher Tombleson
* @copyright    Copyright 2011 onwards
*/
class CribzHtmlFilter {
    /**
    * Html
    *
    * @var string
    */
    private $html;

    /**
    * Construct
    *
    * @param string $html HTML to parse though filter.
    */
    function __construct($html) {
        $this->html = $html;
    }

    /**
    * Filter
    * Uses preg_replace() to filter and replace values.
    *
    * @param mixed $regex   Array of regex or one regex string.
    * @param mixed $replace Array of replace strings or one replace string.
    *
    * @return filtered html.
    */
    function filter($regex, $replace) {
        return preg_replace($regex, $replace, $this->html);
    }
}
?>
