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

include_once('glob_inc.inc.php');


// Check database tables
$q = db()->prepare('SHOW COLUMNS FROM entry');
$q->execute();
$table_orig = array();
while($r = $q->fetch(PDO::FETCH_ASSOC))
{
	if($r['Field'] == 'entry_id')
	{
		$r['Extra'] = str_replace('auto_increment', '', $r['Extra']); // Only difference in the two tables
	}
	$table_orig[$r['Field']] = $r;
}
$q = db()->prepare('SHOW COLUMNS FROM entry_deleted');
$q->execute();
$table_deleted = array();
while($r = $q->fetch(PDO::FETCH_ASSOC))
{
	$table_deleted[$r['Field']] = $r;
}

$error = array();
foreach($table_orig as $key => $r)
{
	if(!isset($table_deleted[$key])) {
        $error[] = 'Table entry_deleted missing field ' . $key;
    }
	elseif(implode($r) != implode($table_deleted[$key])) {
        $error[] = 'Field ' . $key . ' is not the same in the two tables.<br>'.chr(10) .  implode('<br>'.chr(10), $table_deleted[$key]) .' ----------<br>' . chr(10). implode('<br>'.chr(10), $r);
    }
	
	unset($table_deleted[$key]);
}

if(count($table_deleted))
{
	foreach($table_deleted as $key => $r)
	{
		$error[] = 'Table entry missing field '.$key.' (or its not removed from entry_deleted)';
	}
}

if(count($error))
{
	emailSendAdmin ('Problems in entry_delete', 
			'The two entry tables "entry" and "entry_delete" are not the same.'.chr(10).
			'Following differences where found:'.chr(10).
			chr(10).' - '.implode(chr(10).' - ', $error).chr(10));
	echo 'Et problem oppsto. Utvikleren av systemet har glemt � fikse en ting. Si fra til systemansvarlig for bookingsystemet.<br /><br />';
	echo 'Sletting vil ikke v�re tilgjengelig f�r dette er fikset.';


    var_dump($error);
	exit;
}


// Checking what we are doing
$action_delete = !(isset($_GET['undelete']) && $_GET['undelete'] == '1');

if(!isset($_GET['entry_id'])) {
    $entry = array();
}
elseif($action_delete) {
    $entry = getEntry($_GET['entry_id']);
}
else {
    $entry = getEntryDeleted($_GET['entry_id']);
}

if(!count($entry))
{
	if($action_delete)
	{
		echo 'Finner ikke bookingen du vil slette. Muligens den allerede er slettet.<br /><br />';
		echo '- <a href="entry.php?entry_id='.((int)$_GET['entry_id']).'">Tilbake</a>';
		exit;
	}
	else
	{
		echo 'Finner ikke bookingen du vil gjenopplive.<br /><br />';
		echo '- <a href="entry.php?entry_id='.((int)$_GET['entry_id']).'">Tilbake</a>';
		exit;
	}
}

// Please confirm
if(!isset($_GET['confirmed']) || $_GET['confirmed'] != 1)
{
	print_header($day, $month, $year, $area);
	
	echo '<h1>Er du sikker du vil ';
	if($action_delete)
	{
		$additions = '';
		echo 'slette bookingen';
	}
	else
	{
		$additions = '&amp;undelete=1';
		echo 'gjenopprette bookingen';
	}
	echo '?</h1>';
	
	echo 
		'<p style="font-size: 1.4em; margin: 10px;">'.
			'<a href="entry_delete.php?entry_id='.$entry['entry_id'].$additions.'&amp;confirmed=1&amp;alert=1">'.
				iconHTML('tick').' Ja, og varsle de som satt opp som verter'.
			'</a>'.
		'</p>'.
		'<p style="font-size: 1.4em; margin: 10px;">'.
			'<a href="entry_delete.php?entry_id='.$entry['entry_id'].$additions.'&amp;confirmed=1">'.
				iconHTML('tick').' Ja, men ikke varsle de som satt opp som verter'.
			'</a>'.
		'</p>'.
		'<p style="font-size: 1.4em; margin: 10px;">'.
			'<a href="entry.php?entry_id='.$entry['entry_id'].'">'.
				iconHTML('cross').' Nei'.
			'</a>'.
		'</p>'
		;
	exit;
}


