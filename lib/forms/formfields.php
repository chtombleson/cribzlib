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
* @subpackage   Cribz Form Fields
* @author       Christopher Tombleson
* @copyright    Copyright 2011 onwards
*/
class CribzFormField {

    /**
    * Name
    *
    * @var string
    */
    private $name;

    /**
    * Label
    *
    * @var string
    */
    private $label;

    /**
    * Class
    *
    * @var string
    */
    private $class;

    /**
    * Regex
    *
    * @var string
    */
    private $regex;

    /**
    * Options
    *
    * @var array
    */
    private $options;

    /**
    * Required
    *
    * @var bool
    */
    private $required;

    /**
    * Max Length
    *
    * @var int
    */
    private $maxlength;

    /**
    * Min Length
    *
    * @var int
    */
    private $minlength;

    /**
    * Value
    *
    * @var mixed
    */
    private $value = null;

    /**
    * Construct
    * Create new form field.
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
    function __construct($name, $required = true, $label = '', $maxlength = null, $minlength = null, $class = '', $regex = '/.*/', $options = array()) {
        $this->name = $name;
        $this->label = empty($label) ? $name : $label;
        $this->class = $class;
        $this->regex = $regex;
        $this->required = $required;
        $this->maxlength = empty($maxlength) ? null : (int) $maxlength;
        $this->minlength = empty($minlength) ? null : (int) $minlength;
        $this->options = $options;
    }

    /**
    * Build
    * Build the form field
    */
    function build() {}

    /**
    * Validate
    *
    * @param array $data    Data from submitted form.
    */
    function validate($data) {}

    /**
    * Set Value
    *
    * @param mixed $value   Value to set.
    */
    function setValue($value) {}

    /**¶
    * Get
    * Get a class variable by name.¶
    *
    * @param string $name   Name of variable to get.¶
    *
    * @return value of class variable or null if not found.¶
    */
    function get($name) {
        if (isset($this->$name)) {
            return $this->name;
        }
        return null;
    }
}

class CribzTextField extends CribzFormField {

    /**
    * Build
    * Build Text input field.
    */
    function build() {
        $name = $this->get('name');
        $class = $this->get('class');
        $value = $this->get('value');
        $required = $this->get('required');
        $label = $this->get('label');

        $field = '<input type="text" name="' . $name . '" ';

        if (!empty($class)) {
            $field .= 'class="' . $class .'" ';
        }

        if (!empty($value)) {
            $field .= 'value="' . $value .' " ';
        }

        if ($required) {
            $field .= 'required ';
        }

        $field .= '/>';
        return array('field' => $field, 'label' => $label);
    }

    /**
    * Validate
    * Validate input from text field.
    *
    * @param array $data    Data from submitted form.
    *
    * @return false on invalid, true on valid.
    */
    function vaildate($data) {
        $required = $this->get('required');
        $minlength = $this->get('minlength');
        $maxlength = $this->get('maxlength');
        $regex = $this->get('regex');

        if ($required) {
            if (!empty($data)) {
                if (!empty($minlength)) {
                    if (strlen($data) < $minlength) {
                        return false;
                    }
                }

                if (!empty($maxlength)) {
                    if (strlen($data) > $maxlength) {
                        return false;
                    }
                }

                if (preg_match($regex, $data)) {
                    return true;
                }
            }
            return false;
        } else {
            if (!empty($minlength)) {
                if (strlen($data) < $minlength) {
                    return false;
                }
            }

            if (!empty($maxlength)) {
                if (strlen($data) > $maxlength) {
                    return false;
                }
            }

            if (preg_match($regex, $data)) {
                return true;
            }
            return false;
        }
    }

    /**
    * Set Value
    * Set Value of text field
    *
    * @param mixed $value   Value to set.
    */
    function setValue($value) {
        $this->value = $value;
    }
}

class CribzEmailField extends CribzTextField {
    /**
    * Regex
    *
    * @var string
    */
    private $regex = '/([A-Za-z0-9\S]+@[A-Za-z0-9]+\.[A-Za-z]+(\.[A-Za-z])?/';
}

