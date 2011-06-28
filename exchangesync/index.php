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

SYNCING/COPYING JMBOOKING TO EXCHANGE 2007
Hallvard Nyg�rd <hn@jaermuseet> for J�rmuseet, 
a Norwegian science center.

J�rmuseet
http://jaermuseet.no/

JM-booking, the project using the ExchangePHP
https://github.com/hnJaermuseet/JM-booking


How sync/copy works - One way sync (copy) over to Exchange calendar
- Gets all calendar elements in Exchange from now and 2 years forward
- Entries in JM-booking is connected to users in the database
- Gets all entries for a user that has not ended jet and 2 years forward*
- For each of these we check if they are already synced
	- If synced: are they changed
		- If changed: alert user (email) + create new
		- If not: check if they are changed in JM-booking
			- If yes: delete from exchange + create a new
			- If no: do nothing
	- If not synced: create
- Also check if the user is deleted from a entry

- The script has a nice output to make a clean log
- If an error has occured, email is sent to administrator with log

* Must be less timespan than the one we get from Exchange

In JM-booking we are using the following tables
- entry
- entry_exchangesync

Entry contains the entries and who is connected to the entry
Entry_exchangesync contains a table with
	- user_id, who we synced this to
	- entry_id, what we synced
	- entry_rev, what revision we synced
	- exchange_id, what id we got at the Exchange server
	- exchange_changekey, what changekey we got from Exhange

Also see demo in ExchangePHP repository:
https://github.com/hnJaermuseet/ExchangePHP



Database table:
CREATE TABLE `entry_exchangesync` (
	`user_id` INT NOT NULL ,
	`entry_id` INT NOT NULL ,
	`entry_rev` INT NOT NULL ,
	`sync_until` INT NOT NULL ,
	`exchange_id` VARCHAR( 255 ) NOT NULL ,
	`exchange_changekey` VARCHAR( 255 ) NOT NULL ,
	INDEX ( `user_id` )
) ENGINE = InnoDB 

*/

$alert_admin = false;
$require_login = false;
$path_site_config = '../config/site.config.php';

require_once dirname(__FILE__).'/../libs/ExchangePHP/ExchangePHP.php';
require_once dirname(__FILE__).'/../functions/exchangesync.php';

function printout ($txt)
{
	global $user_id;
	if(php_sapi_name() == 'cli') // Command line
	{
		echo date('Y-m-d H:i:s').' [user '.$user_id.'] '.$txt."\r\n";
	}
	else
	{
		echo str_replace(' ', '&nbsp;', 
			date('Y-m-d H:i:s').' [user '.$user_id.'] '.$txt).'<br />'.chr(10);
	}
}
function printout_mysqlerror ()
{
	if(mysql_error())
		printout(mysql_error());
}

// MySQL and other stuff, using the same as the rest of the system
require_once dirname(__FILE__).'/../glob_inc.inc.php';

if(php_sapi_name() != 'cli' && $exchangesync_from_clionly)
{
	echo 'Only accessable from command line.';
	alertAdmin(array($_SERVER['REMOTE_ADDR'].' tried to access exchangesync from web.'));
	exit;
}

// Exchange login, user must have access to the calendars its being told to edit
require dirname(__FILE__).'/password.php';

/* Syntax:
$login = array(
		'username' => '',
		'password' => '',
	);
*/

// Setting up connection to Exchange
$wsdl = dirname(__FILE__).'/Services.wsdl';
$client = new NTLMSoapClient($wsdl, array(
		'login'       => $login['username'], 
		'password'    => $login['password'],
		'trace'       => true,
		'exceptions'  => true,
	)); 
$cal = new ExchangePHP($client);

// Getting areas and rooms
$Q_area = mysql_query("select id as area_id, area_name from mrbs_area");
$area = array();
while($R_area = mysql_fetch_assoc($Q_area))
{
	$area[$R_area['area_id']] = $R_area['area_name'];
}
$Q_room = mysql_query("select id as room_id, room_name from `mrbs_room` ");
$room = array();
while($R_room = mysql_fetch_assoc($Q_room))
{
	$room[$R_room['room_id']] = $R_room['room_name'];
}

/**
 * Simulation of Smarty-object
 *
 */
