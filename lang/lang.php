<?php


// Simple translator
require_once 'lang_no.php';

/**
 * Translate string
 * 
 * @param   string  Text to translate
 * @return  string  Translated text in UTF8
 */
function _l($text)
{
	global $language;
	
	$langFunction = 'lang'.ucfirst($language);
	return $langFunction($text);
}

/**
 * Translate string
 * 
 * @param   string  Text to translate
 * @return  string  Encoded to HTML
 */
function _h($text)
{
	return htmlentities(utf8_decode(_l($text)), ENT_QUOTES);
}

?>