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
class CribzFormField {
    private $name;
    private $label;
    private $class;
    private $regex;
    private $options;
    private $required;
    private $maxlength;
    private $minlength;
    private $value = null;

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

    function build() {}
    function validate($data) {}
    function setValue($value) {}
}

class CribzTextField extends CribzFormField {

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

    function setValue($value) {
        $this->value = $value;
    }
}

class CribzEmailField extends CribzTextField {
    private $regex = '/([A-Za-z0-9\S]+@[A-Za-z0-9]+\.[A-Za-z]+(\.[A-Za-z])?/';
}

class CribzPasswordField extends CribzFormField{
    function build() {
        $field = '<input type="password" name="' . $this->name . '" ';

        if (!empty($this->class)) {
            $field .= 'class="' . $this->class .'" ';
        }

        if ($this->required) {
            $field .= 'required ';
        }
        $field = '/>';

        return array('field' => $field, 'label' => $label);
    }

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

    function setValue($value) {}
}

class CribzTextAreaField extends CribzFormField {
    private $rows = 10;
    private $cols = 60;

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

    function setValue($value) {
        $this->value = $value;
    }

    function setSize($rows, $cols) {
        $this->rows = $rows;
        $this->cols = $cols;
    }
}

class CribzSelectField extends CribzFormField {
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

    function setValue($value) {
        $this->value = $value;
    }
}

class CribzCheckBoxField extends CribzFormField {
    function build() {
        $field = '<input type="checkbox" name="' . $this->name . '" ';

        if (isset($this->value)) {
            $field .= 'value="' . $this->value . '" ';
        }

        $field = '/>';

        return array('field' => $field, 'label' => $label);
    }

    function validate($data) {
        return true;
    }

    function setValue($value) {
        $this->value = $value;
    }
}

class CribzHiddenField extends CribzFormField {
    function build() {
        $field = '<input type="hidden" name="' . $this->name . '" ';

        if (isset($this->value)) {
            $field .= 'value="' . $this->value .' " ';
        }
        $field .= '/>';

        return array('field' => $field);
    }

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

    function setValue($value) {
        $this->value = $value;
    }
}
?>
