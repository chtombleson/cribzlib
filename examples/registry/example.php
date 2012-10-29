<?php
require_once('../../cribzlib.php');

// Add item to registry
CribzLib::registryAdd('test', 'hello world');

// Get the test item from registry
echo CribzLib::registryGet('test') . "\n";

// List all item in registry
print_r(CribzLib::registryList());

// Remove item from registry
CribzLib::registryRemove('test');
?>
