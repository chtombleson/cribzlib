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
* @subpackage   Cribz Form
* @author       Christopher Tombleson
* @copyright    Copyright 2011 onwards
*/
class CribzForm {
    /**
    * Action
    *
    * @var string
    */
    private $action;

    /**
    * Post
    *
    * @var bool
    */
    private $post;

    /**
    * Elements
    *
    * @var array
    */
    private $elements = array();

    /**
    * Construct
    * Create new form.
    *
    * @param string $action     Url for form action.
    * @param bool   $post       Use post request method(Optional).
    */
    function __construct($action, $post = true) {
        $this->action = $action;
        $this->post = $post;
    }

    /**
    * Add Element
    * Add element to the form.
    *
    * @param string $type       Input type (text, email, password, textarea, select, checkbox, hidden).
    * @param string $name       Element name.
    * @param bool   $required   Is element required.
    * @param string $label      Label for element(Optional).
    * @param int    $maxlength  Maxium length of input(Optional).
    * @param int    $minlength  Minium length of input(Optional).
    * @param string $class      Add class to element(Optional).
    * @param string $regex      Regular Expression used to validate input(Optional).
    * @param array  $options    Array of options for select inputs(Optional).
    */
    function addElement($type, $name, $required = true, $label = '', $maxlength = null, $minlength = null, $class = '', $regex = '/.*/', $options = array()) {
        switch ($type) {
            case 'text' :
                $this->elements[$name] = new CribzTextField($name, $required, $label, $maxlength, $minlength, $class, $regex, $options);
                break;
            case 'email' :
                $this->elements[$name] = new CribzEmailField($name, $required, $label, $maxlength, $minlength, $class, $regex, $options);
                break;
            case 'password' :
                $this->elements[$name] = new CribzPasswordField($name, $required, $label, $maxlength, $minlength, $class, $regex, $options);
                break;
            case 'textarea' :
                $this->elements[$name] = new CribzTextAreaField($name, $required, $label, $maxlength, $minlength, $class, $regex, $options);
                break;
            case 'select' :
                $this->elements[$name] = new CribzSelectField($name, $required, $label, $maxlength, $minlength, $class, $regex, $options);
                break;
            case 'checkbox' :
                $this->elements[$name] = new CribzCheckBoxField($name, $required, $label, $maxlength, $minlength, $class, $regex, $options);
                break;
            case 'hidden' :
                $this->elements[$name] = new CribzHiddenField($name, $required, $label, $maxlength, $minlength, $class, $regex, $options);
                break;
        }
    }

    /**
    * Set Default
    * Set Default value of input.
    *
    * @param string $name       Name of input to add default to.
    * @param mixed  $value      Value to set input to.
    */
    function setDefault($name, $value) {
        $this->elements[$name]->setValue($value);
    }

    /**
    * Add Data
    * Add Data to form.
    *
    * @param mixed $data    Array or Object of data to be added to form, format name => value.
    * @see setDefault()
    */
    function addData($data) {
        foreach ($data as $name => $value) {
            $this->setDefault($name, $value);
        }
    }

    /**
    * Validate
    * Validate form.
    *
    * @param array $data        Data from  submitted form.
    *
    * @return true on success, array on error.
    */
    function validate($data) {
        $error = array();
        if (isset($data['token'])) {
            session_start();
            $token = $_SESSION['form_token'];
            session_write_close();

            if ($data['token'] != $token) {
                return array('Token' => 'Invalid Token Used');
            }
            unset($data['token']);

            foreach ($data as $name => $value) {
                if (!$this->elements[$name]->validate($value)) {
                    $error[$name] = 'Invalid Input';
                }
            }

            if (empty($error)) {
                return true;
            }
            return $error;
        } else {
            return array('Token' => 'Invalid Token Used');
        }
    }

    /**
    * Render
    * Render form
    *
    * @return string html for the form.
    */
    function render() {
        $formtop  = "<form action=\"" . $this->action . "\"";

        if ($this->post) {
            $formtop .= " method=\"post\"";
        } else {
            $formtop .= " method=\"get\"";
        }

        $formtop .= " class=\"form\">\n";

        $formbody = "<ul>\n";
        $formbody .= "\t<li>\n";
        $formbody .= "\t\t<input type=\"hidden\" name=\"token\" value=\"" . $this->gen_token() . "\" />\n";
        $formbody .= "\t</li>\n";

        foreach ($this->elements as $element) {
            $html = $element->build();
            $formbody .= "\t<li>\n";
            
            if (isset($html['label'])) {
                $formbody .= "\t\t<p><label>".$html['label']."</label></p>\n";
            }

            $formbody .= "\t\t".$html['field']."\n";
            $formbody .= "\t</li>";
        }

        $formbody .= "\t<li>\n";
        $formbody .= "\t\t<input type=\"submit\" name=\"submit\" value=\"Submit\" />\n";
        $formbody .= "\t</li>\n";
        $formbody .= "</ul>\n";
        $formbtm = "</form>\n";

        return $formtop.$formbody.$formbtm;
    }

    /**
    * Gen Token
    * Generate a token to be used to validate form.
    *
    * @return string token.
    */
    function gen_token() {
        $tokz = array('A', 'B', 'C', 'D', 'E', 'F', 'a', 'b', 'c', 'd', 'e', 'f', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
        $token = '';

        for ($i=0; $i < 12; $i++) {
            $token .= array_rand($tokz);
        }

        session_start();
        $_SESSION['form_token'] = $token;
        session_write_close();

        return $token;
    }
}
?>
