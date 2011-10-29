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

// Invoice
$invoice_sendto = array('rekneskap@jaermuseet.no'); // Can ble multiple emails
//$invoice_sendto = array('hn@jaermuseet.no', 'hallvard.nygaard@jaermuseet.no'); // Testing addresses
$invoice_location = 'files/invoice/';

/* External login */
// Add to site.config.php:
/*
$login_internal_addresses = array('192.168.', '127.0.'); // Starts with one of these
$login_password_external_complex           = true; // According to http://technet.microsoft.com/en-us/library/cc786468%28WS.10%29.aspx
$login_password_external_minchar           = 123;
$login_password_external_maxage            = 123*24*60*60; // in seconds
$login_password_external_new_notamonglast3 = true;
*/

/* IP filter */

// Addresses allowed on all pages
$ip_filter_okeyaddresses = ''; // Starts with

// Pages allowed for all IPs
$ip_filter_pagesWithoutFilter = 
	array(
		'login.php'
	);
