<?php
require_once('../../cribzlib.php');
CribzLib::loadModule('Crypt');

$blowfish = CribzCrypt::hash('helloworld');
echo "blowfish: " . $blowfish . "\n";

$md5 = CribzCrypt::hash('helloworld', 'md5');
echo "MD5: " . $md5 . "\n";

$sha = CribzCrypt::hash('helloworld', 'sha512');
echo "SHA512: " . $sha . "\n";

$comp_blow = CribzCrypt::compareHash('helloworld', $blowfish);
echo ($comp_blow == true) ? "Blowfish compare good\n" : "Blowfish no good\n";

$comp_md5 = CribzCrypt::compareHash('helloworld', $md5);
echo ($comp_md5 == true) ? "MD5 compare good\n" : "MD5 no good\n";

$comp_sha = CribzCrypt::compareHash('helloworld', $sha);
echo ($comp_sha == true) ? "SHA compare good\n" : "SHA no good\n";
?>
