<?php
class CribzTemplate_Loader_String extends CribzTemplate_Loader {
    private $templates = array();

    function __construct($templates) {
        $this->set_templates($templates);
    }

    public function set_templates($templates) {
        foreach ($templates as $name => $template) {
            $this->set_template($name, $template);
        }
    }

    public function set_template($name, $template) {
        if (!isset($this->templates[$name])) {
            $this->templates[$name] = $template;
        } else {
            throw new CribzTemplate_LoaderException("Template with then name, {$name} already exists.", 3);
        }
    }

    public function get_template($name) {
        return $this->templates[$name];
    }
}
?>
