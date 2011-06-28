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


function exchangesync_getCalendarItems($cal, $from, $to, $user_ews_sync_mail)
{
		global $alert_admin, $alerts;
		global $user_id;
		
		try
		{
			$calendaritems = $cal->getCalendarItems(
				$from,
				$to,
				$user_ews_sync_mail
			);
		}
		catch (Exception $e)
		{
			if($cal->client->getError() == '401')
			{
				// Unauthorized
				throw new Exception('Exchange said: Wrong username and password. Exception message: '.$e->getMessage());
			}
			else
			{
				throw new Exception ('getCalendarItems exception: '.$e->getMessage());
			}
		}
		
		$cal_ids = array(); // Id => ChangeKey
		if(is_null($calendaritems))
		{
			throw new Exception('getCalendarItems failed: '.$cal->getError());
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
		return $cal_ids; // Exchange id => Exchange change key
}

function exchangesync_getUsersEntriesInPeriod($user_id, $sync_from, $sync_to)
{
	$Q_next_entries = mysql_query("select entry_id, time_start, time_end, rev_num, entry_name
		from `entry` where 
		(`user_assigned` LIKE '%;".$user_id.";%') AND 
		(`time_end` >= '".$sync_from."') AND 
		(`time_end` <  '".$sync_to."')");
	$entries = array();
	checkMysqlErrorAndThrowException(__LINE__, __FILE__);
	while($R_entry = mysql_fetch_assoc($Q_next_entries))
	{
		$entries[$R_entry['entry_id']]             = $R_entry;
	}
	return $entries;
}

function exchangesync_getUsersSyncdata ($user_id, $sync_from)
{
	$sync = array();
	$Q = mysql_query("select * from `entry_exchangesync` 
		WHERE
			`user_id` = '".$user_id."' AND
			`sync_until` >= '".$sync_from."'");
	checkMysqlErrorAndThrowException(__LINE__, __FILE__);
	while($R_sync = mysql_fetch_assoc($Q))
	{
		$sync[$R_sync['entry_id']] = $R_sync;
	}
	return $sync;
}

function exchangesync_analyzeSync ($entries, $cal_ids, $cal, $user, $user_id)
{
	global $alert_admin, $alerts;
	global $systemurl;
	global $sync, $entryObj, $entries_new, $entries_delete;
	
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
						exchangesync_getUsermsgDeleted ($entry)
					);
				
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
						exchangesync_getUsermsgChanged($entry)
					);
					
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
			
			$rooms = $entryObj->room.' ('.$area[$entry['area_id']].')';
			
			// Add the entry to list of items
			$i = $cal->createCalendarItems_addItem(
				utf8_encode($entryObj->entry_name), 
				exchangesync_getEntryCalendarDescription ($systemurl, $entryObj),
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
}

function exchangesync_getEntryCalendarDescription ($systemurl, $entryObj)
{
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
	
	return $description;
}
function exchangesync_getUsermsgDeleted ($entry)
{
	return
		'Hei'.chr(10).chr(10).
		
		'Det er blitt oppdaget at du har slettet en booking som '.
		'var overført til kalenderen din.'.chr(10).chr(10).
		
		'Bookingnavn: '.$entry['entry_name'].chr(10).
		'Bookingid: '.$entry['entry_id'].chr(10).
		'Starter: '.date('H:i d.m.Y', $entry['time_start']).chr(10).chr(10).
		
		'Det er opprettet ny avtale i kalenderen din med oppdatert informasjon fra bookingsystemet.'.chr(10).
		'Hvis du ønsker å slette en booking eller gjøre endringer på den, så må dette gjøres i '.chr(10).
		'bookingsystemet og kan ikke gjøres i kalenderen.'.chr(10).chr(10).
		
		'Mvh. Bookingsystemet';
}
function exchangesync_getUsermsgChanged ($entry)
{
	return
		'Hei'.chr(10).chr(10).
		
		'Det er blitt oppdaget at du har endret en booking som '.
		'var overført til kalenderen din.'.chr(10).chr(10).
		
		'Bookingnavn: '.$entry['entry_name'].chr(10).
		'Bookingid: '.$entry['entry_id'].chr(10).
		'Starter: '.date('H:i d.m.Y', $entry['time_start']).chr(10).chr(10).
		
		'Det er opprettet ny avtale i kalenderen din med oppdatert informasjon fra bookingsystemet.'.chr(10).
		'Hvis du ønsker å gjøre endringer på den, så må dette gjøres i bookingsystemet og kan'.chr(10).
		'ikke gjøres i kalenderen.'.chr(10).chr(10).
		
		'Mvh. Bookingsystemet';
}
function exchangesync_getUsermsgAccessDenied($systemurl)
{
	return
		'Hei'.chr(10).chr(10).
		
		'Det er blitt satt opp at jeg skulle synkronisere bookinger du er satt opp på '.
		'inn i kalenderen din i Outlook. Jeg får det ikke til fordi du ikke har gitt meg tilgang.'.chr(10).chr(10).
		
		'Gå inn på denne adressen for å lese hvordan du kan fikse dette:'.chr(10).
		$systemurl.'/sync.html'.chr(10).chr(10).
		
		'Jeg har slått av synkroniseringen av din bruker. Det må bli slått på igjen når det er fikset.'.chr(10).chr(10).
		
		'Mvh. Bookingsystemet';
}


function exchangesync_createItems ($entries, $cal, $entries_new, $user_id)
{
	global $alert_admin, $alerts;
	global $systemurl;
	
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
				emailSend($user_id, 'Ikke tilgang til kalender', exchangesync_getUsermsgAccessDenied($systemurl));
				
				mysql_query("UPDATE `users` SET `user_ews_sync` = '0' WHERE `user_id` =".$user_id);
				
				printout($entries_new[$i] .' not created. User '.$user_id.' has access denied error when creating items. Has disabled the sync of this user. Message from Exchange: '.$ids['ResponseMessage']->MessageText);
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
/**
 * 
 * @param  array   exchangeid => entry_id
 * @param  ???
 */
function exchangesync_deleteItems($entries_delete, $cal)
{
	global $alert_admin, $alerts;
	
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
	
	return $deleted_items;
}

function exchangesync_getAllAreas()
{
	$Q_area = mysql_query("select id as area_id, area_name from mrbs_area");
	$area = array();
	while($R_area = mysql_fetch_assoc($Q_area))
	{
		$area[$R_area['area_id']] = $R_area['area_name'];
	}
	return $area;
}
function exchangesync_getAllRooms()
{
	$Q_room = mysql_query("select id as room_id, room_name from `mrbs_room` ");
	$room = array();
	while($R_room = mysql_fetch_assoc($Q_room))
	{
		$room[$R_room['room_id']] = $R_room['room_name'];
	}
	return $room;
}
function exchangesync_getAllUsersWithEWSSync()
{
	$users = array();
	$Q_users = mysql_query("select user_id from `users` where user_ews_sync = '1' and user_ews_sync_email != ''");
	while($R_users = mysql_fetch_assoc($Q_users))
	{
		$users[$R_users['user_id']] = getUser($R_users['user_id']);
	}
	return $users;
}

/**
 * Simulation of Smarty-object
 *
 */
class exchangesync_EntryTemplate 
{
	protected $data = array();
	public function __get($var) {
		return $this->data[$var];
	}
	public function assign($var, $value) {
		$this->data[$var] = $value;
	}
}