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
	Administration of users
*/

/*
 * 
 * #################################################
 * ### NOT IN USE - REDIRECTS TO ADMIN_USER2.PHP ###
 * #################################################
 * 
 */

header('Location: admin_user2.php');
exit();

$section = 'users';

include "include/admin_top.php";
/*
if(isset($_GET['uid']) && is_numeric($_GET['uid'])) // Edit user
{
	if(authGetUserLevel(getUserID()) < $user_level && $_GET['uid'] != $userinfo['user_id'])
	{
		showAccessDenied($day, $month, $year, $area, true);
		exit ();
	}
	
	$user = getUser($_GET['uid']);
	// Checking if it exists
	if(!count($user))
	{
		echo _("user not found");
		exit();
	}
	
	// Display edit form
	include "include/admin_middel.php";
	
	echo '<h1>'._('Edit user').'</h1>';
	echo '- <a href="admin_user.php">'._("Back").'</a><br>'.chr(10);
	echo '<b>'._('Viewing user').'</b><br>'.chr(10);
	echo '<form action="admin_user.php" method="post">'.chr(10);
	echo '<b>'._('UserID').':</b><br><input type="text" disabled="disabled" value="'.$user['user_id'].'"><br>'.chr(10);
	echo '<b>'._('Username').':</b><br><input type="text" name="user_name" value="'.$user['user_name'].'"><br>'.chr(10);
	echo '<b>'._('Short username').':</b><br><input type="text" name="user_name" value="'.$user['user_name_short'].'"><br>'.chr(10);
	echo '<b>'._('E-mail').':</b><br><input type="text" name="user_name" value="'.$user['user_email'].'"><br>'.chr(10);
	echo '<b>'._('Phone').':</b><br><input type="text" name="user_name" value="'.$user['user_phone'].'"><br>'.chr(10);
	echo '<b>'._('Default area').':</b><br><input type="text" name="user_name" value="'.$user['user_phone'].'"><br>'.chr(10);
	echo '<b>'._('Access to').':</b><br><input type="text" name="user_name" value="'.$user['user_phone'].'"><br>'.chr(10);
	echo '<input type="hidden" name="edit" value="'.$user['user_id'].'">'.chr(10);
	echo '<br><input type="submit" value="'._('Edit').'">'.chr(10);
	
}
elseif ((isset($_POST['add']) || isset($_POST['edit'])) && isset($_POST['user_name_short']) && 
isset($_POST['user_email']) && isset($_POST['user_phone']) && isset($_POST['user_password']))
{
	if(authGetUserLevel(getUserID()) < $user_level)
	{
		showAccessDenied($day, $month, $year, $area, true);
		exit ();
	}
	// Getting the username
	$adding = true;
	if(isset($_POST['add']))
		$add	= slashes(htmlspecialchars(strip_tags($_POST['add']),ENT_QUOTES));
	else
	{
		$adding = false;
		$uid_edit = (int)$_POST['edit'];
	}
	$user_name_short	= slashes(htmlspecialchars(strip_tags($_POST['user_name_short']),ENT_QUOTES));
	$user_email			= slashes(htmlspecialchars(strip_tags($_POST['user_email']),ENT_QUOTES));
	$user_phone			= slashes(htmlspecialchars(strip_tags($_POST['user_phone']),ENT_QUOTES));
	$user_password		= md5($_POST['user_password']);
	
	if($adding)
	{
		// Check if the user already exists
		$user_exists = mysql_query("select user_id from `users` where user_name = '".$add."'");
		if(mysql_num_rows($user_exists))
		{
			echo _('That user already exists.');
			exit();
		}
		
		mysql_query("INSERT INTO `users` 
			( `user_id` , `user_name` , `user_name_short`, `user_email` , `user_phone` , `user_password` , `user_accesslevel`) 
			VALUES ('', '".$add."', '".$user_name_short."', '".$user_email."', '".$user_phone."', '".$user_password."', '1');");
	}
	else
	{
		
	}
	header("Location: admin_user.php");
	exit();
}
else
{*/
	include "include/admin_middel.php";
	
	echo '<h1>'._('Users').'</h1>';
	// Add
	echo '<form action="admin_user.php" method="post">'.chr(10);
	
	echo '<h2>'._('Add user').'</h2>'.chr(10);
	/*
	if(authGetUserLevel(getUserID()) >= $user_level)
	{
		echo '<input type="text" name="add"> - '._('Name').' / '._('Username').'<br>'.chr(10);
		echo '<input type="text" name="user_name_short"> - '._('Login name').' / '._('Username short').'<br>'.chr(10);
		echo '<input type="text" name="user_email"> - '._('Email').'<br>'.chr(10);
		echo '<input type="text" name="user_phone"> - '._('Phone').'<br>'.chr(10);
		echo '<input type="password" name="user_password"> - '._('Password').'<br>'.chr(10);
		echo '<input type="submit" value="'._('Add').'">'.chr(10);
		echo '<br><br>'.chr(10);
	}
	else
		echo _('You are not allowed to do this.');
	*//*
	echo '- <a href="admin_user2.php">Ny bruker</a>';*/
	// List of users
	echo '<h2>'._('List of users').'</h2><br>'.chr(10);
	echo _('Click on a user to edit.').' '._('In front of each user is a number. This corresponds to the userlevel.').'<br>'.chr(10);
	$Q_users = mysql_query("select * from `users` order by 'user_name'");
	if(!mysql_num_rows($Q_users))
		echo _('No users found.');
	else
	{
		while($R_user = mysql_fetch_assoc($Q_users))
		{
			
			echo '- ('.$R_user['user_accesslevel'].') <a href="admin_user2.php?uid='.$R_user['user_id'].'">'.$R_user['user_name'].'</a> (<a href="user.php?user_id='.$R_user['user_id'].'">'._('View profil').'</a>)<br>'.chr(10);
		}
	}
/*}
*/

echo '</td>
</tr>
</table>
</HTML>';