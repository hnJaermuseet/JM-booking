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


$debug_log = '';
$debug = true;
if($debug)
{
	$debug_time_start = microtime(true);
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

debugAddToLog(__FILE__, __LINE__, 'Start of glob_inc.inc.php');

session_start();

if(isset($_GET['pview']))
	$pview=1;
else
{
	unset($pview);
	$pview=0;
}

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

debugAddToLog(__FILE__, __LINE__, 'Including config');
include "default/config.inc.php";

if (!defined('LC_MESSAGES'))
	define('LC_MESSAGES', 6); // windows workaround for LC_MESSAGES

//putenv ("LANGUAGE=nb_NO");
putenv ("LANG=".$locale); 

//setlocale(LC_MESSAGES, $locale);
bindtextdomain("arbs", "./lang");

textdomain("arbs");

setlocale(LC_TIME, "");


// Establish a database connection.
// On connection error, the message will be output without a proper HTML
// header. There is no way I can see around this; if track_errors isn't on
// there seems to be no way to supress the automatic error message output and
// still be able to access the error text.
debugAddToLog(__FILE__, __LINE__, 'Connecting to database server');
$db_c = mysql_connect($db_host, $db_login, $db_password);

if (!$db_c || !mysql_select_db ($db_database)){
	echo "\n<p>\n", _("FATAL ERROR: Couldn't connect to database."), "\n";
	exit;
}

#lang.inc is included in config.inc.php
#also, all changeing code for language selection is at config.inc.php
#sometimes, script include other stand-alone scripts -> include_once
debugAddToLog(__FILE__, __LINE__, 'Including functions');
include_once "functions.inc.php";
debugAddToLog(__FILE__, __LINE__, 'Including auth_sql.inc.php');
include_once "auth_sql.inc.php";

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
	'<H1>'._("Access denied").'</H1>'.chr(10).
	'	<P>'.chr(10).
	'		'._("You don't have the neccessary rights to do this action.").chr(10).
	'	</P>'.chr(10).
	'</BODY>'.chr(10).
	'</HTML>';
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
	if($_GET['area'] == '')
		unset($_GET['area']);
	$area = (int)$_GET['area'];
}
else
{
	if(isset($login['user_area_default']) && $login['user_area_default'] > 0)
		$area = $login['user_area_default'];
	else
		$area = get_default_area();
}

?>