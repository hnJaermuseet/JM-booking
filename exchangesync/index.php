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
Hallvard Nygård <hn@jaermuseet> for Jærmuseet, 
a Norwegian science center.

Jærmuseet
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

try {
	// Checking config
	if(!isset($exchangesync_login))
		throw new Exception('Missing in config: $exchangesync_login not set. Please put this in site config (see default.config.php for example)');
	if(
		!isset($exchangesync_login['username']) ||
		!isset($exchangesync_login['password'])
	)
		throw new Exception('Config failed: $exchangesync_login is not correct. Please correct this in site config (see default.config.php for example)');
	
	
	// Setting up connection to Exchange
	$wsdl = dirname(__FILE__).'/Services.wsdl';
	$client = new NTLMSoapClient($wsdl, array(
			'login'       => $exchangesync_login['username'], 
			'password'    => $exchangesync_login['password'],
			'trace'       => true,
			'exceptions'  => true,
		)); 
	$cal = new ExchangePHP($client);
	
	// Getting areas and rooms
	$area = exchangesync_getAllAreas();
	$room = exchangesync_getAllRooms();
	
	// Fake template object
	$entryObj = new exchangesync_EntryTemplate();
	
	// Building user array
	$users = exchangesync_getAllUsersWithEWSSync();
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
		checkMysqlErrorAndThrowException(__LINE__);
		while($R_entry = mysql_fetch_assoc($Q_next_entries))
		{
			$entries[$R_entry['entry_id']]             = $R_entry;
		}
		
		// Getting sync-data
		$sync = array();
		$Q = mysql_query("select * from `entry_exchangesync` 
			WHERE
				`user_id` = '".$user_id."' AND
				`sysnc_until` >= '".$sync_from."'");
		checkMysqlErrorAndThrowException(__LINE__);
		while($R_sync = mysql_fetch_assoc($Q))
		{
			$sync[$R_sync['entry_id']] = $R_sync;
		}
		
		// Analysing which to create, which to delete and which not to touch
		$entries_new     = array();
		$entries_delete  = array();
		exchangesync_analyzeSync ($entries, $cal_ids, $cal, $user, $user_id);

		// Delete any entries removed from this user that is already synced
		foreach($sync as $entry_id => $R_sync)
		{
			// => Delete
			$entries_delete[$R_sync['exchange_id']] = $entry_id;
		}
		
		// Create items
		if(!count($entries_new))
			printout('No items to be created in Exchange');
		else
		{
			exchangesync_createItems ($entries, $cal, $entries_new, $user_id);
		}
		
		// Delete items
		$deleted_items = exchangesync_deleteItems($entries_delete, $cal);
	}
}
catch (Exception $e)
{
	printout('Exception: '.$e->getMessage());
	$alert_admin = true;
	$alerts[] = 'Exception: '.$e->getMessage();
}

function checkMysqlErrorAndThrowException($line)
{
	if(mysql_error())
		throw new Exception('MySQL error just above line '.$line.': '.mysql_error());
}

if($alert_admin)
	alertAdmin($alerts);

function alertAdmin($alerts = array())
{
	emailSendAdmin ('Problems in exchangesync', 
		'Please see log around '. date('H:i d-m-Y').chr(10).
		'Alerts:'.chr(10).implode(chr(10), $alerts));
}