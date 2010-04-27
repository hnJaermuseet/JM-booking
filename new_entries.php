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
	List of the newest entries
	- Red entries has not been displayed
	- Started 15.02.2008
*/

include_once("glob_inc.inc.php");

print_header($day, $month, $year, $area);

echo '<h1>'._('50 newest entries').'</h1>'.chr(10).chr(10);

echo '<b>'._('Colour explaination').'</b><br>'.chr(10);
echo '<font color="black">'._('Entry has not changes since last view').'</font><br>'.chr(10);
echo '<font color="red">'._('New entry').'</font><br>'.chr(10);
echo '<font color="blue">'._('Changed entry').'</font><br>'.chr(10);
echo '<br>'.chr(10);

$Q_entries = mysql_query("select entry_id, entry_name, time_last_edit, user_last_edit, rev_num from `entry` order by `time_last_edit` desc limit 50");

echo '<table>'.chr(10);
echo ' <tr>'.chr(10);
echo '  <td><b>'._('Entry id').'</b></td>'.chr(10);
echo '  <td><b>'._('Entry name').'</b></td>'.chr(10);
echo '  <td><b>'._('Last change').'</b></td>'.chr(10);
echo '  <td><b>'._('Changed by').'</b></td>'.chr(10);
echo '  <td><b>'._('Last action').'</b></td>'.chr(10);
echo ' </tr>'.chr(10);

while ($R_entry = mysql_fetch_assoc($Q_entries))
{
	// Getting last change
	$Q_log = mysql_query("select log_id from `entry_log` where entry_id = '".$R_entry['entry_id']."' order by `rev_num` desc limit 1");
	if(mysql_num_rows($Q_log))
		$log_id = mysql_result($Q_log, 0, 'log_id');
	else
		$log_id = 0;
	$log = getEntryLog($log_id);
	
	$user = getUser($R_entry['user_last_edit']);
	if(!isset($user['user_name']))
		$user['user_name'] = _('Not found');
	
	// Seen all?
	if(isset($_GET['seen_all']))
		readEntry ($R_entry['entry_id'], $R_entry['rev_num']);
	
	// Checking if you have read the entry
	$Q_read = mysql_query("select read_id from `entry_read` where user_id = '".$login['user_id']."' and entry_id = '".$R_entry['entry_id']."' and rev_num = '".$R_entry['rev_num']."'");
	if(mysql_num_rows($Q_read))
	{
		$color = 'black';
		$color2 = 'white';
	}
	elseif (count($log) && $log['log_action'] == 'edit')
	{
		$color = 'white';
		$color2 = 'blue';
	}
	else
	{
		$color = 'white';
		$color2 = 'red';
	}
	
	echo ' <tr>'.chr(10);
	echo '  <td bgcolor="'.$color2.'"><center><a href="./entry.php?entry_id='.$R_entry['entry_id'].'">'.$R_entry['entry_id'].'</a></center></td>'.chr(10);
	echo '  <td bgcolor="'.$color2.'"><font color="'.$color.'">'.$R_entry['entry_name'].'</font></td>'.chr(10);
	echo '  <td bgcolor="'.$color2.'"><font color="'.$color.'">'.date('H:i:s d-m-Y', $R_entry['time_last_edit']).'</font></td>'.chr(10);
	echo '  <td bgcolor="'.$color2.'"><font color="'.$color.'">'.$user['user_name'].'</font></td>'.chr(10);
	
	if(count($log))
	{
		echo '  <td bgcolor="'.$color2.'"><font color="'.$color.'">';
		printEntryLog($log);
		echo '</font></td>'.chr(10);
	}
	else
	{
		echo '  <td bgcolor="'.$color2.'">'._('Log not found').'</td>'.chr(10);
	}
	echo ' </tr>'.chr(10);
}

echo '</table>';

echo '<br><br>'.chr(10);
echo '- <a href="new_entries.php?seen_all=1">'._('Mark all entries in this list as read').'</a><br>';

?>