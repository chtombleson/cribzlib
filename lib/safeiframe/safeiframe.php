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
* @subpackage   Cribz Safe IFrame
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzSafeIFrame {
    /**
    * Allowed Domains
    *
    * @var array
    */
    private $allowed_domains;

    /**
    * Constructor
    * Create a new instance of Cribz Safe IFrame.
    *
    * @param array  $allowed_domians    An array of that contains iframe src base urls.
    */
    function __construct($allowed_domains) {
        if (!is_array($allowed_domains)) {
            throw new CribzSafeIFrameException("Allowed domains must be an array of url's.", 0);
        }

        $this->allowed_domains = $allowed_domains;
    }

    /**
    * Safe
    * Check to see if an iframe is coming from an allowed domain and is safe.
    *
    * @param string $iframe     The html for the iframe.
    *
    * @return true if safe or false if not safe.
    */
    function safe($iframe) {
        $source = $this->get_src($iframe);

        if (empty($source)) {
            throw new CribzSafeIFrameException("Unable to get source from iframe.", 1);
        }

        return $this->validate_src($source);
    }

    /**
    * Get Src
    * Get the src part of the iframe.
    *
    * @param string $iframe     Html for the iframe.
    *
    * @return the src url or an empty string.
    */
    private function get_src($iframe) {
        $regex = '#.*src="([^"]+)".*#';

        if (preg_match($regex, $iframe, $matches)) {
            return $matches[1];
        }
        return '';
    }

    /**
    * Validate Src
    * Check to see if the src is from an allowed domain.
    *
    * @param string $src    The Src url.
    *
    * @return true if valid or false if not valid.
    */
    private function validate_src($src) {
        $valid = false;
        foreach ($this->allowed_domains as $domain) {
            $regex = '#'.$domain.'#';
            if (preg_match($regex, $src)) {
                $valid = true;
            }
        }
        return $valid;
    }
}
class CribzSafeIFrameException extends CribzException {}
?>
