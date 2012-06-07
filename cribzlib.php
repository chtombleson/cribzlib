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
* @subpackage   CribzLib
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
require_once(dirname(__FILE__).'/lib/exception/exception.php');
class CribzLib {
    /**
    * Modules
    *
    * @var array
    */
    private $modules = array(
        'Ajax' => array('ajax/ajax.php'),
        'Atom' => array('feeds/atom.php'),
        'Cache' => array('cache/cache.php'),
        'Cookie' => array('cookie/cookies.php'),
        'CountryCodes' => array('misc/countries.php'),
        'Database' => array('database/database.php'),
        'DatabaseLog' => array('log/databaselog.php'),
        'Email' => array('email/email.php'),
        'Filesystem' => array('filesystem/filesystem.php'),
        'Form' => array('forms/formfields.php','forms/form.php'),
        'HtmlFilter' => array('htmlfilter/htmlfilter.php'),
        'I18N' => array('i18n/i18n.php'),
        'Imap' => array('imap/imap.php'),
        'LanguageCodes' => array('misc/langs.php'),
        'Log' => array('log/log.php'),
        'Memcached' => array('memcached/memcached.php'),
        'MVC' => array('mvc/model.php', 'mvc/view.php', 'mvc/controller.php'),
        'Page' => array('page/page.php'),
        'Request' => array('request/request.php'),
        'Rss' => array('feeds/rss.php'),
        'Session' => array('session/sessions.php'),
        'Spellchecker' => array('spellchecker/spellchecker.php', 'misc/langs.php', 'misc/countries.php'),
        'Template' => array('template/template_compiler.php','template/template.php'),
        'Tidy' => array('tidy/tidy.php'),
        'Twig' => array('twig/twig.php'),
        'XmlrpcServer' => array('xmlrpc/server.php'),
        'XmlrpcClient' => array('xmlrpc/client.php')
    );

    /**
    * Load Module
    * Load a module/class
    *
    * @param string $name   Name of module
    */
    function loadModule($name) {
        $modules = array_keys($this->modules);
        if (in_array($name, $modules)) {
            foreach ($this->modules[$name] as $file) {
                require_once(dirname(__FILE__).'/lib/'.$file);
            }
            return true;
        } else {
            throw new CribzException('Module with the name: '.$name.' does not exist.');
        }
    }
}
?>
