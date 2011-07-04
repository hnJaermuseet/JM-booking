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
function alertAdmin($alerts = array())
{
	emailSendAdmin ('Problems in exchangesync', 
		'Please see log around '. date('H:i d-m-Y').chr(10).
		'Alerts:'.chr(10).implode(chr(10), $alerts));
}
function printout ($txt)
{
	global $user_id, $sync_from, $sync_to;
	if(isset($sync_from) && isset($sync_to))
		$sync = '['.date('Y-m-d', $sync_from).'-'.date('Y-m-d', $sync_to).'] ';
	else
		$sync = '';
	
	if(php_sapi_name() == 'cli') // Command line
	{
		echo date('Y-m-d H:i:s', time()).' [user '.$user_id.'] '.$sync.$txt."\r\n";
	}
	else
	{
		echo str_replace(' ', '&nbsp;', 
			date('Y-m-d H:i:s', time()).' [user '.$user_id.'] '.$sync.$txt).'<br />'.chr(10);
	}
}
function printout_mysqlerror ()
{
	if(mysql_error())
		printout(mysql_error());
}

function checkMysqlErrorAndThrowException($line, $file)
{
	if(mysql_error())
		throw new Exception('MySQL error in '.$file.' just above line '.$line.': '.mysql_error());
}

try
{
	set_time_limit(600);
	error_reporting(E_ALL);
	function error_handler($err_no, $err_str, $err_file, $err_line)
	{
		throw new Exception('Errorno '.$err_no.chr(10).$err_str.chr(10).'File: '.$err_file.':'.$err_line);
	}
	set_error_handler('error_handler');
	
	$require_login = false;
	$path_site_config = '../config/site.config.php';
	
	require_once dirname(__FILE__).'/../libs/ExchangePHP/ExchangePHP.php';
	require_once dirname(__FILE__).'/../functions/exchangesync.php';
	
	// MySQL and other stuff, using the same as the rest of the system
	require_once dirname(__FILE__).'/../glob_inc.inc.php';

	if(php_sapi_name() != 'cli' && $exchangesync_from_clionly)
	{
		echo 'Only accessable from command line.';
		alertAdmin(array($_SERVER['REMOTE_ADDR'].' tried to access exchangesync from web.'));
		exit;
	}

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
		if($user_id != 6 && $user_id != 48) // Disable all users except user with id 6 and 48
			continue;
		
		try
		{
			for($year = 2008; $year <= date('Y')-2; $year++) // TODO: fix not only date('Y')-2
			{
				// Period to sync
				$sync_from  = mktime(0,0,0,01, 01, $year);
				$sync_to    = mktime(0,0,0,date('m'), date('d')-50, $year+2); // Next 2 years
				//$sync_to2   = $sync_from+(708*60*60*24);
				$sync_to    = mktime(0,0,0,01, 01, $year+1)-1; // Next year
				$sync_to2   = $sync_to+(5*24*60*60); // + 5 days
				
				$sync_from2 = $sync_from-(5*24*60*60); // - 5 days
				
				// Getting all calendar elements from Exchange
				if(($sync_to2 - $sync_from) > (2*365*24*60*60)) // Not more than 2 years
					throw new Exception ('Exchange will not return more than 2 years. '.
						'Tried to retrive from '.
							date('d-m-Y H:i:s', $sync_from).
						' to '.
							date('d-m-Y H:i:s', $sync_to2));
					// TODO: put exception in exchangesync_getCalendarItems instead
				$cal_ids = exchangesync_getCalendarItems (
						$cal,
						date('Y-m-d', $sync_from2).'T00:00:00',
						date('Y-m-d', $sync_to2).'T00:00:00',
						$user['user_ews_sync_email']
					);
				printout('Number of items in Exchange (+/- 5 days): '.(count($cal_ids)));
				
				// Getting the users entries
				$entries = exchangesync_getUsersEntriesInPeriod ($user_id, $sync_from, $sync_to);
				printout('Number of entries in period: '.(count($entries)));
				
				// Getting sync-data for the user
				$sync = exchangesync_getUsersSyncdata ($user_id, $sync_from, $sync_to);
				printout('Number of entries already synced in period: '.(count($sync)));
				
				// Analysing which to sync and which not to touch
				$entries_sync = array();
				exchangesync_analyzeSync ($entries, $cal_ids, $cal, $user, $user_id);
				
				// Sync any entries removed from this user that has been synced earlier
				foreach($sync as $entry_id => $R_sync)
				{
					// => Sync
					$entries_sync[$entry_id] = getEntry($entry_id);
				}
				
				// Sync items
				printout(count($entries_sync).' items to be synced to Exchange');
				if(count($entries_sync))
				{
					exchangesync_syncItems ($cal, $user, $user_id, $entries_sync);
				}
			}
		}
		catch (Exception $e)
		{
			printout('Exception: '.$e->getMessage());
			$alert_admin = true;
			$alerts[] = 'Exception: '.$e->getMessage();
		}
	}
}
catch (Exception $e)
{
	printout('Exception: '.$e->getMessage());
	$alert_admin = true;
	$alerts[] = 'Exception: '.$e->getMessage();
}

if($alert_admin)
	alertAdmin($alerts);