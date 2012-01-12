<?php
require_once(dirname(__FILE__).'/../../cribzlib.php');
$cribzlib = new CribzLib();

// Load the Template module
$cribzlib->loadModule('Template');

// Creating new instance of template class
// First Parameter is path to template file to compile.
// Second Parameter is path to cache directory to store compiled templates(optional, default is /tmp/cribzcache/).
$cribz_template = new CribzTemplate(dirname(__FILE__).'/template.tpl', dirname(__FILE__).'/cache/');

// Time to define the data to be inputed into the template
$data = array();

// Define sitename to replace tag {$sitename} in the template.
$data['sitename'] = 'Cribz Lib Example';

// Define some news for the {foreach $new as $newsitem} loop
$data['news'][0] = new stdClass();
$data['news'][0]->name = 'Test';
$data['news'][0]->description = 'The template engine work booya.';

$data['news'][1] = new stdClass();
$data['news'][1]->name = 'Test 2';
$data['news'][1]->description = 'It still works hell yeah.';

//Lets compile and output the template.
$cribz_template->output($data);
?>
