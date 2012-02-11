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
* @subpackage   Cribz Template Compiler
* @author       Christopher Tombleson
* @copyright    Copyright 2011 onwards
*/
class CribzTemplateCompiler {
    /**
    * Cache
    *
    * @var string
    */
    private $cache;

    /**
    * Template
    *
    * @var string
    */
    private $template;

    /**
    * Memcache
    *
    * @var bool
    */
    private $memcache;

    /**
    * Construct
    *
    * @param string $template   Path to template file to compile.
    * @param array  $memcache   Memcache server details.
    * @param string $cache      Path to cache directory.
    */
    function __construct($template, $memcache, $cache) {
        $this->template = $template;
        $this->cache = $cache;
        $this->memcache = $memcache;
    }

    /**
    * Parse
    * Parse the template file and replace the place holder and compile template.
    *
    * @param array $data    Array of data that is used to replace place holders in the template.
    * @param bool  $include If true the the template is being included into another template.
    *
    * @return string path to compiled template, or false if cache directory is writeable.
    */
    function parse($data, $include = false) {
        if (!file_exists($this->cache)) {
            if (!$this->create_cache_dir($this->cache)) {
                return false;
            }
        }

        $tpl = file_get_contents($this->template);
        $tpl = $this->replaceif($tpl, $data);
        $tpl = $this->replaceforeach($tpl, $data);
        $tpl = $this->replace($tpl, $data);
        $tpl = $this->replaceInclude($tpl, $data);
        $tpl_filename = basename($this->template).'.'.mt_rand(0, 9999);
        $tpl_path = $this->cache.$tpl_filename;

        if ($include) {
            return $tpl;
        } else {
            if (!empty($this->memcache)) {
                $cribzlib = new CribzLib();
                $cribzlib->loadModule('Memcached');

                $memcache = new CribzMemcached();
                $memcache->addServer($this->memcache['host'], $this->memcache['port'], $this->memcache['weight']);

                $memcache->add($tpl_filename, $tpl, 10);
                return $tpl_filename;
            } else {
                file_put_contents($tpl_path, $tpl);
                return $tpl_path;
            }
        }
    }

    /**
    * Replace Include
    * Compiles included templates into a template.
    *
    * @param string $tpl    Template file.
    * @param array  $data   Data to be use to replace place holders.
    *
    * @return string template file.
    */
    private function replaceInclude($tpl, $data) {
        $regex = '#(\{include="([^"]+)"\})#';

        if (preg_match_all($regex, $tpl, $matches)) {
            foreach ($matches[2] as $key => $include) {
                $template = new CribzTemplateCompiler($include, $this->cache);
                $tpl_str = $template->parse($data, true);
                $tpl = str_replace($matches[0][$key], $tpl_str, $tpl);
            }
        }
        return $tpl;
    }

    /**
    * Replace Foreach
    * Evaluates the foreach place holders in the template file and
    * replaces it with the apporiate output.
    *
    * @param string $tpl    Template file.
    * @param array  $data   Data to be used to replace place holders.
    *
    * @return string template file.
    */
    private function replaceforeach($tpl, $data) {
        $regex = '#\(foreach \$([A-Za-z0-9_]+) as \$([A-Za-z0-9_]+)\)([^\(]+)\(\/foreach\)#s';
        if (preg_match_all($regex, $tpl, $matches)) {
            for ($i=0; $i < count($matches[0]); $i++) {
                $foreach = '';
                if (isset($data[$matches[1][$i]]) && !empty($data[$matches[1][$i]])) {
                    if (preg_match_all('#&&\$'.$matches[2][$i].'\.([A-Za-z0-9_]+)&&#', $matches[3][$i], $vars)) {
                        foreach ($data[$matches[1][$i]] as $info) {
                            $foreach_new = $matches[3][$i];
                            foreach ($vars[1] as $var) {
                                $foreach_new = str_replace('&&$'.$matches[2][$i].'.'.$var.'&&', $info->$var, $foreach_new);
                            }
                            $foreach .= trim($foreach_new, "\n");
                        }
                    }

                    if (preg_match('#&&\$'.$matches[2][$i].'&&#s', $matches[3][$i])) {
                        foreach ($data[$matches[1][$i]] as $info) {
                            $foreach_new = $matches[3][$i];
                            $foreach_new = str_replace('&&$'.$matches[2][$i].'&&', $info, $foreach_new);
                            $foreach .= trim($foreach_new, "\n");
                        }
                    }
                    $tpl = str_replace($matches[0][$i], $foreach, $tpl);
                } else {
                    $tpl = str_replace($matches[0][$i], $foreach, $tpl);
                }
            }
        }
        return $tpl;
    }

    /**
    * Replace If
    * Evaulates if statment place holders in the template file and replace them with correct output.
    *
    * @param string $tpl    Template file.
    * @param array  $data   Data to be used to replace place holders.
    *
    * @return string template file.
    */
    private function replaceif($tpl, $data) {
        $regex = '#{if \$([A-Za-z0-9_]+)}([^{]+)({else}([^{]+))?{\/if}#';
        if (preg_match_all($regex, $tpl, $matches)) {
            $matchcount = count($matches[0]);
            for ($i=0; $i < $matchcount; $i++) {
                if (isset($data[$matches[1][$i]]) && !empty($data[$matches[1][$i]])) {
                    $tpl = str_replace($matches[0][$i], $matches[2][$i], $tpl);
                } else {
                    $tpl = str_replace($matches[0][$i], $matches[4][$i], $tpl);
                }
            }
        }
        return $tpl;
    }

    /**
    * Replace
    * Replace variable places holders with the correct value.
    *
    * @param string $tpl    Template file.
    * @param array  $data   Data to be used to replace place holders.
    *
    * @return string template file.
    */
    private function replace($tpl, $data) {
        $regex = '#%%\$([A-Za-z0-9_]+)%%#';
        if (!empty($data)) {
            foreach ($data as $name => $value) {
                if (preg_match_all($regex, $tpl, $matches)) {
                    foreach ($matches[1] as $match) {
                        if ($match == $name) {
                            $tpl = preg_replace('#%%\$('.$name.')%%#', $value, $tpl);
                        }
                    }
                }
            }
        }
        return $tpl;
    }

    /**
    * Create Cache Dir
    * Creates directory of the compiled template files(cache)
    *
    * @param string $cache  Path of directory to be created.
    *
    * @return false on error, true on success.
    */
    private function create_cache_dir($cache) {
        if (@mkdir($cache)) {
            return true;
        } else {
            return false;
        }
    }
}
?>
