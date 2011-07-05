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
	JM-booking - login
*/

include 'glob_inc.inc.php';

$deactivated = false;
if(isset($_POST['WEBAUTH_USER']))
{
	$user = getUserName();
	$pass = getUserPassword();
	
	// Check if we do not have a username/password
	if(empty($user) || empty($pass)) {
		
	}
	else
	{
		$user	= slashes(htmlspecialchars(strip_tags($user),ENT_QUOTES)); // Username
		$pass	= md5($pass); // md5 hash of the password
		
		// Checking against database
		$Q_login = mysql_query("select user_id, deactivated from `users` where user_name_short = '".$user."' and user_password = '".$pass."' limit 1");
		if(mysql_num_rows($Q_login) > '0')
		{
			if(mysql_result($Q_login,0,'deactivated'))
			{
				$deactivated = true;
			}
			else
			{
				session_register('WEBAUTH_VALID');
		        session_register('WEBAUTH_USER');
		        session_register('WEBAUTH_PW');
		        $_SESSION['WEBAUTH_VALID']=true;
		        $_SESSION['WEBAUTH_USER']=$user;
		        $_SESSION['WEBAUTH_PW']=$pass;
				
				// New variabels (JM-booking)
				$_SESSION['user_id']		= mysql_result($Q_login, 0, 'user_id');
				$_SESSION['user_password']	= $pass;
				
				if(isset($_POST['redirect']))
					header('Location: '.$_POST['redirect']);
				else
					header('Location: index.php');
				exit();
			}
		}
		else
		{
			
		}
	}
}

if(isLoggedIn())
{
	echo _('You\'re already logged in.').'<br><br>';
	echo '<a href="logout.php">'._('Log out').'</a>';
	exit();
}
else
{
	if(isset($_POST['WEBAUTH_USER']))
		$user = slashes(htmlspecialchars(strip_tags($_POST['WEBAUTH_USER']),ENT_QUOTES));
	else
		$user = '';
	
	echo '<html>'.chr(10).'<head>'.chr(10).
		'	<title>'._('JM-booking').' - '._('Log in').'</title>'.chr(10).
		'	<link rel="stylesheet" href="css/jm-booking.css" type="text/css">'.chr(10).
		'	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">'.chr(10).
		'	<script type="text/javascript" src="js/browser_detection.js"></script>'.chr(10).
		'</head>'.chr(10).
		'<body>'.chr(10);
	
	echo '<br><br><br><br>'.chr(10).chr(10);
	echo $testSystem['msgLogin'];
	
	echo 
		'<table '.
			'style="border:0px solid #0000ff; border-collapse:collapse;" '.
			'align="center" '.
			'cellspacing="0" '.
			'cellpadding="0">'.chr(10);
			'	<tr>'.chr(10);
	
	echo '		<td align="center" style="border:1px solid #0000ff; padding: 30px;">'.chr(10);
	echo '			<form method="POST" action="'.$_SERVER['PHP_SELF'].'">'.chr(10);
	if(isset($_GET['redirect']))
		echo '<input type="hidden" name="redirect" value="'.$_GET['redirect'].'">'.chr(10);
	echo chr(10).
		'<table width="300" border="0" align="center" cellspacing="0" cellpadding="1">'.chr(10).
		'	<tr>'.chr(10).
		'		<td colspan="2" style="text-align:center; font-size:18px; padding: 10px;"><b>Innlogging til booking</b></td>'.chr(10).
		'	</tr>'.chr(10).chr(10);
	if(!$deactivated && isset($_POST['WEBAUTH_USER'])) {
		echo '	<tr><td colspan="2"" align="center"><div class="error">'.
		_("Username and/or password is wrong").
		'</div></td></tr>'.chr(10).chr(10);
	}
	if($deactivated) {
		echo '	<tr><td colspan="2" align="center"><div class="error">'.
		_('The account is disabled').
		'</div></td></tr>'.chr(10).chr(10);
	}
	echo '	<tr>'.chr(10).
		'		<td>'._('Username').'</td>'.chr(10).
		'		<td><input id="dofocus" type="text" value="'.$user.'" name="WEBAUTH_USER"></td>'.chr(10).
		'	</tr>'.chr(10).chr(10);
	
	echo '	<tr>'.chr(10).
		'		<td>'._('Password').'</td>'.chr(10).
		'		<td><input id="dofocus2" type="password" name="WEBAUTH_PW"></td>'.chr(10).
		'	</tr>'.chr(10).chr(10);
	
	echo '	<tr>'.chr(10).
		'		<td>&nbsp;</td>'.chr(10).
		'		<td><input type="submit" value="'._('Log in').'"></td>'.chr(10).
		'	</tr>'.chr(10).chr(10);
	echo '</table>'.chr(10).chr(10);
	echo '			</form>'.chr(10);
	echo '		</td>'.chr(10);
	
	echo 
	'		<td style="text-align: center; vertical-align:middle; border:1px solid #0000ff; padding: 30px; width: 300px;">'.chr(10).
	'			<a href="/wiki/" style="font-size: 28px">Wiki</a><br>'.chr(10).
	'			Wiki for opplæring og rutiner på Vitenfabrikken'.chr(10).
	'		</td>'.chr(10).
	'	</tr>'.chr(10).
	'</table>'.chr(10).chr(10);
	
	if(isset($_POST['WEBAUTH_USER']))
		echo '<script language="javascript">document.getElementById(\'dofocus2\').focus();</script>';
	else
		echo '<script language="javascript">document.getElementById(\'dofocus\').focus();</script>';
	
	echo chr(10).
		'</body>'.chr(10).
		'</html>';
	
	exit();
}