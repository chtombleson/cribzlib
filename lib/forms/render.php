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
* @subpackage   Cribz Form Render
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzFormRender {
    private $elements = array();
    private $action;
    private $method;
    private $token;

    function __construct($elements, $action, $method, $token) {
        $this->elements = $elements;
        $this->action = $action;
        $this->method = $method;
        $this->token = $token;
    }

    function render() {}
}

class CribzFormRender_List extends render {
    function render() {
        $html  = "<form action=\"{$this->action}\"";
        $html .= " method=\"".strtoupper($this->method)."\"";
        $html .= " class=\"form\" />\n";
        $html .= "\t<ul>\n";
        $html .= "\t\t<li><input type=\"hidden\" name=\"token\" value=\"{$this->token}\" /></li>\n";

        foreach($this->elements as $element) {
            $input = $element->build();
            $html .= "\t\t<li>";

            if (!empty($input['label'])) {
                $html .= "<p><label>{$input['label']}</label></p>";
            }

            $html .= $input['field']."</li>\n";
        }

        $html .= "\t\t<li><input type=\"submit\" name=\"submit\" value=\"Submit\" /></li>\n";
        $html .= "\t</ul>\n";
        $html .= "</form>\n";
        return $html;
    }
}

class CribzFormRender_Table extends render {
    function render() {
        $html  = "<form action=\"{$this->action}\"";
        $html .= " method=\"".strtoupper($this->method)."\"";
        $html .= " class=\"form\" />\n";
        $html .= "\t<table>\n";
        $html .= "\t\t<tr><td>&nbsp;</td><td><input type=\"hidden\" name=\"token\" value=\"{$this->token}\" /></td></tr>\n";

        foreach($this->elements as $element) {
            $input = $element->build();
            $html .= "\t\t<tr>";

            if (!empty($input['label'])) {
                $html .= "<td><label>{$input['label']}</label></td>";
            } else {
                $html .= "<td>&nbsp;</td>";
            }

            $html .= "<td>{$input['field']}</td></tr>\n";
        }

        $html .= "\t\t<tr><td>&nbsp;</td><td><input type=\"submit\" name=\"submit\" value=\"Submit\" /></td></tr>\n";
        $html .= "\t</table>\n";
        $html .= "</form>\n";
        return $html;
    }
}
?>
