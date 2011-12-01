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
* @subpackage   Cribz Email
* @author       Christopher Tombleson
* @copyright    Copyright 2011 onwards
*/
class CribzEmail {
    
    /**
    * Send Mail
    * Send a plian text email
    *
    * @param mixed  $to         Array or string of email addresses to send to.
    * @param string $subject    Subject of message.
    * @param string $message    Message to send.
    * @param array  $headers    Additonal headers for email(Optiona).
    *
    * @return false on error, true on success.
    */
    function send_mail($to, $subject, $message, $headers = array()) {
        $message = strip_tags($message);

        if (is_array($to)) {
            $mailto_str = '';
            foreach ($to as $mailto) {
                $mailto_str .= $mailto . ', ';
            }
            $mailto_str = trim($mailto_str, ', ');
        } else {
            $mailto_str = $to;
        }

        if (!empty($headers)) {
            $header_str = "";
            foreach ($headers as $header) {
                $header_str .= $header . "\r\n";
            }
        }

        if (!empty($headers)) {
            if (mail($mailto_str, $subject, $message, $header_str)) {
                return true;
            } else {
                return false;
            }
        } else {
            if (mail($mailto_str, $subject, $message)) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
    * Send Html Mail
    * Send a html email
    *
    * @param mixed  $to         Array or string of email addresses to send to.
    * @param string $subject    Subject of message.
    * @param string $message    Message to send.
    * @param array  $headers    Additonal headers for email(Optiona).
    *
    * @return false on error, true on success.
    */
    function send_html_mail($to, $subject, $message, $headers = array()) {
        if (is_array($to)) {
            $mailto_str = '';
            foreach ($to as $mailto) {
                $mailto_str .= $mailto . ', ';
            }
            $mailto_str = trim($mailto_str, ', ');
        } else {
            $mailto_str = $to;
        }

        $header_str = "MIME-Version: 1.0\r\n";
        $header_str .= "Content-type: text/html; charset=iso-8859-1\r\n";

        if (!empty($headers)) {
            foreach ($headers as $header) {
                $header_str .= $header . "\r\n";
            }
        }

        if (mail($mailto_str, $subject, $message, $header_str)) {
            return true;
        } else {
            return false;
        }
    }
}
?>
