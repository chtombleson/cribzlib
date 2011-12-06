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
    public $name;

    /**
    * Label
    *
    * @var string
    */
    public $label;

    /**
    * Class
    *
    * @var string
    */
    public $class;

    /**
    * Regex
    *
    * @var string
    */
    public $regex;

    /**
    * Options
    *
    * @var array
    */
    public $options;

    /**
    * Required
    *
    * @var bool
    */
    public $required;

    /**
    * Max Length
    *
    * @var int
    */
    public $maxlength;

    /**
    * Min Length
    *
    * @var int
    */
    public $minlength;

    /**
    * Value
    *
    * @var mixed
    */
    public $value = null;

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
}

class CribzTextField extends CribzFormField {

    /**
    * Build
    * Build Text input field.
    */
    function build() {
        $field = '<input type="text" name="' . $this->name . '" ';

        if (!empty($this->class)) {
            $field .= 'class="' . $this->class .'" ';
        }

        if (!empty($this->value)) {
            $field .= 'value="' . $this->value .' " ';
        }

        if ($this->required) {
            $field .= 'required ';
        }

        $field .= '/>';
        return array('field' => $field, 'label' => $this->label);
    }

    /**
    * Validate
    * Validate input from text field.
    *
    * @param array $data    Data from submitted form.
    *
    * @return false on invalid, true on valid.
    */
    function validate($data) {
        if ($this->required) {
            if (!empty($data)) {
                if (!empty($this->minlength)) {
                    if (strlen($data) < $this->minlength) {
                        return false;
                    }
                }

                if (!empty($this->maxlength)) {
                    if (strlen($data) > $this->maxlength) {
                        return false;
                    }
                }

                if (preg_match($this->regex, $data)) {
                    return true;
                }
            }
            return false;
        } else {
            if (!empty($this->minlength)) {
                if (strlen($data) < $this->minlength) {
                    return false;
                }
            }

            if (!empty($this->maxlength)) {
                if (strlen($data) > $this->maxlength) {
                    return false;
                }
            }

            if (preg_match($this->regex, $data)) {
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
    public $regex = '/([A-Za-z0-9\S]+@[A-Za-z0-9]+\.[A-Za-z]+(\.[A-Za-z])?/';
}

class CribzPasswordField extends CribzFormField{
    /**
    * Build
    * Build password input field.
    */
    function build() {
        $field = '<input type="password" name="' . $this->name . '" ';

        if (!empty($this->class)) {
            $field .= 'class="' . $this->class .'" ';
        }

        if ($this->required) {
            $field .= 'required ';
        }
        $field .= '/>';

        return array('field' => $field, 'label' => $this->label);
    }

    /**
    * Validate
    * Validate input from password field.
    *
    * @param array $data    Data from submitted form.
    *
    * @return false on invalid, true on valid.
    */
    function validate($data) {
        if ($this->required) {
            if (!empty($data)) {
                if (!empty($this->minlength)) {
                    if (strlen($data) < $this->minlength) {
                        return false;
                    }
                }

                if (!empty($this->maxlength)) {
                    if (strlen($data) > $this->maxlength) {
                        return false;
                    }
                }

                if (preg_match($this->regex, $data)) {
                    return true;
                }
            }
            return false;
        } else {
            if (!empty($this->minlength)) {
                if (strlen($data) < $this->minlength) {
                    return false;
                }
            }

            if (!empty($this->maxlength)) {
                if (strlen($data) > $this->maxlength) {
                    return false;
                }
            }

            if (preg_match($this->regex, $data)) {
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
        $field = '<textarea name="' . $this->name . '"';

        if (!empty($this->class)) {
            $field .= ' class="' . $this->class . '"';
        }

        $field .= ' rows="' . $this->rows .'" cols="' . $this->cols . '"';

        if ($this->required) {
            $field .= ' required';
        }

        $field .= '>';

        if (!empty($this->value)) {
            $field .= $this->value;
        }

        $field .= '</textarea>';
        return array('field' => $field, 'label' => $this->label);
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
        if ($this->required) {
            if (!empty($data)) {
                if (!empty($this->minlength)) {
                    if (strlen($data) < $this->minlength) {
                        return false;
                    }
                }

                if (!empty($this->maxlength)) {
                    if (strlen($data) > $this->maxlength) {
                        return false;
                    }
                }

                if (preg_match($this->regex, $data)) {
                    return true;
                }
            }
            return false;
        } else {
            if (!empty($this->minlength)) {
                if (strlen($data) < $this->minlength) {
                    return false;
                }
            }

            if (!empty($this->maxlength)) {
                if (strlen($data) > $this->maxlength) {
                    return false;
                }
            }

            if (preg_match($this->regex, $data)) {
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
        $field = '<select name="' . $this->name . '"';

        if (!empty($this->class)) {
            $field .= ' class="' . $this->class . '"';
        }

        if ($this->required) {
            $field .= ' required';
        }

        $field .= '>';

        if (!empty($this->options)) {
            foreach ($this->options as $name => $value) {
                $field .= '<option value="' . $value . '"';

                if (!empty($this->value) && ($this->value == $value)) {
                    $field .= ' selected';
                }
                $field .= '>';
                $field .= $name;
                $field .= '</option>';
            }
        }
        $field .= '</select>';

        return array('field' => $field, 'label' => $this->label);
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
        if ($this->required) {
            if (!empty($data)) {
                if (in_array($data, $this->options)) {
                    return true;
                }
            }
            return false;
        } else {
            if (in_array($data, $this->options)) {
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
        $field = '<input type="checkbox" name="' . $this->name . '" ';

        if (isset($this->value)) {
            $field .= 'value="' . $this->value . '" ';
        }

        $field .= '/>';

        return array('field' => $field, 'label' => $this->label);
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
        $field = '<input type="hidden" name="' . $this->name . '" ';

        if (isset($this->value)) {
            $field .= 'value="' . $this->value .' " ';
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
        if ($this->required) {
            if (!empty($data)) {
                if (!empty($this->minlength)) {
                    if (strlen($data) < $this->minlength) {
                        return false;
                    }
                }

                if (!empty($this->maxlength)) {
                    if (strlen($data) > $this->maxlength) {
                        return false;
                    }
                }

                if (preg_match($this->regex, $data)) {
                    return true;
                }
            }
            return false;
        } else {
            if (!empty($this->minlength)) {
                if (strlen($data) < $this->minlength) {
                    return false;
                }
            }

            if (!empty($this->maxlength)) {
                if (strlen($data) > $this->maxlength) {
                    return false;
                }
            }

            if (preg_match($this->regex, $data)) {
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
