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

include_once("glob_inc.inc.php");


if(isset($_POST['user_id']) && is_numeric($_POST['user_id']))
	$user_id = (int)$_POST['user_id'];
elseif(isset($_GET['user_id']) && is_numeric($_GET['user_id']))
	$user_id = (int)$_GET['user_id'];
else
{
	echo _('Error: No user spesified.');
	exit();
}

$user = getUser ($user_id);
if (!count($user))
{
	echo _('Can\'t find user');
	exit();
}


print_header($day, $month, $year, $area);

echo '<h1>'._('Viewing user details').'</h1>'.chr(10).chr(10);

if($user['deactivated'])
	echo '<div class="error" style="width: 500px;">'._('This user is deactivated.').'</div>';

if($login['user_access_useredit'] || $login['user_id'] == $user['user_id'])
{
	echo '<a href="admin_user2.php?editor=1&amp;id='.$user['user_id'].'">'.
		iconHTML('user_edit').
		' Endre bruker</a><br />'.chr(10).chr(10);
	echo '<a href="admin_user_password.php?id='.$user['user_id'].'">'.
		iconHTML('lock_edit').
		' Endre passord</a><br /><br />'.chr(10).chr(10);
}

echo '<b>'._('UserID').':</b> '.$user['user_id'].'<br>'.chr(10);
echo '<b>'._('Username').':</b> '.$user['user_name'].'<br>'.chr(10);
echo '<b>'._('Short username').':</b> '.$user['user_name_short'].'<br>'.chr(10);
echo '<b>'._('E-mail').':</b> '.$user['user_email'].'<br>'.chr(10);
echo '<b>'._('Phone').':</b> '.$user['user_phone'].'<br>'.chr(10);
echo '<b>Stilling:</b> '.$user['user_position'].'<br>'.chr(10);

echo '<!-- ';
echo '<br><b>Adresse for internettkalender i Outlook:</b><br><input type="text" size="70" value="'.
	$systemurl.'/entry_ical.php?user_id='.$user['user_id'].'"><br>'.
	'Se <a href="'.wikiLink ('Bookingsystemet/Bookinger_i_Outlook').'">guide på wiki</a> for informasjon om hvordan du legger inn kalenderen'.chr(10);
echo '-->';
	
echo '<h2>'._('Upcoming entries for ').' '.$user['user_name'].'</h2>'.chr(10);
filterMakeAlternatives();
$filters = array();
$filters = addFilter($filters, 'user_assigned', $user['user_id']);
$filters = addFilter($filters, 'time_start', 'current', '>');
filterLink($filters);	echo '<br><br>'.chr(10).chr(10);
$SQL = genSQLFromFilters($filters, 'entry_id').' order by time_start';
$Q_next_entries = mysql_query($SQL);
/*
$Q_next_entries = mysql_query("select entry_id from `entry` where 
	user_assigned like '%;".$user['user_id'].";%' and
	time_start > '".time()."'
	order by time_start
	limit 50;");*/

if(!mysql_num_rows($Q_next_entries))
	echo '<i>'._('No upcoming entries found').'</i>'.chr(10);
else
{
	echo '<table style="border-collapse: collapse;">'.chr(10);
	echo ' <tr>'.chr(10);
	echo '  <td class="border"><b>'._('Starts').'</b></td>'.chr(10);
	echo '  <td class="border"><b>'._('Name').'</b></td>'.chr(10);
	echo '  <td class="border"><b>'._('Where').'</b></td>'.chr(10);
	echo ' </tr>'.chr(10);
	while($R_entry = mysql_fetch_assoc($Q_next_entries))
	{
		$entry = getEntry($R_entry['entry_id']);
		if(count($entry))
		{
			echo ' <tr>'.chr(10);
			echo '  <td class="border"><b>'.date('d-m-Y H:i', $entry['time_start']).'</b></td>'.chr(10);
			echo '  <td class="border"><a href="entry.php?entry_id='.$entry['entry_id'].'">'.$entry['entry_name'].'</a></td>'.chr(10);
			echo '  <td class="border">';
			$area = getArea($entry['area_id']);
			if(count($area))
				echo $area['area_name'].' - ';
			$rooms = array();
			foreach ($entry['room_id'] as $rid)
			{
				if($rid == '0')
					$rooms[] = _('Whole area');
				else
				{
					$room = getRoom($rid);
					if(count($room))
						$rooms[] = $room['room_name'];
				}
			}
			echo implode(', ', $rooms);
			echo '</td>'.chr(10);
			echo ' </tr>'.chr(10);
		}
	}
	echo '</table>'.chr(10);
}
?>