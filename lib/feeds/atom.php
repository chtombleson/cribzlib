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
* @subpackage   Cribz Atom
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzAtom {

    /**
    * Title
    *
    * @var string
    */
    private $title;

    /**
    * Subtitle
    *
    * @var string
    */
    private $subtitle;

    /**
    * Link
    *
    * @var string
    */
    private $link;

    /**
    * Author
    *
    * @var array
    */
    private $author;

    /**
    * Domain
    *
    * @var string
    */
    private $domain;

    /**
    * Lang
    *
    * @var string
    */
    private $lang;

    /**
    * Constructor
    * Create a new instance of Cribz Atom
    *
    * @param string $title      Feed Title
    * @param string $subtitle   Feed Subtitle
    * @param string $link       Feed url
    * @param string $domain     Domain name
    * @param array  $author     Array of author details name & email
    * @param string $lang       Language code
    */
    function __construct($title, $subtitle, $link, $domain, $author, $lang='en-us') {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->link = $link;
        $this->domain = $domain;
        $this->author = $author;
        $this->lang = $lang;
    }

    /**
    * Build Atom
    * Create the xml structure for the atom feed.
    *
    * @param array $entries     Array of stdClass for atom entries
    *
    * @return string xml structure
    */
    function buildAtom($entries) {
        $xml = "<?xml version=\"1.0\"?>\n";
        $xml .= "<feed xml:lang=\"".$this->lang."\" xmlns=\"http://www.w3.org/2005/Atom\">\n";
        $xml .= "\t<title>".$this->title."</title>\n";
        $xml .= "\t<subtitle>".$this->subtitle."</subtitle>\n";
        $xml .= "\t<link href=\"".$this->link."\" rel=\"self\"/>\n";
        $xml .= "\t<updated>".date(DATE_ATOM)."</updated>\n";
        $xml .= "\t<author>\n";
        $xml .= "\t\t<name>".$this->author['name']."</name>\n";
        $xml .= "\t\t<email>".$this->author['email']."</email>\n";
        $xml .= "\t</author>\n";
        $xml .= "\t<id>tag:".$this->domain.",".date('Y').":".$this->link."</id>\n";

        if (!empty($entries)) {
            foreach ($entries as $entry) {
                $xml .= $this->addEntry($entry->title, $entry->link, $entry->time, $entry->author, $entry->summary);
            }
        }

        $xml .= "</feed>";
        return $xml;
    }

    /**
    * Add Entry
    * Create the xml structure for an entry
    *
    * @param string $title      Entry title
    * @param string $link       Entry link
    * @param int    $time       Unix timestamp
    * @param string $author     Authors name
    * @param string $summary    Feed summary
    *
    * @return string of xml structure
    */
    private function addEntry($title, $link, $time, $author, $summary) {
        $xml = "\t<entry>\n"
        $xml .= "\t\t<title>".$title."</title>\n";
        $xml .= "\t\t<link type=\"text/html\" href=\"".$link."\" />\n";
        $xml .= "\t\t<id>tag:".$this->domain.",".date('Y').":".$link."</id>\n";
        $xml .= "\t\t<updated>".date(DATE_ATOM, $time)."</updated>\n";
        $xml .= "\t\t<author>\n";
        $xml .= "\t\t\t<name>".$author."</name>\n";
        $xml .= "\t\t</author>\n";
        $xml .= "\t\t<summary>".$summary."</summary>\n";
        $xml .= "\t</entry>\n";
        return $xml;
    }
}
?>
