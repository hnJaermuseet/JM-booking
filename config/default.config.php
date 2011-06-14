<?php

/*
 * Default config
 */

// Database settings
$db_host      = 'localhost';
$db_database  = 'jm-booking';
$db_login     = 'jm-booking';
$db_password  = 'u5rsdu75ndfty66';


// System is a test system (extra messages displayed and other color schema)
$systemIsInTest = true;

// Site config (can override all settings in this file)
$path_site_config = 'config/site.config.php';

// Paths to files that are saved on disk
$entry_confirm_pdf_path = 'files/entry-confirm';
$entry_confirm_att_path = 'files/entry-attachment';
$chart_path = 'files/charts';

// Language settings
$locale = 'nb_NO'; // Others can be de_DE, en_US
$language = 'no'; // Simple translator, used in later versions


/* IP filter */

// Addresses allowed on all pages
$ip_filter_okeyaddresses = ''; // Starts with

// Pages allowed for all IPs
$ip_filter_pagesWithoutFilter = 
	array(
		'login.php'
	);