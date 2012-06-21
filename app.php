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
* @subpackage   App.php
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
function get_input() {
    return trim(fgets(STDIN));
}

function parse_options($args) {
    $valid_options = array('help', 'directory', 'database', 'url', 'cache');
    $options = array();

    foreach ($args as $key => $arg) {
        if (preg_match('#-([a-z]+)#', $arg)) {
            $arg = trim(str_replace('-', '', $arg));
            if (in_array($arg, $valid_options)) {
                switch ($arg) {
                    case 'help':
                        $options[$arg] = trim($arg);
                        break;

                    case 'database':
                        $options[$arg] = trim($arg);
                        break;

                    case 'directory':
                        $options[$arg] = trim($args[$key + 1]);
                        break;

                    case 'url':
                        $options[$arg] = trim($args[$key + 1]);
                        break;

                    case 'cache':
                        $options[$arg] = trim($args[$key + 1]);
                        break;
                }
            }
        }
    }
    return $options;
}

function process_database() {
    $valid_drivers = array('mysql', 'sqlite','pgsql');
    $db = array();
    $valid_driver = false;

    while (!$valid_driver) {
        echo "Database Driver (mysql, sqlite, pgsql(PostgreSql)): ";
        $db['driver'] = get_input();

        if (!in_array($db['driver'], $valid_drivers)) {
            echo "Invalid Database Driver\n";
        } else {
            $valid_driver = true;
        }
    }

    echo "Database Host: ";
    $db['host'] = get_input();

    echo "Database Name: ";
    $db['name'] = get_input();

    echo "Database User: ";
    $db['user'] = get_input();

    echo "Database Password: ";
    $db['pass'] = get_input();

    echo "Database Port: ";
    $in = get_input();
    $db['port'] = empty($in) ? 0 : $in;

    return $db;
}

function process_config($options, $db) {
    $config = "<?php\n";
    $config .= "\$config = new stdClass();\n";

    foreach ($options as $option => $value) {
        switch ($option) {
            case 'directory':
                $config .= "\$config->dirroot = \"{$value}\";\n";
                break;

            case 'url':
                $config .= "\$config->www = \"{$value}\";\n";
                break;

            case 'cache':
                $config .= "\$config->cache = \"{$value}\";\n";
                break;
        }
    }

    $config .= "\$config->libroot = \"{$options['directory']}lib/\";\n";

    if (!empty($db)) {
        $config .= "\$config->db = array(\n";

        foreach ($db as $key => $value) {
            if ($key != 'port') {
                $config .= "\t'{$key}' => '{$value}',\n";
            } else {
                $config .= "\t'{$key}' => {$value},\n";
            }
        }

        $config .= ");\n";
    }

    $config .= "?>";
    return file_put_contents($options['directory'].'config.php', $config);
}

function confirm($options, $db) {
    echo "\n=======================================\n";
    echo "Application details:\n";

    foreach ($options as $option => $value) {
        if ($option != 'help' && $option != 'database') {
            echo "\t{$option}: {$value}\n";
        }
    }

    if (!empty($db)) {
        echo "\nDatabase details:\n";
        foreach ($db as $key => $value) {
            echo "\t{$key}: {$value}\n";
        }
    }

    echo "Are these details correct[y/n]: ";
    $reply = strtolower(get_input());

    if ($reply == 'y') {
        return true;
    }

    return false;
}

function create_app($options, $db) {
    require_once(dirname(__FILE__).'/version.php');
    $downloadurl = "https://github.com/downloads/chtombleson/cribzlib/cribzlib-{$version->release}-release.tar.gz";

    $options['directory'] = rtrim($options['directory'], '/').'/';
    $options['url ']= rtrim($options['url'], '/').'/';
    $options['cache'] = empty($options['cache']) ? '/tmp/cache/' : rtrim($options['cache'], '/').'/';

    if (!mkdir($options['directory'], 0755)) {
        echo "!!!!!Error: Unable to create directory, {$directory}\n";
        exit;
    }

    mkdir($options['directory'].'lib/', 0755);

    if (!process_config($options, $db)) {
        echo "!!!!!Error: Unable to write config file, {$directory}config.php\n";
        exit;
    }

    $cmd = "cd {$options['directory']}lib/ && wget {$downloadurl} && tar xvf cribzlib-{$version->release}-release.tar.gz";
    $cmd .= " && rm cribzlib-{$version->release}-release.tar.gz";
    echo exec($cmd);

    echo "\nApplication is ready to be built!!!!!!!\n";
    exit;
}

function help_message() {
    echo "Useage: php app.php -directory /var/www/app/ -url http://test.example.com [-database, -cache]\n";
    echo "\t-help: Show this message.\n";
    echo "\t-directory: Path to directory where the app will live.\n";
    echo "\t-url: Web address for the app.\n";
    echo "\t-database: Use a database backend.\n";
    echo "\t-cache: Path to cache directory (Default: /tmp/cache/).\n";
}

$options = parse_options($argv);
$db = array();

if (empty($options) || !in_array('directory', array_keys($options)) || !in_array('url', array_keys($options))) {
    help_message();
    exit;
}

foreach ($options as $option => $value) {
    switch ($option) {
        case 'help':
            help_message();
            exit;
            break;

        case 'database':
            $db = process_database();
            break;
    }
}

if (confirm($options, $db)) {
    create_app($options, $db);
} else {
    exit;
}
?>
