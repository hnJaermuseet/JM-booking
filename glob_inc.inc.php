<?php

/*
JM-booking
Copyright (C) 2007-2010  Jaermuseet <http://www.jaermuseet.no>
Contact: <hn@jaermuseet.no> 
Project: <http://github.com/hnJaermuseet/JM-booking>

Based on ARBS, Advanced Resource Booking System, copyright (C) 2005-2007 
ITMC der TU Dortmund <http://sourceforge.net/projects/arbs/>. ARBS is based 
on MRBS by Daniel Gardner <http://mrbs.sourceforge.net/>.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
 
 /*
  * This file is included by every invoked php file
  */

date_default_timezone_set('Europe/Oslo');

$debug_log = '';
$debug = false;
$debug_time_start = microtime(true);
if($debug)
{
    ini_set('display_errors', '1');
	error_reporting(E_ALL);
}
function debugAddToLog($file, $line, $txt = '')
{
	global $debug, $debug_log, $debug_time_start;
	
	if($debug)
	{
		$time = round(microtime(true) - $debug_time_start, 4);
		
		// Formating
		$time_parts = explode('.',"$time", 2);
		if(!isset($time_parts[1]))
				$time_parts[1] = 0;
		while(4 > strlen($time_parts[1]))
		{
			$time_parts[1] = $time_parts[1]."0";
		}
		while(4 > strlen($time_parts[0]))
		{
			$time_parts[0] = "0".$time_parts[0];
		}
		$time = $time_parts[0].'.'.$time_parts[1];
		
		// Add to log
		$debug_log .= chr(10).
			$time.'-'.$file.' - LINE '.$line.': '.$txt;
	}
}
function debugPrintLog ()
{
	global $debug, $debug_log;
	
	if($debug)
	{
		echo '<!-- '.$debug_log.' -->';
	}
}
function debugPrintTimeTotal($optionalText = '') {
    global $debug_time_start;
    $time = ((microtime(true) - $debug_time_start))*1000;
    return '<!-- TIME: '.$time.' ms '.$optionalText.'-->';
}

debugAddToLog(__FILE__, __LINE__, 'Start of glob_inc.inc.php');

session_start();

if(isset($_GET['day']))
	$day = $_GET['day'];
else
	$day = '';
if(isset($_GET['month']))
	$month = $_GET['month'];
else
	$month = '';
if(isset($_GET['year']))
	$year = $_GET['year'];
else
	$year = '';

debugAddToLog(__FILE__, __LINE__, 'Including old config (default/config.inc.php)');
include 'default/config.inc.php';

debugAddToLog(__FILE__, __LINE__, 'Including default config (config/default.config.php)');
include 'config/default.config.php';

debugAddToLog(__FILE__, __LINE__, 'Including site config ('.$path_site_config.')');
if(file_exists($path_site_config))
{
	include $path_site_config;
}
else
{
	debugAddToLog(__FILE__, __LINE__, 'Site config not found ('.$path_site_config.')');
}

/* ## Database connection ## */
// Establish a database connection.
// On connection error, the message will be output without a proper HTML
// header. There is no way I can see around this; if track_errors isn't on
// there seems to be no way to supress the automatic error message output and
// still be able to access the error text.
debugAddToLog(__FILE__, __LINE__, 'Connecting to database server');
$db_c = mysql_connect($db_host, $db_login, $db_password);

if (!$db_c || !mysql_select_db ($db_database)){
	echo chr(10).'<p>'.chr(10).
		__("FATAL ERROR: Couldn't connect to database OR could not access database.").chr(10);
	echo '<br />'.mysql_error();
	exit;
}

#lang.inc is included in config.inc.php
#also, all changeing code for language selection is at config.inc.php
#sometimes, script include other stand-alone scripts -> include_once
debugAddToLog(__FILE__, __LINE__, 'Including functions');
include_once 'functions.inc.php';

/* showAccessDenied()
 * 
 * Displays an appropate message when access has been denied
 * 
 * Retusns: Nothing
 */
function showAccessDenied($day, $month, $year, $area, $admin)
{
	global $lang,$section;
	
	if($admin)
	{
		require "include/admin_middel.php";
	}
	else
		print_header($day, $month, $year, $area);
	
	echo chr(10).
	'<H1>'.__("Access denied").'</H1>'.chr(10).
	'	<P>'.chr(10).
	'		'.__("You don't have the neccessary rights to do this action.").chr(10).
	'	</P>'.chr(10).
	'</BODY>'.chr(10).
	'</HTML>';
	exit();
}

