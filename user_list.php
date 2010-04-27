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

print_header($day, $month, $year, $area);

$Q_users = mysql_query("select user_id from `users` order by user_name");
echo '<h1>'._('Users').'</h1>'.chr(10);

if(!isset($_GET['gid']))
{
	$Q_groups = mysql_query("select * from `groups` order by `group_name`");
	echo '<b>Brukergrupper</b><br />';
	echo '<ul>'.chr(10);
	while($R = mysql_fetch_assoc($Q_groups))
		echo '<li><a href="'.$_SERVER['PHP_SELF'].'?gid='.$R['group_id'].'">'.$R['group_name'].'</a></li>';
	echo '</ul>'.chr(10);
	
	echo '<b>Alle brukere</b><br />';
	echo '<table class="prettytable">'.chr(10);
	echo '	<tr>'.chr(10);
	echo '		<th>Navn</th>'.chr(10);
	echo '		<th>Initialer</th>'.chr(10);
	echo '		<th>Anlegg</th>'.chr(10);
	echo '	</tr>'.chr(10).chr(10);
	while($R_user = mysql_fetch_assoc($Q_users))
	{
		$user = getUser($R_user['user_id'], true);
		echo '	<tr>'.chr(10);
		
		echo '		<td>'.
				'<a href="user.php?user_id='.$user['user_id'].'">'.
				iconHTML('user').' '.
				$user['user_name'].'</a>'.
			'</td>'.chr(10);
		
		echo '		<td>'.
				$user['user_name_short'].
			'</td>'.chr(10);
		
		echo '		<td>';
			$area_user = getArea($user['user_area_default']);
			if(!count($area_user))
				$area_user['area_name'] = ''; 
			echo $area_user['area_name'];
		echo '</td>'.chr(10);
		
		echo '	</tr>'.chr(10).chr(10);
	}
	echo '</table>'.chr(10);

}
else
{
	$group = getGroup($_GET['gid']);
	if(count($group))
	{
		echo '<b>Viser brukergruppen '.$group['group_name'].'</b><br />'.chr(10);
		echo '<a href="telefonliste.php?gid='.$group['group_id'].'">Vis som telefonliste</a><br />';
		if(!count($group['users']))
			echo '<i>Ingen brukere på denne listen</i>';
		else
		{
			echo '<table class="prettytable">';
			echo '	<tr>'.chr(10);
			echo '		<th>Navn</th>'.chr(10);
			echo '		<th>Telefon</th>'.chr(10);
			echo '		<th>Stilling</th>'.chr(10);
			echo '	</tr>'.chr(10);
			foreach($group['users'] as $user)
			{
				$user = getUser($user);
				if(count($user))
				{
					echo '	<tr>'.chr(10);
					echo '		<td>'.$user['user_name'].'</td>'.chr(10);
					echo '		<td>'.$user['user_phone'].'</td>'.chr(10);
					echo '		<td>'.$user['user_position'].'</td>'.chr(10);
					echo '	</tr>'.chr(10);
				}
			}
			echo '</table>'.chr(10);
		}
	}
}
?>