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
function write_doc_file($class, $functions) {
    if (empty($functions)) {
        return null;
    }

    $xml  = "<?xml version=\"1.0\"?>\n";
    $xml .= "<class>\n";
    $xml .= "\t<classname>".htmlentities($class)."</classname>\n";

    $class = strtolower($class);

    $xml .= "\t<functions>\n";
    foreach ($functions as $function) {
        $xml .= "\t\t<function>\n";
        $xml .= "\t\t\t<realname>".htmlentities($function->realname)."</realname>\n";
        $xml .= "\t\t\t<name>".htmlentities($function->name)."</name>\n";
        $xml .= "\t\t\t<description>".htmlentities($function->description)."</description>\n";

        if (!empty($function->params)) {
            $xml .= "\t\t\t<params>\n";
            foreach ($function->params as $param) {
                $xml .= "\t\t\t\t<param>".htmlentities($param)."</param>\n";
            }
            $xml .= "\t\t\t</params>\n";
        }

        if (!empty($function->return)) {
            $xml .= "\t\t\t<return>".htmlentities($function->return)."</return>\n";
        }
        $xml .= "\t\t</function>\n";
    }
    $xml .= "\t</functions>\n";
    $xml .= "</class>";

    file_put_contents(dirname(__FILE__).'/docs/'.$class.'.xml', $xml);
}

function parse_file($file) {
    $regex = '#/\*\*(.+)\*/#Us';
    $content = file_get_contents($file);
    $functions = array();
    $class = '';

    if (preg_match('#class ([a-zA-Z0-9_]+)#', $content, $classmatch)) {
        $class = $classmatch[1];
    }

    if (!empty($class)) {
        preg_match_all('#function ([a-zA-Z0-9_\-]+)#', $content, $funcmatches);

        if (preg_match_all($regex, $content, $matches)) {
            for ($i=0; $i < count($matches[0]); $i++) {
                if (!preg_match('#(@package)#', $matches[1][$i]) && !preg_match('#(@var)#', $matches[1][$i])) {
                    $matches[1][$i] = str_replace('*', '', $matches[1][$i]);
                    $lines = explode("\n", $matches[1][$i]);

                    $functions[$i] = new stdClass();
                    $functions[$i]->name = trim($lines[1]);
                    $functions[$i]->description = trim($lines[2]);
                    $functions[$i]->params = array();
                    unset($lines[0], $lines[1], $lines[2]);

                    foreach ($lines as $line) {
                        if (!empty($line)) {
                            if (preg_match('#(@param)#', $line)) {
                                $functions[$i]->params[] = trim($line);
                            }

                            if (preg_match('#(@return)#', $line)) {
                                $functions[$i]->return = trim($line);
                            }
                        }
                    }

                    $name = strtolower($functions[$i]->name);
                    if ($name == 'constructor' || $name == 'construct') {
                        $name = '__construct';
                    }

                    if ($name == 'destruct') {
                        $name = '__destruct';
                    }

                    if ($name == 'to string' && in_array('__toString', $funcmatches[1])) {
                        $name = '__tostring';
                    }

                    if ($name == 'get' && in_array('__get', $funcmatches[1])) {
                        $name = '__get';
                    }

                    if ($name == 'set' && in_array('__set', $funcmatches[1])) {
                        $name = '__set';
                    }

                    $camel = str_replace(' ', '', $name);
                    $under = str_replace(' ', '_', $name);

                    foreach ($funcmatches[1] as $funcname) {
                        $name = strtolower($funcname);

                        if ($camel == $name) {
                            $functions[$i]->realname = $funcname;
                        }

                        if ($under == $name) {
                            $functions[$i]->realname = $funcname;
                        }
                    }
                }
            }
            write_doc_file($class, $functions);
        }
    }
}

function parse_dir($path) {
    $path = realpath($path);
    $items = scandir($path);

    foreach ($items as $item) {
        if ($item != '.' && $item != '..') {
            if (is_dir($path.'/'.$item)) {
                parse_dir($path.'/'.$item);
            } else {
                if (preg_match('#(\.php)#', $item) && !preg_match('#(Twig)#', $path) && !preg_match('#(facebook-php-sdk)#', $path)) {
                    echo "Documenting file: ".$path."/".$item."\n";
                    parse_file($path.'/'.$item);
                }
            }
        }
    }
}

$items = array(dirname(dirname(__FILE__)).'/cribzlib.php', dirname(dirname(__FILE__)).'/lib');
foreach ($items as $item) {
    if (is_dir($item)) {
        parse_dir($item);
    } else {
        echo "Documenting file: ".$item."\n";
        parse_file($item);
    }
}
?>
