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

require "libs/editor.class.php";
$section = 'users';

include "include/admin_top.php";

$id = 0;
if(isset($_GET['id']) && is_numeric($_GET['id']))
	$id = (int)$_GET['id'];
if(isset($_POST['id']) && is_numeric($_POST['id']))
	$id = (int)$_POST['id'];

if(!$login['user_access_useredit'] && $id != $login['user_id'])
{
	showAccessDenied($day, $month, $year, $area, true);
	exit ();
}

$user = getUser($id);
if(!count($user))
{
	echo 'Unable to locate user.';
	exit;
}

$failed_msg = ''; $pw = ''; $failed = false;
$serious_failed = false;
if(isset($_POST['password_new']))
{
	$user2 = $user;
	$user2['user_password_lastchanged'] = time(); // All new
	$pw = $_POST['password_new'];
	try {
		if(
			$id == $login['user_id'] && 
			(
				!isset($_POST['password_old']) || 
				getPasswordHash($_POST['password_old']) != $user['user_password']
			)
		)
		{
			$serious_failed = true;
			throw new Exception(_h('Old password is not correct.'));
		}
		loginPWcheckExternal ($user2, $pw);
		loginPWcheckSetNew ($user2, $pw);
	}
	catch (Exception $e)
	{
		$failed_msg = $e->getMessage();
		$failed = true;
	}
	
	if(
		!$serious_failed && 
		(
			!$failed ||
			($failed && isset($_POST['ignore_msg']) && $_POST['ignore_msg'] == '1')
		)
	)
	{
		$sql = 
			'UPDATE `users` SET '.
				'`user_password`              = :user_password, '.
				'`user_password_1`            = :user_password_1, '.
				'`user_password_2`            = :user_password_2, '.
				'`user_password_3`            = :user_password_3, '.
				'`user_password_lastchanged`  = :user_password_lastchanged, '.
				'`user_password_complex`      = :user_password_complex'.
			' WHERE `user_id` = :user_id LIMIT 1 ;';
		$Q = db()->prepare($sql);
        $Q->bindValue(':user_password', getPasswordHash($pw));
        $Q->bindValue(':user_password_1', $user['user_password']);
        $Q->bindValue(':user_password_2', $user['user_password_1']);
        $Q->bindValue(':user_password_3', $user['user_password_2']);
        $Q->bindValue(':user_password_lastchanged', time());
        $Q->bindValue(':user_password_complex', (!$failed) ? 1 : 0);
        $Q->bindValue(':user_id', $user['user_id']);
		if(!$Q->execute())
		{
			echo 'Error mysql<br>';
			exit;
		}
		
		if($user['user_id'] == $login['user_id'])
			header('Location: logout.php?newpw_ok=1');
		else
			header('Location: admin_user_password.php?id='.$user['user_id'].'&ok=1');
		exit;
	}
}

include "include/admin_middel.php";

echo '<h1>'._h('Change password for').' '.$user['user_name'].'</h1>'.chr(10).chr(10);

if(isset($_GET['ok']) && $_GET['ok'] == '1')
{
	echo '<div class="success" style="width: 400px;">'._h('Password has been changed.').'</div>';
}
else
{
	echo '<form action="'.$_SERVER['PHP_SELF'].'?id='.$user['user_id'].'" method="post">'.chr(10);
	
	if($id == $login['user_id'])
	{
		if($serious_failed)
			echo '<div class="error" style="width: 400px;">'.$failed_msg.'</div>';
		
		echo '<b>'._h('Old password').':</b><br />'.chr(10);
		echo '<input type="password" name="password_old" value=""><br /><br />'.chr(10).chr(10);
	}
	
	echo '<b>'._h('New password').':</b><br />'.chr(10);
	echo '<input type="password" name="password_new" value="'.$pw.'"><br /><br />'.chr(10).chr(10);
	
	if(!isset($_POST['password_new']))
	{
		echo '<input type="submit" value="'._h('Save password').'">'.chr(10);
	}
	elseif($failed && !$serious_failed)
	{
		echo '<script type="text/javascript">
		$("input[type=password][name=password_new]").keyup(function() {
			$("#failed_msg").slideUp();
			$("input[type=hidden][name=ignore_msg]").attr("value", "0");
		});
		$("input[type=password][name=password_new]").change(function() {
			$("#failed_msg").slideUp();
			$("input[type=hidden][name=ignore_msg]").attr("value", "0");
		});
		$("input[type=password][name=password_new]").click(function() {
			$("#failed_msg").slideUp();
			$("input[type=hidden][name=ignore_msg]").attr("value", "0");
		}); 
</script>'.chr(10);
		echo '<div class="notice" id="failed_msg" style="width: 400px;">'.
		_h(
			'Password can not be used if you want the user to be able to log in externally. '.
			'Log in from internal computers will still be possible.'
		).'<br /><br />'.
		'<b>- '.$failed_msg.'</b><br /><br />'.
		
		_h('Press "Save password" again to use the choosen password.').
		'</div>';
		echo '<input type="hidden" value="1" name="ignore_msg">';
		echo '<input type="submit" value="'._h('Save password').'">'.chr(10);
	}
	elseif($serious_failed)
		echo '<input type="submit" value="'._h('Save password').'">'.chr(10);
	/*
	elseif($failed_msg != '')
	{
		echo '<div class="success" style="width: 400px;">'.
		_h('The password is good enougth for external logins.').'</div>';
		echo '<input type="submit" value="'._h('Save password').'">'.chr(10);
	}*/
	echo '</form>'.chr(10);
}