<?php
class CribzLib {
    private $modules = array(
        'Ajax' => 'ajax/ajax.php'),
        'Cookie' => 'cookie/cookies.php'),
        'CountryCodes' => 'misc/countries.php',
        'Database' => 'database/database.php',
        'Email' => 'email/email.php',
        'Form' => array('forms/formfields.php','forms/form.php'),
        'HtmlFilter' => 'htmlfilter/htmlfilter.php',
        'I18N' => 'i18n/i18n.php',
        'LanguageCodes' => 'misc/langs.php',
        'Page' => 'page/page.php',
        'Session' => 'session/sessions.php',
        'Spellchecker' => array('spellchecker/spellchecker.php', 'misc/langs.php', 'misc/countries.php'),
        'Template' => array('template/template_compiler.php','template/template.php'),
        'Tidy' => 'tidy/tidy.php'
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
