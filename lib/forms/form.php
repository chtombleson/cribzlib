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
* @copyright    Copyright 2012 onwards
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
                $this->addTextBox($name, $required, $label, $maxlength, $minlength, $class, $regex);
                break;
            case 'email' :
                $this->addEmail($name, $required, $label, $maxlength, $minlength, $class);
                break;
            case 'password' :
                $this->addPassword($name, $required, $label, $maxlength, $minlength, $class, $regex);
                break;
            case 'textarea' :
                $this->addTextArea($name, $required, $label, $maxlength, $minlength, $class, $regex);
                break;
            case 'select' :
                $this->addSelect($name, $required, $label, $maxlength, $minlength, $class, $regex, $options);
                break;
            case 'checkbox' :
                $this->addCheckBox($name, $required, $label, $maxlength, $minlength, $class, $regex);
                break;
            case 'hidden' :
                $this->addHidden($name, $required, $label, $maxlength, $minlength, $class, $regex);
                break;
        }
    }

    /**
    * Add Text Box
    * Add text box element to the form.
    *
    * @param string $name       Element name.
    * @param bool   $required   Is element required.
    * @param string $label      Label for element(Optional).
    * @param int    $maxlength  Maxium length of input(Optional).
    * @param int    $minlength  Minium length of input(Optional).
    * @param string $class      Add class to element(Optional).
    * @param string $regex      Regular Expression used to validate input(Optional).
    */
    function addTextBox($name, $required = true, $label = '', $maxlength = null, $minlength = null, $class = '', $regex = '/.*/') {
        if (isset($this->elements[$name])) {
            throw new CribzFormException('Form element with name: '.$name.' already exists.', 0);
        }
        $this->elements[$name] = new CribzTextField($name, $require, $label, $maxlength, $minlength, $class, $regex, null);
    }

    /**
    * Add Email
    * Add email element to the form.
    *
    * @param string $name       Element name.
    * @param bool   $required   Is element required.
    * @param string $label      Label for element(Optional).
    * @param int    $maxlength  Maxium length of input(Optional).
    * @param int    $minlength  Minium length of input(Optional).
    * @param string $class      Add class to element(Optional).
    */
    function addEmail($name, $required = true, $label = '', $maxlength = null, $minlength = null, $class ='') {
        if (isset($this->elements[$name])) {
            throw new CribzFormException('Form element with name: '.$name.' already exists.', 0);
        }
        $this->elements[$name] = new CribzEmailField($name, $required, $label, $maxlength, $minlength, $class, null, null);
    }

    /**
    * Add Password
    * Add password element to the form.
    *
    * @param string $name       Element name.
    * @param bool   $required   Is element required.
    * @param string $label      Label for element(Optional).
    * @param int    $maxlength  Maxium length of input(Optional).
    * @param int    $minlength  Minium length of input(Optional).
    * @param string $class      Add class to element(Optional).
    * @param string $regex      Regular Expression used to validate input(Optional).
    */
    function addPassword($name, $required = true, $label = '', $maxlength = null, $minlength = null, $class = '', $regex = '/.*/') {
        if (isset($this->elements[$name])) {
            throw new CribzFormException('Form element with name: '.$name.' already exists.', 0);
        }
        $this->elements[$name] = new CribzPasswordField($name, $required, $label, $maxlength, $minlength, $class, $regex, null);
    }

    /**
    * Add Text Area
    * Add text area element to the form.
    *
    * @param string $name       Element name.
    * @param bool   $required   Is element required.
    * @param string $label      Label for element(Optional).
    * @param int    $maxlength  Maxium length of input(Optional).
    * @param int    $minlength  Minium length of input(Optional).
    * @param string $class      Add class to element(Optional).
    * @param string $regex      Regular Expression used to validate input(Optional).
    */
    function addTextArea($name, $required = true, $label = '', $maxlength = null, $minlength = null, $class = '', $regex = '/.*/') {
        if (isset($this->elements[$name])) {
            throw new CribzFormException('Form element with name: '.$name.' already exists.', 0);
        }
        $this->elements[$name] = new CribzTextArea($name, $required, $label, $maxlength, $minlength, $class, $regex, null);
    }

    /**
    * Add Select
    * Add select element to the form.
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
    function addSelect($name, $required = true, $label = '', $maxlength = null, $minlength = null, $class = '', $regex = '/.*/', $options=array()) {
        if (isset($this->elements[$name])) {
            throw new CribzFormException('Form element with name: '.$name.' already exists.', 0);
        }
        $this->elements[$name] = new CribzSelectField($name, $required, $label, $maxlength, $minlength, $class, $regex, $options);
    }

    /**
    * Add Check Box
    * Add check box element to the form.
    *
    * @param string $name       Element name.
    * @param bool   $required   Is element required.
    * @param string $label      Label for element(Optional).
    * @param int    $maxlength  Maxium length of input(Optional).
    * @param int    $minlength  Minium length of input(Optional).
    * @param string $class      Add class to element(Optional).
    * @param string $regex      Regular Expression used to validate input(Optional).
    */
    function addCheckBox($name, $required = true, $label = '', $maxlength = null, $minlength = null, $class = '', $regex = '/.*/') {
        if (isset($this->elements[$name])) {
            throw new CribzFormException('Form element with name: '.$name.' already exists.', 0);
        }
        $this->elements[$name] = new CribzCheckBoxField($name, $required, $label, $maxlength, $minlength, $class, $regex, null);
    }

    /**
    * Add Hidden
    * Add hidden element to the form.
    *
    * @param string $name       Element name.
    * @param bool   $required   Is element required.
    * @param string $label      Label for element(Optional).
    * @param int    $maxlength  Maxium length of input(Optional).
    * @param int    $minlength  Minium length of input(Optional).
    * @param string $class      Add class to element(Optional).
    * @param string $regex      Regular Expression used to validate input(Optional).
    */
    function addHidden($name, $required = true, $label = '', $maxlength = null, $minlength = null, $class = '', $regex = '/.*/') {
        if (isset($this->elements[$name])) {
            throw new CribzFormException('Form element with name: '.$name.' already exists.', 0);
        }
        $this->elements[$name] = new CribzHiddenField($name, $required, $label, $maxlength, $minlength, $class, $regex, null);
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
    * Submitted
    * Has to form been submitted.
    *
    * @return true if the form has been submitted or false.
    */
    function submitted() {
        if ($this->post) {
            if (isset($_POST['submit']) && !empty($_POST['submit'])) {
                return true;
            } else {
                return false;
            }
        } else {
            if (isset($_GET['submit']) && !empty($_GET['submit'])) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
    * Validate
    * Validate form.
    *
    * @return true on success, array on error.
    */
    function validate() {
        $cribzlib = new Cribzlib();
        $cribzlib->loadModule('Request');
        $cribzlib->loadModule('Session');
        $request = new CribzRequest();
        $session = new CribzSessions();

        $error = array();
        $token_form = $request->required_param('token', 'string');

        if (!empty($token_form)) {
            $token_sesion = $session->get('form_token');

            if ($token_form != $token_session) {
                return array('Token' => 'Invalid Token Used');
            }

            $data = array();

            foreach (array_keys($this->elements) as $element) {
                $data[$element] = $request->required_param($element, 'string');
            }

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
    * Sanitize
    * Sanitize input to prevent unwanted input.
    *
    * @param string $data   Data to be sanitized
    *
    * @return sanitized data.
    */
    function sanitize($data) {
        $data = htmlentities($data);
        return $data;
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
        $tokz = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
            'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
            '|', '{', '}', '[', ']', '?', '!', '@', '#', '%', '^', '*', '(',
            ')', '~', '<', '>', '+', '=', '&', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9,
        );

        $token = '';

        for ($i=0; $i < 12; $i++) {
            $token .= array_rand($tokz);
        }

        $cribzlib = new CribzLib();
        $cribzlib->loadModule('Session');
        $session = new CribzSessions();
        $session->set('form_token', $token);

        return $token;
    }
}

class CribzFormException extends CribzException {}
?>
