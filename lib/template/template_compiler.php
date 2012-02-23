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
    * @var CribzMemcache Object
    */
    private $memcache;

    /**
    * Construct
    *
    * @param string $template   Path to template file to compile.
    * @param object $memcache   CribzMemcache Object.
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
        if (empty($this->memcache)) {
            if (!file_exists($this->cache)) {
                if (!$this->create_cache_dir($this->cache)) {
                    return false;
                }
            }
        }

        $tpl_filename = basename($this->template).'.php';
        $tpl_path = $this->cache.$tpl_filename;

        if (!empty($this->memcache)) {
            $mem_file = $this->memcache->get($tpl_filename);
            if (empty($mem_file)) {
                $tpl = file_get_contents($this->template);
                $tpl = $this->replaceif($tpl, $data);
                $tpl = $this->replaceforeach($tpl, $data);
                $tpl = $this->replace($tpl, $data);
                $tpl = $this->replaceInclude($tpl, $data);

                if ($include) {
                    return $tpl;
                } else {
                    $this->memcache->add($tpl_filename, $tpl, 3600);
                    return $tpl_filename;
                }
            } else {
                return $tpl_filename;
            }
        }

        if (!file_exists($tpl_path)) {
            $tpl = file_get_contents($this->template);
            $tpl = $this->replaceif($tpl, $data);
            $tpl = $this->replaceforeach($tpl, $data);
            $tpl = $this->replace($tpl, $data);
            $tpl = $this->replaceInclude($tpl, $data);

            if ($include) {
                return $tpl;
            } else {
                file_put_contents($tpl_path, $tpl);
                return $tpl_path;
            }
        } else {
            if (fileatime($tpl_path) < (time() - 3600)) {
                unlink($tpl_path)
                $this->parse($data);
            }
            return $tpl_path;
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
                $template = new CribzTemplateCompiler($include, $this->memcache, $this->cache);
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
                        $foreach .= '<?php foreach($data['.$matches[1][$i].'] as $info): ?>';
                        $foreach_new = $matches[3][$i];
                        foreach ($vars[1] as $var) {
                            $replace = '<?php echo $info->'.$var.'; ?>';
                            $foreach_new = str_replace('&&$'.$matches[2][$i].'.'.$var.'&&', $replace, $foreach_new);
                        }
                        $foreach .= $foreach_new;
                        $foreach .= '<?php endforeach; ?>';
                    }

                    if (preg_match('#&&\$'.$matches[2][$i].'&&#s', $matches[3][$i])) {
                        $foreach .= '<?php foreach($data['.$matches[1][$i].'] as $info): ?>';
                        $foreach_new = $matches[3][$i];
                        $replace = '<?php echo $info; ?>';
                        $foreach_new = str_replace('&&$'.$matches[2][$i].'&&', $replace, $foreach_new);
                        $foreach .= $foreach_new;
                        $foreach .= '<?php endforeach; ?>';
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
                    $replace = '<?php if (isset($data['.$matches[1][$i].']) && !empty($data['.$matches[1][$i].'])): ?>';

                    if (preg_match('#{else}([^{]+)#', $matches[2][$i], $match)) {
                        $replace .= '<?php else: ?>';
                        $replace .= $match[1];
                        $replace .= '<?php endif; ?>';
                    } else {
                        $replace .= $matches[2][$i];
                        $replace .= '<?php endif; ?>';
                    }

                    $tpl = str_replace($matches[0][$i], $replace, $tpl);
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
            if (preg_match_all($regex, $tpl, $matches)) {
                foreach ($matches[1] as $match) {
                    if (in_array($match, array_keys($data))) {
                        $tpl = preg_replace('#%%\$('.$match.')%%#', '<?php echo $data['.$match.']; ?>', $tpl);
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
