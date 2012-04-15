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
* @subpackage   Cribz Rss
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzRss {

    /**
    * Title
    *
    * @var string
    */
    private $title;

    /**
    * Link
    *
    * @var string
    */
    private $link;
    
    /**
    * Description
    *
    * @var string
    */
    private $description;

    /**
    * Lang
    *
    * @var string
    */
    private $lang;

    /**
    * Domain
    *
    * @var string
    */
    private $domain;

    /**
    * Constructor
    * Create a new instance of Cribz Rss
    *
    * @param string $title          Feed title
    * @param string $link           Feed url
    * @param string $description    Feed description
    * @param string $domain         Domain
    * @param string $lang           Language (optional)
    */
    function __construct($title, $link, $description, $domain, $lang='en-us') {
        $this->title = $title;
        $this->link = $link;
        $this->description = $description;
        $this->lang = $lang;
        $this->domian = $domain;
    }

    /**
    * Build Rss
    * Build the xml structure for the rss feed
    *
    * @param array $items   Array of stdClass for rss items
    *
    * @return string xml
    */
    function buildRss($items) {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<rss version=\"2.0\">\n";
        $xml .= "\t<channel>\n";
        $xml .= "\t\t<title>".$this->title."</title>\n";
        $xml .= "\t\t<link>".$this->link."</title>\n";
        $xml .= "\t\t<description>".$this->description."</description>\n";
        $xml .= "\t\t<language>".$this->lang."</language>\n";
        $xml .= "\t\t<copyright>Copyright&copy; ".date('Y')." ".$this->domain."</copyright>\n";

        if (!empty($items)) {
            foreach ($items as $item) {
                $xml .= $this->addItem($item->title, $item->link, $item->description, $item->time);
            }
        }

        $xml .= "\t</channel>\n";
        $xml .= "</rss>";
        return $xml;
    }

    /**
    * Add Item
    * Create the xml structure for an item
    *
    * @param string $title       Item title
    * @param string $description Item description
    * @param string $link        Item link
    * @param int    $time        Unix timestamp
    *
    * @return string item xml
    */
    private function addItem($title, $description, $link, $time) {
        $xml = "\t\t<item>\n";
        $xml .= "\t\t\t<title>".$title."</title>\n";
        $xml .= "\t\t\t<description>".$description."</description>\n";
        $xml .= "\t\t\t<link>".$link."</link>\n";
        $xml .= "\t\t\t<pubDate>".date("D, d M Y H:i:s O", $time)."</pubDate>\n";
        $xml .= "\t\t</item>\n";
        return $xml;
    }
}
?>
