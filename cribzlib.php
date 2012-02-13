<?php
class CribzLib {
    private $modules = array(
        'Ajax' => array('ajax/ajax.php'),
        'Cookie' => array('cookie/cookies.php'),
        'CountryCodes' => array('misc/countries.php'),
        'Database' => array('database/database.php'),
        'Email' => array('email/email.php'),
        'Form' => array('forms/formfields.php','forms/form.php'),
        'HtmlFilter' => array('htmlfilter/htmlfilter.php'),
        'I18N' => array('i18n/i18n.php'),
        'Imap' => array('imap/imap.php'),
        'LanguageCodes' => array('misc/langs.php'),
        'Memcached' => array('memcached/memcached.php'),
        'Page' => array('page/page.php'),
        'Session' => array('session/sessions.php'),
        'Spellchecker' => array('spellchecker/spellchecker.php', 'misc/langs.php', 'misc/countries.php'),
        'Template' => array('template/template_compiler.php','template/template.php'),
        'Tidy' => array('tidy/tidy.php')
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
