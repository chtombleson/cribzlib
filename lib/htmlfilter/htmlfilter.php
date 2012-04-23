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
* @copyright    Copyright 2012 onwards
*/
class CribzHtmlFilter {
    /**
    * Filter
    * Uses preg_replace() to filter and replace values.
    *
    * @param mixed $regex   Array of regex or one regex string.
    * @param mixed $replace Array of replace strings or one replace string.
    *
    * @return filtered html.
    */
    function filter($html, $regex, $replace) {
        return preg_replace($regex, $replace, $html);
    }

    /**
    * Match
    * Match a regular expression and the get first match.
    *
    * @param string $html       Data to match against.
    * @param string $regex      Regex to match.
    * @param bool   $match      Return the matches array.(Optional)
    *
    * @return match array or true on success or false on failure.
    */
    function match($html, $regex, $match = true) {
        if (!$match) {
            if (preg_match($regex, $html)) {
                return true;
            }
            return false;
        }

        if (preg_match($regex, $html, $matches)) {
            return $matches;
        }
        return false;
    }

    /**
    * Match All
    * Match a regular expression and get all matches.
    *
    * @param string $html       Data to match against.
    * @param string $regex      Regex to match.
    * @param bool   $match      Return the matches array.(Optional)
    *
    * @return match array or true on success or false on failure.
    */
    function match_all($html, $regex, $match = true) {
        if (!$match) {
            if (preg_match_all($regex, $html)) {
                return true;
            }
            return false;
        }

        if (preg_match_all($regex, $html, $matches)) {
            return $matches;
        }
        return false;
    }
}
?>
