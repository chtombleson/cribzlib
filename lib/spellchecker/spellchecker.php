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
* @subpackage   Cribz Spellchecker
* @author       Christopher Tombleson
* @copyright    Copyright 2011 onwards
*/
class CribzSpellchecker {
    /**
    * Lang
    *
    * @var string
    */
    private $lang;

    /**
    * Mode
    *
    * @var int
    */
    private $mode;

    /**
    * Construct
    *
    * @param string $lang       String with lang code.
    * @param int    $mode       Mode to be passed to pspell(Optional).
    */
    function __construct($lang, $mode = PSPELL_NORMAL) {
        $cribzlib = new CribzLib();
        $cribzlib->loadModule('LanguageCodes');
        $cribzlib->loadModule('CountryCodes');

        $this->setMode($mode);
        $this->setLang($lang);
    }

    /**
    * Set Mode
    * Check to see if mode is valid.
    *
    * @param int $mode      Mode to be passed to pspell.
    *
    * @return true if mode is valid and set or false on error.
    */
    function setMode($mode) {
        $valid_modes = array(PSPELL_FAST, PSPELL_NORMAL, PSPELL_BAD_SPELLERS, PSPELL_RUN_TOGETHER);
        if (!in_array($mode, $valid_modes)) {
            return false;
        }
        $this->mode = $mode;
    }
    
    /**
    * Set Lang
    * Check to see if lang is valid and then set it.
    *
    * @param string $lang       String containing lang code.
    *
    * @return true if valid and set or false on error.
    */
    function setLang($lang) {
        global $langcodes, $countrycodes;
        $langcodes = array_keys($langcodes);
        $countrycodes = array_keys($countrycodes);

        if (strpos('-', $lang) !== false) {
            $langpart = explode('-', $lang);
            if ($this->check_array($langpart[0], $langcodes) && $this->check_array($langpart[1], $countrycodes)) {
                $this->lang = $lang;
                return true;
            }
            return false;

        } else if (strpos('_', $lang) !== false) {
            $langpart = explode('_', $lang);
            if ($this->check_array($langpart[0], $langcodes) && $this->check_array($langpart[1], $countrycodes)) {
                $this->lang = $lang;
                return true;
            }
            return false;

        } else {
            if ($this->check_array($lang, $langcodes)) {
                $this->lang = $lang;
                return true;
            }
            return false;
        }
    }

    /**
    * Spellcheck Block
    * Check spelling in a block of text.
    *
    * @param string $block          Block of text to check.
    * @param bool   $suggestions    Set to true if you want a list of word suggestion(Optional).
    *
    * @return array of incorrectly spelled words.
    */
    function spellcheck_block($block, $suggestions = false) {
        $words = explode(' ', $block);
        $spelled_wrong = array();

        foreach ($words as $word) {
            $correct = $this->spellcheck_word($word, $suggestions);
            if (!$correct) {
                if (!isset($spelled_wrong[$word])) {
                    $spelled_wrong[$word] = $correct;
                }
            }
        }
        return $spelled_wrong;
    }

    /**
    * Spellcheck Word
    * Check spelling of a word.
    *
    * @param string $word           Word to check.
    * @param bool   $suggestions    Set to true if you want a list of word suggestion(Optional).
    *
    * @return true if spelled correctly, an array of suggestions or false if not spelled correctly.
    */
    function spellcheck_word($word, $suggestions = false) {
        $pspell = pspell_new($this->lang, '', '', 'utf-8', $this->mode);

        if (!pspell_check($pspell, $word)) {
            if ($suggestions) {
                return pspell_suggest($pspell, $word);
            }
            return false;
        }
        return true;
    }

    /**
    * Check Array
    * Check and see if value is in an array.
    *
    * @param mixed $value       Value to search for in array.
    * @param array $array       Array to search.
    *
    * @return true if in array or false if not found.
    */
    private function check_array($value, $array) {
        if (in_array($value, $array)) {
            return true;
        }
        return false;
    }
}
?>
