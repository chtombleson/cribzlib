Cribz Lib
=========
Written By: Christopher Tombleson

GPL V3

PHP 5 Libary
------------
Classes Included:

*   Ajax Utils
*   Cookie Utils
*   Database (PDO Based)
*   Forms
*   Session Utils
*   Template Engine
*   Exceptions
*   Html Filters
*   Email Class
*   More to come


Examples
--------
### Template Engine Example
#### Basics
Template File(test.tpl):
`<p>Template Engine Example {$example}</p>`


PHP:
`<?php
    $template = new CribzTemplate('test.tpl');
    $data = array('example' => 1);
    $template->output($data);
?>`

HTML Output:
`<p>Template Engine Example 1<p>`

#### If statments
Template File:
`{if $hello}
    <p>Hello</p>
 {else}
    <p>No Hello</p>
 {/if}`

### Foreach
Template File(foreach.tpl):
`<ul>
{foreach $student as $person}
    <li>{$person.name} has grade of {$person.grade}</li>
{/foreach}
</ul>`

PHP:
`<?php
    $template = new CribzTemplate('foreach.tpl');
    $data = array();
    $data['student'][0] = new stdClass();
    $data['student'][0]->name = 'Jim Bob';
    $data['student'][0]->grade = '90/100';
    $data['student'][1] = new stdClass();
    $data['student'][1]->name = 'Joe Blogs';
    $data['student'][1]->grade = '95/100';
    $template->output($data);
?>`

HTML Output:
`<ul>
    <li>Jim Bob has a grade of 90/100</li>
    <li>Joe Blogs has a grade of 95/100</li>
</ul>`

Also see the template directory in the examples folder for
a better example of how to use the template engine.