/*
 * Test-messages
 */
if($systemIsInTest)
{
	$testSystem = array();
	$testSystem['msgLogin']
		= '<h1 align="center">Du er inne p&aring; TEST-VERSJONEN av bookingen. '.
		'Ingen data her er reelle.</h1>'.
		'<div align="center">Her inne kan du pr&oslash;ve ut ALT du ikke t&oslash;rr &aring; gj&oslash;re ellers. Sl&aring; deg l&oslash;s!<br><br>'.
		'Se ogs&aring; <a href="http://booking.jaermuseet.local/wiki/index.php/Bookingsystemet/Testomr%C3%A5de">'.
			'informasjon om testomr&aring;det p&aring; wikien</a><br><br>'.
		'Bruk f&oslash;lgende for innlogging:<br>Brukernavn: test<br>Passord: test<br><br></div>';
	$testSystem['bodyAttrib'] = ' background="img/bg-test.GIF"';
	$testSystem['bannerExtraClass'] = ' testbanner';
	
	$systemurl = 'http://infoskjerm.jaermuseet.local/jm-bookingtest';
	
	$exchangesync_from_clionly    = false;
	$importdatanova_from_clionly  = false;
}
else
{
	$testSystem = array();
	$testSystem['msgLogin']          = '';
	$testSystem['bodyAttrib']        = '';
	$testSystem['bannerExtraClass']  = '';
	
	$systemurl = 'http://booking.jaermuseet.local';
	
	$exchangesync_from_clionly    = true;
	$importdatanova_from_clionly  = true;
}

/*
 * IP filter
 * - Used denied access to all files except some pages
 * - Template displayed when accessing from a faulty address is
 *   located in tmeplates/wrong_ip.tpl
 */
if (
	isset($_SERVER['REMOTE_ADDR']) && // Not set in CLI mode
	!in_array($_SERVER['PHP_SELF'], $ip_filter_pagesWithoutFilter) &&
	substr($_SERVER['REMOTE_ADDR'],0,strlen($ip_filter_okeyaddresses)) != $ip_filter_okeyaddresses
)
{
	echo __('Access denied. This page is not accessable for external users.');
	exit();
}

/*
	## LOGIN ##
	If not logged in, redirect to login.php
*/
debugAddToLog(__FILE__, __LINE__, 'Checking login status');
$login = array();
if(isset($_SESSION['user_id']))
	$login['user_id']	= $_SESSION['user_id'];
else
	$login['user_id']	= '';

if(isset($_SESSION['user_password']))
	$login['user_password']	= $_SESSION['user_password'];
else
	$login['user_password']	= '';


// Earlier this was a setting that could be changed (MRBS etc)
// JM-booking is not made for running as non-logged-in users
if(!isset($require_login))
	$require_login = true;


if($require_login && basename($_SERVER['PHP_SELF']) != 'login.php') {
	if(!isLoggedIn())
	{
		header('Location: login.php?redirect='.htmlentities ($_SERVER['REQUEST_URI'],ENT_QUOTES));
		exit(); // Don't run anymore after header is sent, DONT REMOVE THIS!
	}
}

getUserinfoLoggedin();

if(isset($_GET['area']))
{
	if($_GET['area'] == '') {
		unset($_GET['area']);
    }
	$area = (int)$_GET['area'];
}
else
{
	if(isset($login['user_area_default']) && $login['user_area_default'] > 0)
		$area = $login['user_area_default'];
	else
		$area = get_default_area();
}

function getAreaIds($area_id_from_default) {
    $area_ids = array();
    if(isset($_GET['area'])) {
        $split = explode(',', $_GET['area']);
        foreach($split as $id) {
            $area_ids[] = (int)$id;
        }
    }

    if(!count($area_ids)) {
        $area_ids = array($area_id_from_default);
    }

    $areas = array();
    foreach($area_ids as $area_id) {
        $area = getArea($area_id);
        if(count($area)) {
            $areas[$area['area_id']] = $area;
        }
    }
    return $areas;
}

if(isset($supportMultipleAreas) && $supportMultipleAreas) {
    $areas = getAreaIds($area);
}