<?php
class CribzForm {
    private $action;
    private $post;
    private $elements = array();

    function __construct($action, $post = true) {
        $this->action = $action;
        $this->post = $post;
    }

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

    function setDefault($name, $value) {
        $this->elements[$name]->setValue($value);
    }

    function addData($data) {
        foreach ($data as $name => $value) {
            $this->setDefault($name, $value);
        }
    }

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
            $html = $element->render();
            $formbody .= "\t<li>\n";
            
            if (isset($html['label'])) {
                $formbody .= "\t\t<p><label>".$html['label']."</label></p>\n";
            }

            $formbody .= "\t\t".$html['field']."\n";
            $formbody .= "\t</li>";
        }

        $formbody .= "\t<li>\n";
        $formbody .= "\t\t<input type=\"submit\" name=\"submit\" value=\"Submit\" />\n";
        $fomrbody .= "\t</li>\n";
        $formbody .= "</ul>\n";
        $formbtm = "</form>\n";

        return $formtop.$formbody.$formbtm;
    }

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
