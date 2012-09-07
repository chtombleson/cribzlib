<?php
function write_md($data, $file) {
    global $writedir;

    $markdown  = "";
    $markdown .= "#".$data['title']."\n\n\n";

    foreach ($data['function'] as $function) {
        $markdown .= "##".trim($function['name'])." | ".trim($function['funcname'])."()\n";
        $markdown .= trim($function['descript'])."\n";

        if (!empty($function['params'])) {
            foreach ($function['params'] as $params) {
                foreach ($params as $param) {
                    $markdown .= "* ".trim($param)."\n";
                }
            }
            $markdown .= "\n";
        }

        if (!empty($function['return'])) {
            $markdown .= trim($function['return'])."\n";
        }
        $markdown .= "\n\n";
    }
    $filename = basename($file, '.xml');
    file_put_contents(realpath($writedir).'/'.$filename.'.md', $markdown);
}

function parse_file($file) {
    $xml = simplexml_load_file($file);
    $data = array();
    $data['title'] = $xml->classname;
    $count = 0;
    foreach ($xml->functions->function as $function) {
        $data['function'][$count]['name'] = $function->name;
        $data['function'][$count]['funcname'] = $function->realname;
        $data['function'][$count]['descript'] = $function->description;

        if (!empty($function->params)) {
            foreach ($function->params as $param) {
                $data['function'][$count]['params'][] = $param;
            }
        }

        if (!empty($function->return)) {
            $data['function'][$count]['return'] = $function->return;
        }
        $count++;
    }
    write_md($data, $file);
}

$writedir = $argv[1];
$items = scandir(dirname(__FILE__).'/docs/');

foreach ($items as $item) {
    if ($item != '.' && $item != '..') {
        parse_file(dirname(__FILE__).'/docs/'.$item);
    }
}
?>
