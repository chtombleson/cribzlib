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
* @subpackage   Cribz Xss
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzXss {
    /**
    * Allowed Attributes
    *
    * @var array
    */
    private $allowed_attributes = array();

    /**
    * Allowed Tags
    *
    * @var array
    */
    private $allowed_tags = array();

    /**
    * Default Attributes
    *
    * @var array
    */
    private $default_attributes = array('id', 'class', 'href', 'src', 'width', 'height');

    /**
    * Default Tags
    *
    * @var array
    */
    private $default_tags = array('a', 'div', 'span', 'ul', 'li', 'img', 'ol', 'p', 'h1', 'h2',
                                  'h3', 'h4', 'code', 'pre', 'iframe', 'strong', 'em');

    /**
    * Constructor
    * Create a new instance of Cribz Xss.
    *
    * @param array  $tags           Array of allowed tags (optional).
    * @param array  $attributes     Array of allowed attributes (optional).
    */
    function __construct($tags = array(), $attributes = array()) {
        if (!empty($tags)) {
            $this->allowed_tags = array_merge($tags, $this->default_tags);
        } else {
            $this->allowed_tags = $this->default_tags;
        }

        if (!empty($attributes)) {
            $this->allowed_attributes = array_merge($attributes, $this->default_attributes);
        } else {
            $this->allowed_attributes = $this->default_attributes;
        }
    }

    /**
    * Parse
    * Apply allowed tags & attributes to html and returned cleaned output.
    *
    * @param string $html   Html to parse.
    *
    * @return string clean html.
    */
    function parse($html) {
        $html = $this->parse_tags($html);
        return $html;
    }

    /**
    * Parse Tags
    * Checked for tags that aren't allowed and remove them and the contents inside of them.
    *
    * @param string $html   Html to parse.
    *
    * @return cleaned html.
    */
    private function parse_tags($html) {
        $tags = $this->allowed_tags;
        $regex = '#<([^>]+)>#';

        if (preg_match_all($regex, $html, $matches)) {
            $count = count($matches[0]);
            for ($i = 0; $i < $count; $i++) {
                if (preg_match('#^([A-Za-z]+)#', $matches[1][$i], $namematch)) {
                    $tagname = $namematch[1];

                    if (strpos($tagname, '/') !== false) {
                        $tagname = str_replace('/', '', $tagname);
                    }

                    if (!in_array(strtolower($tagname), $tags)) {
                        if (preg_match('#<'.$matches[1][$i].'>([^<]+)</'.$tagname.'>#', $html, $openclosematch)) {
                            $html = str_replace($openclosematch[0], '', $html);
                        } else {
                            $html = str_replace($matches[0][$i], '', $html);
                        }
                    }

                    $clean_attr = $this->parse_attributes($tagname, $matches[1][$i]);
                    if (empty($clean_attr)) {
                        $html = str_replace('<'.$matches[1][$i].'>', '<'.strtolower($tagname).'>', $html);
                    } else {
                        $html = str_replace('<'.$matches[1][$i].'>', '<'.strtolower($tagname).' '.$clean_attr.'>', $html);
                    }
                }
            }
        }
        return trim($html);
    }

    /**
    * Parse Attributes
    * Remove attributes from tags that aren't allowed.
    *
    * @param string $tagname        The tag name.
    * @param string $attributestr   The attribute for the tag.
    *
    * @return cleaned attribute string.
    */
    private function parse_attributes($tagname, $attributestr) {
        $attributes = $this->allowed_attributes;
        $attributestr = preg_replace('#^'.$tagname.'#', '', $attributestr);

        $regex = '#([A-Za-z]+)="([^"]+)"#';
        if (preg_match_all($regex, $attributestr, $matches)) {
            $count = count($matches[0]);
            for ($i = 0; $i < $count; $i++) {
                $attrname = strtolower($matches[1][$i]);

                if (!in_array($attrname, $attributes)) {
                    $attributestr = str_replace($matches[0][$i], '', $attributestr);
                }
            }
        }
        return trim($attributestr);
    }
}
class CribzXssException extends CribzException {}
?>