class CribzPasswordField extends CribzFormField{
    /**
    * Build
    * Build password input field.
    */
    function build() {
        $name = $this->get('name');
        $class = $this->get('class');
        $value = $this->get('value');
        $required = $this->get('required');
        $label = $this->get('label');

        $field = '<input type="password" name="' . $name . '" ';

        if (!empty($class)) {
            $field .= 'class="' . $class .'" ';
        }

        if ($required) {
            $field .= 'required ';
        }
        $field .= '/>';

        return array('field' => $field, 'label' => $label);
    }

    /**
    * Validate
    * Validate input from password field.
    *
    * @param array $data    Data from submitted form.
    *
    * @return false on invalid, true on valid.
    */
    function vaildate($data) {
        $required = $this->get('required');
        $minlength = $this->get('minlength');
        $maxlength = $this->get('maxlength');
        $regex = $this->get('regex');

        if ($required) {
            if (!empty($data)) {
                if (!empty($minlength)) {
                    if (strlen($data) < $minlength) {
                        return false;
                    }
                }

                if (!empty($maxlength)) {
                    if (strlen($data) > $maxlength) {
                        return false;
                    }
                }

                if (preg_match($regex, $data)) {
                    return true;
                }
            }
            return false;
        } else {
            if (!empty($minlength)) {
                if (strlen($data) < $minlength) {
                    return false;
                }
            }

            if (!empty($maxlength)) {
                if (strlen($data) > $maxlength) {
                    return false;
                }
            }

            if (preg_match($regex, $data)) {
                return true;
            }
            return false;
        }
    }

    /**
    * Set Value
    * Set Value of input field
    *
    * @param mixed $value   Value to set.
    */
    function setValue($value) {}
}

class CribzTextAreaField extends CribzFormField {
    /**
    * Rows
    *
    * @var int
    */
    private $rows = 10;

    /**
    * Cols
    *
    * @var int
    */
    private $cols = 60;

    /**
    * Build
    * Build textarea input field.
    */
    function build() {
        $name = $this->get('name');
        $class = $this->get('class');
        $value = $this->get('value');
        $required = $this->get('required');
        $label = $this->get('label');

        $field = '<textarea name="' . $name . '"';

        if (!empty($class)) {
            $field .= ' class="' . $class . '"';
        }

        $field .= ' rows="' . $this->rows .'" cols="' . $this->cols . '"';

        if ($required) {
            $field .= ' required';
        }

        $field .= '>';

        if (!empty($value)) {
            $field .= $value;
        }

        $field .= '</textarea>';
        return array('field' => $field, 'label' => $label);
    }

    /**
    * Validate
    * Validate input from textarea field.
    *
    * @param array $data    Data from submitted form.
    *
    * @return false on invalid, true on valid.
    */
    function vaildate($data) {
        $required = $this->get('required');
        $minlength = $this->get('minlength');
        $maxlength = $this->get('maxlength');
        $regex = $this->get('regex');

        if ($required) {
            if (!empty($data)) {
                if (!empty($minlength)) {
                    if (strlen($data) < $minlength) {
                        return false;
                    }
                }

                if (!empty($maxlength)) {
                    if (strlen($data) > $maxlength) {
                        return false;
                    }
                }

                if (preg_match($regex, $data)) {
                    return true;
                }
            }
            return false;
        } else {
            if (!empty($minlength)) {
                if (strlen($data) < $minlength) {
                    return false;
                }
            }

            if (!empty($maxlength)) {
                if (strlen($data) > $maxlength) {
                    return false;
                }
            }

            if (preg_match($regex, $data)) {
                return true;
            }
            return false;
        }
    }

    /**
    * Set Value
    * Set Value of textarea.
    *
    * @param mixed $value   Value to set.
    */
    function setValue($value) {
        $this->value = $value;
    }

    /**
    * Set Size
    * Set Size of textarea
    *
    * @param int $rows      Number of rows.
    * @param int $cols      Number of columns.
    */
    function setSize($rows, $cols) {
        $this->rows = $rows;
        $this->cols = $cols;
    }
}

