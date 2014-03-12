<?php


// Simple translator
require_once 'lang_no.php';

/**
 * Translate string
 * 
 * @param   string  $text  Text to translate
 * @return  string  Translated text in UTF8
 */
function _l($text)
{
	global $language;
	
	$langFunction = 'lang'.ucfirst($language); // function langNo()
    // TODO: remove charset convertion when everything is UTF-8
	return utf8_decode($langFunction($text));
}

function __($text) {
    return _l($text);
}

/**
 * Translate string
 * 
 * @param   string  $text  Text to translate
 * @return  string  Encoded to HTML
 */
function _h($text)
{
	return htmlentities(_l($text), ENT_QUOTES);
}