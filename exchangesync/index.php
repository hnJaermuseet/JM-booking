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

require_once dirname(__FILE__).'/../libs/ExchangePHP/ExchangePHP.php';

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
		$alertAdmin = true;
		$alerts[]   = array('getCalendarItems exception: '.$e->getMessage());
		continue;
	}

	$cal_ids = array(); // Id => ChangeKey
	if(is_null($calendaritems))
	{
		printout('getCalendarItems failed: '.$cal->getError());
		$alertAdmin = true;
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
	foreach($entries as $entry) // Running through items in database
	{
		// Checking for previous sync
		if(isset($sync[$entry['entry_id']]))
		{
			// Okey, this entry has been synced to this user before
			
			// Check if it is deleted in Exchange
			$this_sync = $sync[$entry['entry_id']];
			if(!isset($cal_ids[$this_sync['exchange_id']])) // $cal_ids: exchange_id = exchange_changeid
			{
				// So, the user or somebody has deleted the element in Exchange
				printout('Err! Calendar element is deleted in Exchange! Alerting user and creates a new one.');
				$create_new  = true;
				$delete      = false;
				
				// Deleting from sync
				mysql_query("DELETE FROM `entry_exchangesync`
					WHERE
						`exchange_id` = '".$this_sync['exchange_id']."'
					");
				printout_mysqlerror ();
				
				emailSend($user_id,
					'Slettet avtale i kalender',
					
					'Hei'.chr(10).chr(10).
					
					'Det er blitt oppdaget at du har slettet en booking som '.
					'var overført til kalenderen din.'.chr(10).chr(10).
					
					'Bookingnavn: '.$entry['entry_name'].chr(10).
					'Bookingid: '.$entry['entry_id'].chr(10).
					'Starter: '.date('H:i d.m.Y', $entry['time_start']).chr(10).chr(10).
					
					'Det er opprettet ny avtale i kalenderen din med oppdatert informasjon fra bookingsystemet.'.chr(10).
					'Hvis du ønsker å slette en booking eller gjøre endringer på den, så må dette gjøres i '.chr(10).
					'bookingsystemet og kan ikke gjøres i kalenderen.'.chr(10).chr(10).
					
					'Mvh. Bookingsystemet');
				
				$alert_admin = true;
				$alerts[]    = 'User '.$user_id.' has deleted a calendar item.';
			}
			else
			{
				// Check if it is changed in Exchange
				if($cal_ids[$this_sync['exchange_id']] != $this_sync['exchange_changekey'])
				{
					// So, the user or something has changed the element in Exchange
					printout('Err! Calendar element is changed in Exchange! Alerting user and creates a new one.');
					$create_new  = true;
					$delete      = false;
					
					// Deleting from sync
					mysql_query("DELETE FROM `entry_exchangesync`
						WHERE
							`exchange_id` = '".$this_sync['exchange_id']."'
						");
					printout_mysqlerror ();
					
					emailSend($user_id,
					'Endret avtale i kalender',
					
					'Hei'.chr(10).chr(10).
					
					'Det er blitt oppdaget at du har endret en booking som '.
					'var overført til kalenderen din.'.chr(10).chr(10).
					
					'Bookingnavn: '.$entry['entry_name'].chr(10).
					'Bookingid: '.$entry['entry_id'].chr(10).
					'Starter: '.date('H:i d.m.Y', $entry['time_start']).chr(10).chr(10).
					
					'Det er opprettet ny avtale i kalenderen din med oppdatert informasjon fra bookingsystemet.'.chr(10).
					'Hvis du ønsker å gjøre endringer på den, så må dette gjøres i bookingsystemet og kan'.chr(10).
					'ikke gjøres i kalenderen.'.chr(10).chr(10).
					
					'Mvh. Bookingsystemet');
					
					$alert_admin = true;
					$alerts[]    = 'User '.$user_id.' has edited a calendar item.';
				}
				else
				{
					// Okey, no changes in Exchange
					
					// Is there anything changed in JM-booking?
					// TODO: Is it possible to only check relevant fields?
					if($this_sync['entry_rev'] < $entry['rev_num'])
					{
						// Create a new calendar element and delete the old one
						// (no updates)
						$create_new  = true;
						$delete      = true;
						$delete_id   = $this_sync['exchange_id'];
					}
					else
					{
						// No changes anywhere, do nothing
						$create_new  = false;
						$delete      = false;
					}
				}
			}
		}
		else
		{
			// Never synced before, create a new one
			$create_new  = true;
			$delete      = false;
		}
		
		if($create_new)
		{
			$entry = getEntry($entry['entry_id']); // Need more information
			templateAssignEntry('entryObj', $entry);
			
			// Get the name of the room
			$rooms = array();
			if(!count($entry['room_id']))
				$rooms[] = _('Whole area');
			else
			{
				// Single room
				foreach ($entry['room_id'] as $rid)
				{
					if ($rid == '0')
						$rooms[] = _('Whole area');
					elseif(is_numeric($rid))
					{
						$rooms[] = $room[(int)$rid]['room_name'];
					}
				}
			}
			$rooms = trim(implode(', ', $rooms));
			if($rooms != '')
				$rooms .= ' ('.$area[$entry['area_id']].')';
			else
				$rooms = $area[$entry['area_id']];
			
			$description = 
				'Visning av hele bookingen:'.chr(10).
				$systemurl.'/entry.php?entry_id='.$entryObj->entry_id.chr(10).
				utf8_encode('BID: '. $entryObj->entry_id).chr(10).
				utf8_encode('Type: '. $entryObj->entry_type).chr(10).
				utf8_encode('Kunde: '. $entryObj->customer_name).chr(10).
				utf8_encode('Vert(er): '. $entryObj->user_assigned_names).chr(10).
				utf8_encode('Antall voksne: '. $entryObj->num_person_adult).chr(10).
				utf8_encode('Antall barn: '. $entryObj->num_person_child).chr(10);
				
			if($entryObj->program_id_name != '')
				$description .= utf8_encode('Fast program: '. $entryObj->program_id_name).chr(10);
			
			$description .= chr(10).
				utf8_encode('Programbeskrivelse:'.chr(10).
					$entryObj->program_description);
			
			// Add the entry to list of items
			$i = $cal->createCalendarItems_addItem(
				utf8_encode($entryObj->entry_name), 
				$description,
				date('c', $entryObj->time_start), 
				date('c', $entryObj->time_end),
					array(
						'ReminderIsSet' => false,
						'Location' => utf8_encode($rooms),
					),
				$user['user_ews_sync_email']
				);
			$entries_new[$i] = $entry['entry_id'];
		}
		if($delete)
		{
			$entries_delete[$delete_id] = $entry['entry_id'];
		}
		
		unset($sync[$entry['entry_id']]);
	}

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
		try
		{
			$created_items = $cal->createCalendarItems();
		}
		catch (Exception $e)
		{
			printout('Exception - createCalendarItems: '.$e->getMessage().'<br />');
			$created_items = array();
			$alert_admin   = true;
			$alerts[]      = 'createCalendarItems exception: '.$e->getMessage();
		}
		
		foreach($created_items as $i => $ids)
		{
			if(!is_null($ids['Id'])) // Null = unsuccessful
			{
				$entry = $entries[$entries_new[$i]];
				printout($entries_new[$i].' created.');
				// Inserting in sync
				mysql_query("INSERT INTO `entry_exchangesync` (
					`entry_id` ,
					`exchange_id` ,
					`exchange_changekey`,
					`user_id`,
					`entry_rev`,
					`sync_until`
				)
				VALUES (
					'".$entry['entry_id']."' , 
					'".$ids['Id']."', 
					'".$ids['ChangeKey']."',
					'".$user_id."',
					'".$entry['rev_num']."',
					'".$entry['time_end']."'
				);");
				printout_mysqlerror ();
			}
			else
			{
				if($ids['ResponseMessage']->ResponseCode == 'ErrorCreateItemAccessDenied')
				{
					// Alert admin, alert user and disable sync
					emailSend($user_id,
					'Ikke tilgang til kalender',
					
					'Hei'.chr(10).chr(10).
					
					'Det er blitt satt opp at jeg skulle synkronisere bookinger du er satt opp på '.
					'inn i kalenderen din i Outlook. Jeg får det ikke til fordi du ikke har gitt meg tilgang.'.chr(10).chr(10).
					
					'Gå inn på denne adressen for å lese hvordan du kan fikse dette:'.chr(10)
					$systemurl.'/sync.html'.chr(10).chr(10).
					
					'Jeg har slått av synkroniseringen av din bruker. Det må bli slått på igjen når det er fikset.'.chr(10).chr(10).
					
					'Mvh. Bookingsystemet');
					
					mysql_query("UPDATE `users` SET `user_ews_sync` = '0' WHERE `user_id` =".$user_id);
					
					printout($entries_new[$i] .' not created. User '.$user_id.' has access denied error when creating items. Has disabled the sync of this user. Message from Exchange: '.$ids['ResponseMessage']->MessageText;
					$alert_admin = true;
					$alerts[]    = $entries_new[$i] .' not created. User '.$user_id.' has access denied error when creating items. Has disabled the sync of this user. Message from Exchange: '.$ids['ResponseMessage']->MessageText;
				}
				else
				{
					// Unknown error, alert admin
					printout($entries_new[$i] .' not created: '.print_r($ids['ResponseMessage'], true));
					$alert_admin = true;
					$alerts[]    = $entries_new[$i] .' not created. Message from Exchange: '.$ids['ResponseMessage']->MessageText;
				}
			}
		}
	}


	/**********************
	 * ## DELETE ITEMS ## *
	 **********************/
	$deleted_items = array();
	foreach($entries_delete as $delete_id => $entry_id)
	{
		try
		{
			//$sync[$entry['entry_id']]['exchange_id']
			$deleted_item = $cal->deleteItem($delete_id);
			$deleted_items[$entry_id] = $deleted_item;
			printout($entry_id.' deleted');
			
			// Deleting from sync
			mysql_query("DELETE FROM `entry_exchangesync`
				WHERE
					`exchange_id` = '".$delete_id."'
				");
			printout_mysqlerror ();
		}
		catch (Exception $e)
		{
			printout('Exception - deleteItem - '.$entry_id.': '.$e->getMessage());
			$alert_admin = true;
			$alerts[]    = 'deleteItem exception';
		}
	}
}

if($alert_admin)
	alertAdmin($alerts);

function alertAdmin($alerts = array())
{
	emailSendAdmin ('Problems in exchangesync', 
		'Please see log around '. date('H:i d-m-Y').chr(10).
		'Alerts:'.chr(10).implode(chr(10), $alerts));
}