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
	Administration of groups
*/

$section = 'groups';

include "include/admin_top.php";

if(isset($_GET['gid']) && is_numeric($_GET['gid'])) // Display of a singel one
{	
	$gid = $_GET['gid'];
	
	// Checking if it exists
	$Q_group = mysql_query("select * from `groups` where group_id = '$gid'");
	if(!mysql_num_rows($Q_group))
	{
		echo _("Group not found");
		exit();
	}
	
	$group_name = mysql_result($Q_group, 0, 'group_name');
	
	// Splitting the users
	$gusers = splittIDs(mysql_result($Q_group, 0, 'user_ids'));
	$gusers1 = array();
	$gusers2 = '';
	foreach ($gusers as $user_id)
	{
		$the_user = getUserinfo ($user_id);
		if(count($the_user))
		{
			if(!isset($gusers1[$user_id]))
			{
				$gusers1[$user_id] = $the_user['user_name'];
				$gusers2 .= ';'.$user_id.';';
			}
		}
	}
	
	if(isset($_POST['group_add_user']) && is_numeric($_POST['group_add_user']))
	{
		// Adding a user
		if(!$login['user_access_useredit'])
		{
			showAccessDenied($day, $month, $year, $area, true);
			exit ();
		}
		
		$user_id = $_POST['group_add_user'];
		if(checkUser($user_id))
		{
			if(!array_key_exists($user_id, $gusers1))
				$gusers2 .= ';'.$user_id.';';
		}
		else
		{
			echo _('User does not exist');
			exit();
		}
		
		mysql_query("UPDATE `groups` SET `user_ids` = '".$gusers2."' WHERE `group_id` = '$gid' LIMIT 1 ;");
		header("Location: admin_group.php?gid=$gid");
		exit();
	}
	
	if(isset($_GET['group_del_user']) && is_numeric($_GET['group_del_user']))
	{
		// Deleting a user
		if(!$login['user_access_useredit'])
		{
			showAccessDenied($day, $month, $year, $area, true);
			exit ();
		}
		
		$user_id = $_GET['group_del_user'];
		$gusers2 = str_replace(';'.$user_id.';', '', $gusers2);
		mysql_query("UPDATE `groups` SET `user_ids` = '".$gusers2."' WHERE `group_id` = '$gid' LIMIT 1 ;");
		header("Location: admin_group.php?gid=$gid");
		exit();
	}
	
	// Display
	include "include/admin_middel.php";
	echo '<h1>'._('Usergroups').'</h1>';
	echo '- <a href="admin_group.php">'._("Back").'</a><br>'.chr(10);
	echo '<b>'._('Viewing group').'</b><br>'.chr(10);
	echo _('Group name').': '.$group_name.'<br>'.chr(10);
	echo _('Users').': '.count($gusers1).'<br><br>'.chr(10);
	echo '<b>'._('Add user to group').':</b><br>'.chr(10);
	
	if($login['user_access_useredit'])
	{
		echo '<form action="admin_group.php?gid='.$gid.'" method="post">'.chr(10);
		echo '<input type="text" name="group_add_user">'.chr(10);
		echo '<input type="submit" value="'._('Add').'">'.chr(10);
		echo ' ('._('Enter userID').')'.chr(10);
	}
	else
		echo _('You are not allowed to do this.');
	echo '<br><br>'.chr(10);
	echo '<b>'._('Users').'</b><br>'.chr(10);
	
	echo '<ol>'.chr(10);
	foreach ($gusers1 as $user_id => $user_name)
	{
		echo '<li><a href="user.php?user_id='.$user_id.'">'.$user_name.'</a>';
		
		if($login['user_access_useredit'])
			echo ' (<a href="admin_group.php?gid='.$gid.'&amp;group_del_user='.$user_id.'">'._('Remove user from group').'</a>)';
		
		echo '</li>'.chr(10);
	}
	echo '</ol>'.chr(10);
	
}
elseif(isset($_POST['add']))
{
	// Adding
	if(!$login['user_access_useredit'])
	{
		showAccessDenied($day, $month, $year, $area, true);
		exit ();
	}
	
	// Checking input
	$add = slashes(htmlspecialchars(strip_tags($_POST['add']),ENT_QUOTES));
	mysql_query("INSERT INTO `groups` ( `group_id` , `user_ids` , `group_name` ) VALUES ('', '', '".$add."');");
	header("Location: admin_group.php");
	exit();
}
else
{
	include "include/admin_middel.php";
	
	echo '<h1>'._('Usergroups').'</h1>';
	// Add
	echo '<form action="admin_group.php" method="post">'.chr(10);
	echo '<b>'._('Add group').'</b><br>'.chr(10);
	if($login['user_access_useredit'])
	{
		echo '<input type="text" name="add"><br>'.chr(10);
		echo '<input type="submit" value="'._('Add').'">'.chr(10);
	}
	else
		echo _('You are not allowed to do this.');
	echo '<br><br>'.chr(10);
	
	// List of groups
	echo '<b>'._('List of usergroups').'</b><br>'.chr(10);
	$Q_groups = mysql_query("select * from `groups` order by 'group_name'");
	if(!mysql_num_rows($Q_groups))
		echo _('No groups found.');
	else
	{
		while($R_group = mysql_fetch_assoc($Q_groups))
		{
			
			echo '- <a href="admin_group.php?gid='.$R_group['group_id'].'">'.$R_group['group_name'].'</a> ('.count(splittIDs($R_group['user_ids'])).' '._('users').')<br>'.chr(10);
		}
	}
}

echo '</td>
</tr>
</table>
</HTML>';