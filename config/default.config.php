<?php

/*
 * ## Default config ##
 *
 * Please but site spesific configs in "site.config.php" 
 * in the same folder as this file.
 *
 */

// Database settings
$db_host      = 'localhost';
$db_database  = 'jm-booking';
$db_login     = 'jm-booking';
$db_password  = 'u5rsdu75ndfty66';


// System is a test system (extra messages displayed and other color schema)
$systemIsInTest = true;

// Site config (can override all settings in this file)
if(!isset($path_site_config))
	$path_site_config = 'config/site.config.php';

// Paths to files that are saved on disk
$entry_confirm_pdf_path = 'files/entry-confirm';
$entry_confirm_att_path = 'files/entry-attachment';
$chart_path = 'files/charts';

// Language settings
$locale = 'nb_NO'; // Others can be de_DE, en_US
$language = 'no'; // Simple translator, used in later versions


// Sync with Exchange server
// User must have access to the calendars its being told to edit
/*
But in site config and remove example data:
$exchangesync_login = array(
		'username' => '123',
		'password' => '321'
	);
*/

// Import from Datanova Web reports
/*
But in site config and remove example data:
$importdatanova_login = array(
		'username' => '123',
		'password' => '321',
		'shop'     => '1',
	);
$importdatanova_baseurl = 'http://123.321.456.654/Webreports';
*/

/* IP filter */

// Addresses allowed on all pages
$ip_filter_okeyaddresses = ''; // Starts with

// Pages allowed for all IPs
$ip_filter_pagesWithoutFilter = 
	array(
		'login.php'
	);