// Copying data
if($action_delete)
{
	// Delete
	$from_table  = 'entry';
	$to_table    = 'entry_deleted';
	$log_action2 = 'entry_deleted';
}
else
{
	// Undelete
	$from_table  = 'entry_deleted';
	$to_table    = 'entry';
	$log_action2 = 'entry_undeleted';
}

$q = db()->prepare('insert into '.$to_table.' (select * from '.$from_table.' where `entry_id` = :entry_id limit 1)');
$q->bindValue(':entry_id', $entry['entry_id'], PDO::PARAM_INT);
if(!$q->execute())
{
	emailSendAdmin ('Problems in entry_delete', 
			'Mysql error on insert query:'.chr(10).
            implode(', ', $q->errorInfo()).chr(10));
	
	echo 'En feil oppsto. Feilmelding er sendt til systemadministrator.<br />';
	echo 'Sletting vil ikke v�re tilgjengelig f�r dette er fikset.';
	exit;
}

// Building log_data
$log_data = $entry;
$log_data['room_id']        = splittalize($log_data['room_id']);
$log_data['user_assigned']  = splittalize($log_data['user_assigned']);
unset($log_data['contact_person_email2']);
unset($log_data['mva']);
unset($log_data['mva_grunnlag']);
unset($log_data['mva_grunnlag_sum']);
unset($log_data['faktura_belop_sum']);
unset($log_data['faktura_belop_sum_mva']);
unset($log_data['eks_mva_tot']);
unset($log_data['grunnlag_mva_tot']);
unset($log_data['mva_vis']);
unset($log_data['customer_name']);
unset($log_data['time_day']);
unset($log_data['time_month']);
unset($log_data['time_year']);
unset($log_data['time_hour']);
unset($log_data['time_min']);
unset($log_data['invoice_locked']);
unset($log_data['entry_id']);
newEntryLog($entry['entry_id'], 'edit', $log_action2, $entry['rev_num']+1, $log_data);


// Verify that it is copied
$q = db()->prepare('select entry_id from '.$to_table.' where `entry_id` = :entry_id limit 1');
$q->bindValue(':entry_id', $entry['entry_id'], PDO::PARAM_INT);
if(!$q->execute())
{
	emailSendAdmin ('Problems in entry_delete', 
			'Mysql error on verify query:'.chr(10).
            implode(', ', $q->errorInfo()).chr(10));
	
	echo 'En feil oppsto. Feilmelding er sendt til systemadministrator.<br />';
	echo 'Sletting vil ikke v�re tilgjengelig f�r dette er fikset.';
	exit;
}

if($q->rowCount() <= 0)
{
	emailSendAdmin ('Problems in entry_delete', 
			'Entry id '.$entry['entry_id'].' is not in '.$to_table.' after copying.'.chr(10));
	
	echo 'En feil oppsto. Feilmelding er sendt til systemadministrator.<br />';
	echo 'Sletting vil ikke v�re tilgjengelig f�r dette er fikset.';
	exit;
}

// Deleting
$q_delete = db()->prepare('delete from '.$from_table.' where `entry_id` = :entry_id limit 1');
$q_delete->bindValue(':entry_id', $entry['entry_id'], PDO::PARAM_INT);
if(!$q_delete->execute())
{
	emailSendAdmin ('Problems in entry_delete', 
			'Mysql error on delete query:'.chr(10).
			implode(', ', $q_delete->errorInfo()).chr(10));
	
	echo 'En feil oppsto. Feilmelding er sendt til systemadministrator.<br />';
	echo 'Sletting vil ikke v�re tilgjengelig f�r dette er fikset.';
	exit;
}

// Alerting
if(isset($_GET['alert']) && $_GET['alert'] == '1')
{
	foreach ($entry['user_assigned'] as $user_id)
	{
		if($user_id != $login['user_id'])
		{
			if($action_delete) {
                emailSendEntryDeleted($entry, $user_id);
            }
			else {
                emailSendEntryUndeleted($entry, $user_id);
            }
		}
	}
}

header('Location: entry.php?entry_id='.$entry['entry_id']);