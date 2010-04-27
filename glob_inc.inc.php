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


session_start();
session_register('instance');
session_register('session_selected_language');

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
	
if(isset($_GET['instance']))
{
	// check for invalid or malcious instance values
	if (strrpos($_GET['instance'],".")!=0 || strrpos($_GET['instance'],"/")!=0) {
		echo(_("Invalid instance-name"));
		exit;
	}
	$instance=$_GET['instance'];
	$_SESSION['instance']=$instance;
	// reset session later
	$session_must_be_reset=true;
}
if(!isset($_SESSION['instance'])) {
	$_SESSION['instance']="default";
}
$instance = $_SESSION['instance'];


include_once("./language.inc.php");

include "default/config.inc.php";

// Establish a database connection.
// On connection error, the message will be output without a proper HTML
// header. There is no way I can see around this; if track_errors isn't on
// there seems to be no way to supress the automatic error message output and
// still be able to access the error text.
if (empty($db_nopersist))
	$db_c = mysql_pconnect($db_host, $db_login, $db_password);
else
	$db_c = mysql_connect($db_host, $db_login, $db_password);

if (!$db_c || !mysql_select_db ($db_database)){
	echo "\n<p>\n", _("FATAL ERROR: Couldn't connect to database."), "\n";
	exit;
}

#lang.inc is included in config.inc.php
#also, all changeing code for language selection is at config.inc.php
#sometimes, script include other stand-alone scripts -> include_once
include_once "functions.inc.php";
include_once "mrbs_auth.inc.php";

if (isset($session_must_be_reset))
	reset_session();


/*
	## LOGIN ##
	If not logged in, redirect to login.php
*/

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
		header('Location: login.php');
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