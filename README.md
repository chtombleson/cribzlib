Cribz Lib
=========
Written By: Christopher Tombleson

GPL V3

Requirements
------------
*   PHP 5.3+
*   Tidy PHP extension installed([tidy on sourceforge](http://tidy.sourceforge.net/ "Tidy"))
*   Pspell PHP extension ([pspell php docs](http://php.net/manual/en/pspell.requirements.php "Pspell"))
*   Imap PHP extension ([imap php docs](http://php.net/manual/en/book.imap.php "Imap"))
*   Memcached extension ([memcached php docs](http://php.net/manual/en/book.memcached.php "Memcached"))
*   Curl extension ([curl php docs](http://php.net/manual/en/book.curl.php "Curl"))

PHP 5 Libary
------------
Classes Included:

*   Ajax Utils
*   Atom Feeds
*   Authentication (Database, Facebook, OpenID)
*   Cache
*   Command line script Utils
*   Cookie Utils
*   Database (PDO Based)
*   Data Store
*   Email
*   Event Queue
*   Exceptions
*   Filesystem Utils
*   Forms
*   Imap
*   Logging
*   Memcached
*   MVC
*   Page
*   Requests
*   Restful Client
*   Rss Feeds
*   Safe IFrames
*   Session Utils
*   Spellchecker
*   Template Engine
*   Tidy Class
*   Twig
*   XMLRPC Server/Client
*   XSS
*   More to come

App.php
------------
app.php can be used to the basic folder structure and base for your application.
It will create a config file and setup the cribzlib code base.

After you have run the script start building your application.

Support & Documentation
--------------------------
You can read the inline code documentation via a script readdocs.php @ documentation/readdocs.php

readdoc.php is a commandline tool. Usage php readdocs.php CLASS NAME [FUNCTION NAME]

For example: php readdocs.php CribzDatabase connect (Will give info about the database connect function).

For example: php readdocs.php CribzDatabase (Will give info about all function in the CribzDatabase class).


Wiki Docs
--------------
There is documentation on the wiki @ github.

Documention for 1.0 Stable releases is @ [Cribz Network Wiki] (http://wiki.cribznetwork.com/index.php/Cribz_Lib/1.0/ "Cribz Network Wiki")

Also documentation is also available @ [Cribz Network Wiki] (http://wiki.cribznetwork.com "Cribz Network Wiki")

IRC
-----------
\#cribznetwork on Freenode