class CribzSelectField extends CribzFormField {
    /**
    * Build
    * Build Select input field.
    */
    function build() {
        $name = $this->get('name');
        $class = $this->get('class');
        $value = $this->get('value');
        $required = $this->get('required');
        $label = $this->get('label');
        $options = $this->get('options');

        $field = '<select name="' . $name . '"';

        if (!empty($class)) {
            $field .= ' class="' . $class . '"';
        }

        if ($required) {
            $field .= ' required';
        }

        $field .= '>';

        if (!empty($options)) {
            foreach ($options as $key => $val) {
                $field .= '<option value="' . $val . '"';

                if (!empty($value) && ($value == $val)) {
                    $field .= ' selected';
                }
                $field .= '>';
                $field .= $key;
                $field .= '</option>';
            }
        }
        $field .= '</select>';

        return array('field' => $field, 'label' => $label);
    }

    /**
    * Validate
    * Validate input from select field.
    *
    * @param array $data    Data from submitted form.
    *
    * @return false on invalid, true on valid.
    */
    function validate($data) {
        $required = $this->get('required');
        $options = $this->get('options');

        if ($required) {
            if (!empty($data)) {
                if (in_array($data, $options)) {
                    return true;
                }
            }
            return false;
        } else {
            if (in_array($data, $options)) {
                return true;
            }
            return false;
        }
    }

    /**
    * Set Value
    * Set value of select field.
    *
    * @param mixed $value   Value to set.
    */
    function setValue($value) {
        $this->value = $value;
    }
}

class CribzCheckBoxField extends CribzFormField {
    /**
    * Build
    * Build checkbox input field.
    */
    function build() {
        $name = $this->get('name');
        $class = $this->get('class');
        $value = $this->get('value');
        $required = $this->get('required');
        $label = $this->get('label');

        $field = '<input type="checkbox" name="' . $name . '" ';

        if (isset($value)) {
            $field .= 'value="' . $value . '" ';
        }

        $field .= '/>';

        return array('field' => $field, 'label' => $label);
    }

    /**
    * Validate
    * Validate input from checkbox field.
    *
    * @param array $data    Data from submitted form.
    *
    * @return false on invalid, true on valid.
    */
    function validate($data) {
        return true;
    }

    /**
    * Set Value
    * Set value of checkbox input.
    *
    * @param mixed $value   Value to set.
    */
    function setValue($value) {
        $this->value = $value;
    }
}

class CribzHiddenField extends CribzFormField {
    /**
    * Build
    * Build hidden input field.
    */
    function build() {
        $name = $this->get('name');
        $value = $this->get('value');

        $field = '<input type="hidden" name="' . $name . '" ';

        if (isset($value)) {
            $field .= 'value="' . $value .' " ';
        }
        $field .= '/>';

        return array('field' => $field);
    }

    /**
    * Validate
    * Validate input from hidden field.
    *
    * @param array $data    Data from submitted form.
    *
    * @return false on invalid, true on valid.
    */
    function validate($data) {
        $required = $this->get('required');
        $minlength = $this->get('minlength');
        $maxlength = $this->get('maxlength');
        $regex = $this->get('regex');

        if ($required) {
            if (!empty($data)) {
                if (!empty($minlength)) {
                    if (strlen($data) < $minlength) {
                        return false;
                    }
                }

                if (!empty($maxlength)) {
                    if (strlen($data) > $maxlength) {
                        return false;
                    }
                }

                if (preg_match($regex, $data)) {
                    return true;
                }
            }
            return false;
        } else {
            if (!empty($minlength)) {
                if (strlen($data) < $minlength) {
                    return false;
                }
            }

            if (!empty($maxlength)) {
                if (strlen($data) > $maxlength) {
                    return false;
                }
            }

            if (preg_match($regex, $data)) {
                return true;
            }
            return false;
        }
    }

    /**
    * Set Value
    * Set Value of hidden input field.
    *
    * @param mixed $value   Value to set.
    */
    function setValue($value) {
        $this->value = $value;
    }
}
?>
