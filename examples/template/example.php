<?php
require_once('../../cribzlib.php');

// Load the CribzTemplate Class
CribzLib::loadModule('Template');

// Create a new instance of CribzTemplate
$cribz_template = new CribzTemplate(dirname(__FILE__).'/', '/tmp/cache/', true);

// Load a template
$template = $cribz_template->loadTemplate('example.tpl');

// Render a template and pass some data to the template
echo $template->render(array('hello' => 'Hi there !!!'));
echo "\n";
echo $template->render(array());
?>
