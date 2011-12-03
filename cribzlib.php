<?php
class CribzLib {
    private $modules = array(
        'Ajax' => 'ajax/ajax.php',
        'Cookie' => 'cookie/cookies.php',
        'Database' => 'database/database.php',
        'Email' => 'email/email.php',
        'Form' => array('forms/formfields.php','forms/form.php'),
        'HtmlFilter' => 'htmlfilter/htmlfilter.php',
        'I18N' => 'i18n/i18n.php',
        'Session' => 'session/sessions.php';
        'Template' => array('template/template_compiler.php','template/template.php')
    );

    function loadModule($name) {
        $modules = array_keys($this->modules);
        if (in_array($name, $modules)) {
            foreach ($this->modules[$name] as $file) {
                require_once(dirname(__FILE__).'/lib/'.$file);
            }
            return true;
        } else {
            return false;
        }
    }
}
?>