class EntryTemplate 
{
	protected $data = array();
	public function __get($var) {
		return $this->data[$var];
	}
	public function assign($var, $value) {
		$this->data[$var] = $value;
	}
}
$entryObj = new EntryTemplate();

// Building user array
$users = array();
$Q_users = mysql_query("select user_id from `users` where user_ews_sync = '1' and user_ews_sync_email != ''");
while($R_users = mysql_fetch_assoc($Q_users))
{
	$users[$R_users['user_id']] = getUser($R_users['user_id']);
}

if(!count($users))
{
	printout('No users has enabled Exchange sync and has a sync email');
}

foreach($users as $user_id => $user)
{
	// Getting all calendar elements from Exchange
	try
	{
		$calendaritems = $cal->getCalendarItems(
			date('Y-m-d').'T00:00:00', // Today
			date('Y-m-d',time()+61171200).'T00:00:00', // Approx 2 years, seems to be a limit
			$user['user_ews_sync_email']
		);
	}
	catch (Exception $e)
	{
		printout('Exception - getCalendarItems: '.$e->getMessage());
		
		if($cal->client->getError() == '401')
		{
			// Unauthorized
			printout('Exchange said: Wrong username and password.');
		}
		else
		{
			printout('getCalendarItems exception: '.$e->getMessage());
		}
		$alert_admin = true;
		$alerts[]   = 'getCalendarItems exception: '.$e->getMessage();
		continue;
	}

	$cal_ids = array(); // Id => ChangeKey
	if(is_null($calendaritems))
	{
		printout('getCalendarItems failed: '.$cal->getError());
		$alert_admin = true;
		$alerts[]   = 'getCalendarItems failed: '.$cal->getError();
		continue;
	}
	else
	{
		// Going through existing elements
		foreach($calendaritems as $item) {
			if(!isset($item->Subject))
				$item->Subject = '';
			$cal_ids[$item->ItemId->Id] = $item->ItemId->ChangeKey;
			printout('Existing: '.$item->Start.'   '.$item->End.'   '.$item->Subject);
		}
	}

	// Getting entries for the user for the next 2 years
	$sync_from = mktime(0,0,0,date('m'), date('d'), date('Y'));
	$Q_next_entries = mysql_query("select entry_id, time_start, time_end, rev_num, entry_name
		from `entry` where 
		(`user_assigned` LIKE '%;".$user_id.";%') AND 
		(`time_end` >= '".$sync_from."') AND 
		(`time_end` <  '".mktime(0,0,0,date('m'), date('d')-50, date('Y')+2)."')");
	$entries = array();
	printout_mysqlerror();
	while($R_entry = mysql_fetch_assoc($Q_next_entries))
	{
		$entries[$R_entry['entry_id']]             = $R_entry;
	}

	// Getting sync-data
	$sync = array();
	$Q = mysql_query("select * from `entry_exchangesync` 
		WHERE
			`user_id` = '".$user_id."' AND
			`sync_until` >= '".$sync_from."'");
	printout_mysqlerror ();
	while($R_sync = mysql_fetch_assoc($Q))
	{
		$sync[$R_sync['entry_id']] = $R_sync;
	}


	// Analysing which to create
	$entries_new     = array();
	$entries_delete  = array();
	exchangesync_analyzeSync ($entries, $cal_ids, $cal, $user, $user_id);

	// Any entries removed from this user that is already synced
	foreach($sync as $entry_id => $R_sync)
	{
		// => Delete
		$entries_delete[$R_sync['exchange_id']] = $entry_id;
	}



	/**********************
	 * ## CREATE ITEMS ## *
	 **********************/
	if(!count($entries_new))
		printout('No items to be created in Exchange');
	else
	{
		exchangesync_createItems ($entries, $cal, $entries_new, $user_id);
	}


	/**********************
	 * ## DELETE ITEMS ## *
	 **********************/
	$deleted_items = exchangesync_deleteItems($entries_delete, $cal);
}

if($alert_admin)
	alertAdmin($alerts);

function alertAdmin($alerts = array())
{
	emailSendAdmin ('Problems in exchangesync', 
		'Please see log around '. date('H:i d-m-Y').chr(10).
		'Alerts:'.chr(10).implode(chr(10), $alerts));
}