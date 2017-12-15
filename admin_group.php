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
	$Q_group = db()->prepare("select * from `groups` where group_id = '$gid'");
	$Q_group->execute();
	if($Q_group->rowCount() <= 0)
	{
		echo __("Group not found");
		exit();
	}

    $row = $Q_group->fetch();
	$group_name = $row['group_name'];
	
	// Splitting the users
	$gusers = splittIDs($row['user_ids']);
	$gusers1 = array();
	$gusers2 = '';
	foreach ($gusers as $user_id)
	{
		$the_user = getUser($user_id);
		if(count($the_user))
		{
			if(!isset($gusers1[$user_id]))
			{
				$gusers1[$user_id] = $the_user['user_name'];
				$gusers2 .= ';'.$user_id.';';
			}
		}
	}
	
	if(isset($_GET['group_add_user']) && is_numeric($_GET['group_add_user']))
	{
		// Adding a user
		if(!$login['user_access_useredit'])
		{
			showAccessDenied($day, $month, $year, $area, true);
			exit ();
		}
		
		$user_id = $_GET['group_add_user'];
		if(checkUser($user_id))
		{
			if(!array_key_exists($user_id, $gusers1))
				$gusers2 .= ';'.$user_id.';';
		}
		else
		{
			echo __('User does not exist');
			exit();
		}
		
		$Q = db()->prepare("UPDATE `groups` SET `user_ids` = :user_ids WHERE `group_id` = :gid LIMIT 1 ;");
        $Q->bindValue(':gid', $gid, PDO::PARAM_INT);
        $Q->bindValue(':user_ids', $gusers2, PDO::PARAM_STR);
        $Q->execute();
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
		$Q = db()->prepare("UPDATE `groups` SET `user_ids` = :user_ids WHERE `group_id` = :gid LIMIT 1 ;");
        $Q->bindValue(':gid', $gid, PDO::PARAM_INT);
        $Q->bindValue(':user_ids', $gusers2, PDO::PARAM_STR);
        $Q->execute();
		header("Location: admin_group.php?gid=$gid");
		exit();
	}
	
	// Display
	include "include/admin_middel.php";
	echo '<h1>'.__('Usergroups').'</h1>';
	echo '- <a href="admin_group.php">'.__("Back").'</a><br>'.chr(10);
	echo '<b>'.__('Viewing group').'</b><br>'.chr(10);
	echo __('Group name').': '.$group_name.'<br>'.chr(10);
	echo __('Users').': '.count($gusers1).'<br><br>'.chr(10);
	echo '<b>'.__('Add user to group').':</b><br>'.chr(10);
	
	if($login['user_access_useredit'])
	{
		echo '<form action="admin_group.php" method="get">'.chr(10);
		echo '<input type="hidden" name="gid" value="'.$gid.'">'.chr(10);
		echo '<input type="text" name="group_add_user">'.chr(10);
		echo '<input type="submit" value="'.__('Add').'">'.chr(10);
		echo ' ('.__('Enter userID').')'.chr(10);
	}
	else
		echo __('You are not allowed to do this.');
	echo '<br><br>'.chr(10);
	echo '<b>'.__('Users').'</b><br>'.chr(10);
	
	$Q_users = db()->prepare("SELECT user_id, user_name FROM `users` WHERE `deactivated` = false ORDER BY user_name");
	$Q_users->execute();
	$all_users = array();
	while($R_user = $Q_users->fetch())
	{
		$all_users[$R_user['user_id']] = $R_user['user_name'];
	}
	echo '<ol>'.chr(10);
	foreach ($gusers1 as $user_id => $user_name)
	{
		echo '<li><a href="user.php?user_id='.$user_id.'">'.$user_name.'</a>';
		
		if($login['user_access_useredit'])
			echo ' (<a href="admin_group.php?gid='.$gid.'&amp;group_del_user='.$user_id.'">'.__('Remove user from group').'</a>)';
		
		echo '</li>'.chr(10);
		
		if(isset($all_users[$user_id]))
			unset($all_users[$user_id]);
	}
	echo '</ol>'.chr(10);
	
	if(count($all_users))
	{
		echo '<h2>'._h('Users not in this group').'</h2>';
		echo '<table class="prettytable">'.chr(10);
		foreach ($all_users as $user_id => $user_name)
		{
			echo '<tr><td><a href="user.php?user_id='.$user_id.'">'.$user_name.'</a>';
			
			echo '</td><td>';
			if($login['user_access_useredit'])
				echo '<a href="admin_group.php?gid='.$gid.'&amp;group_add_user='.$user_id.'">'._h('Add user to group').'</a>';
			
			echo '</td></tr>'.chr(10);
		}
		echo '</table>'.chr(10);
	}
	
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
	$Q = db()->prepare("INSERT INTO `groups` ( `group_id` , `user_ids` , `group_name` ) VALUES (null, '', :group_name);");
    $Q->bindValue(':group_name', $add, PDO::PARAM_STR);
    $Q->execute();
	header("Location: admin_group.php");
	exit();
}
else
{
	include "include/admin_middel.php";
	
	echo '<h1>'.__('Usergroups').'</h1>';
	// Add
	echo '<form action="admin_group.php" method="post">'.chr(10);
	echo '<b>'.__('Add group').'</b><br>'.chr(10);
	if($login['user_access_useredit'])
	{
		echo '<input type="text" name="add"><br>'.chr(10);
		echo '<input type="submit" value="'.__('Add').'">'.chr(10);
	}
	else
		echo __('You are not allowed to do this.');
	echo '<br><br>'.chr(10);
	
	// List of groups
	echo '<b>'.__('List of usergroups').'</b><br>'.chr(10);
	$Q_groups = db()->prepare("select * from `groups` order by 'group_name'");
	$Q_groups->execute();
	if($Q_groups->rowCount() <= 0)
		echo __('No groups found.');
	else
	{
		while($R_group = $Q_groups->fetch())
		{
			
			echo '- <a href="admin_group.php?gid='.$R_group['group_id'].'">'.$R_group['group_name'].'</a> ('.count(splittIDs($R_group['user_ids'])).' '.__('users').')<br>'.chr(10);
		}
	}
}

echo '</td>
</tr>
</table>
</HTML>';