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
    * Construct
    *
    * @param string $template   Template file to compile.
    * @param string $cache      Path to cache directory.
    */
    function __construct($template, $cache) {
        $this->template = $template;
        $this->cache = $cache;
    }

    /**
    * Parse
    * Parse the template file and replace the place holder and compile template.
    *
    * @param array $data    Array of data that is used to replace place holders in the template.
    *
    * @return string path to compiled template, or false if cache directory is writeable.
    */
    function parse($data) {
        if (!file_exists($this->cache)) {
            if (!$this->create_cache_dir($this->cache)) {
                return false;
            }
        }

        $tpl = file_get_contents($this->template);
        $tpl = $this->replaceforeach($tpl, $data);
        $tpl = $this->replaceif($tpl, $data);
        $tpl = $this->replace($tpl, $data);
        $tpl_path = $this->cache.basename($this->template).'.'.mt_rand(0, 9999);
        file_put_contents($tpl_path, $tpl);
        return $tpl_path;
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
        $regex = '#(\{foreach \$([A-Za-z0-9_]+) as \$([A-Za-z0-9_]+)\}(.*)\{\/foreach\})#s';

        if (preg_match_all($regex, $tpl, $matches)) {
            for ($i=0; $i < count($matches[0]); $i++) {
                $foreach = '';

                if (isset($data[$matches[2][$i]]) && !empty($data[$matches[2][$i]])) {
                    if (preg_match_all('#\$'.$matches[3][$i].'\.([A-Za-z0-9_]+)#s', $matches[4][$i], $vars)) {
                        foreach ($data[$matches[2][$i]] as $info) {
                            $foreach_new = $matches[4][$i];

                            foreach ($vars[1] as $var) {
                                $foreach_new = str_replace('$'.$matches[3][$i].'.'.$var, $info->$var, $foreach_new);
                            }
                            $foreach .= trim($foreach_new, "\n");
                        }
                    }
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
        $regex = '#(\{if \$([A-Za-z0-9_]+)\}(.*)\{\/if\})#s';

        if (preg_match_all($regex, $tpl, $matches)) {
            for ($i=0; $i < count($matches[0]); $i++) {
                if (isset($data[$matches[2][$i]]) && !empty($data[$matches[2][$i]])) {
                    if (preg_match('#(\{else\}(.*))#s', $matches[3][$i], $else_match)) {
                        $tpl = str_replace($else_match[0], '', $tpl);
                    }

                    $tpl = str_replace("{if \$".$matches[2][$i]."}", '', $tpl);
                    $tpl = str_replace("{/if}", '', $tpl);
                } else {
                    if (preg_match('#((.*)\{else\}(.*))#s', $matches[3][$i], $else_match)) {
                        $tpl = str_replace($else_match[0], $else_match[3], $tpl);
                    }
                    $tpl = str_replace("{if \$".$matches[2][$i]."}", '', $tpl);
                    $tpl = str_replace("{/if}", '', $tpl);
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
        $regex = '#(\{\$([A-Za-z0-9_]+)\})#';
        
        if (!empty($data)) {
            foreach ($data as $name => $value) {
                if (preg_match_all($regex, $tpl, $matches)) {
                    foreach ($matches[2] as $match) {
                        if ($match == $name) {
                            $tpl = preg_replace('#(\{\$('.$name.')\})#', $value, $tpl);
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
