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
function load_doc($class) {
    $xml = @simplexml_load_file(dirname(__FILE__).'/docs/'.$class.'.xml');
    return $xml;
}

function func_name($xml, $function) {
    foreach ($xml->functions->function as $key => $value) {
        if ($function == strtolower($value->realname)) {
            return $value->realname;
        }
    }
}

function display_docs($class, $function=null) {
    $xml = load_doc($class);

    if (empty($xml)) {
        echo "Unable to read the xml documentation file\n";
        exit;
    }

    if (empty($function)) {
        echo "Documentation for class: ".html_entity_decode($xml->classname)."\n\n";
    } else {
        $name = func_name($xml, $function);

        if (empty($name)) {
            echo "Documentation for class: ".html_entity_decode($xml->classname)."\n\n";
            echo "Could not find function ".$function.".\n";
        } else {
            echo "Documentation for class function: ".html_entity_decode($xml->classname)."::";
            echo $name."\n\n";
        }
    }

    if (empty($function)) {
        foreach ($xml->functions->function as $docfunction) {
            echo "Function: ".html_entity_decode($docfunction->realname)."\n";
            echo "Description:\n";
            echo "    ".html_entity_decode($docfunction->name)."\n";
            echo "    ".html_entity_decode($docfunction->description)."\n";

            if (!empty($docfunction->params->param)) {
                echo "Params:\n";
                foreach ($docfunction->params->param as $param) {
                    echo "    ".html_entity_decode($param)."\n";
                }
            }

            if (!empty($docfunction->return)) {
                $return = str_replace('@return', '', $docfunction->return);
                echo "Return: ".trim(html_entity_decode($return))."\n";
            }
            echo "==================================================================\n";
        }
    } else {
        foreach ($xml->functions->function as $docfunction) {
            if ($name == $docfunction->realname) {
                echo "Function: ".html_entity_decode($docfunction->realname)."\n";
                echo "Description:\n";
                echo "    ".html_entity_decode($docfunction->name)."\n";
                echo "    ".html_entity_decode($docfunction->description)."\n";

                if (!empty($docfunction->params->param)) {
                    echo "Params:\n";
                    foreach ($docfunction->params->param as $param) {
                        echo "    ".html_entity_decode($param)."\n";
                    }
                }

                if (!empty($docfunction->return)) {
                    $return = str_replace('@return', '', $docfunction->return);
                    echo "Return: ".trim(html_entity_decode($return))."\n";
                }
                echo "==================================================================\n";
            }
        }
    }
}

if (count($argv) < 2) {
    echo "Usage: php readdocs.php CLASS NAME [FUNCTION NAME]\n";
    exit;
}

$class = strtolower($argv[1]);
$function = empty($argv[2]) ? null : strtolower($argv[2]);

display_docs($class, $function);
?>
