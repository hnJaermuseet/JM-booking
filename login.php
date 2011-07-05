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

include "glob_inc.inc.php";

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
	echo _("You're already logged in.").'<br><br>';
	echo "<a href=\"logout.php\">"._("Log out").'</a>';
	exit();
}
else
{
	if(isset($_POST['WEBAUTH_USER']))
		$user = slashes(htmlspecialchars(strip_tags($_POST['WEBAUTH_USER']),ENT_QUOTES));
	else
		$user = '';
	
	echo '<HTML>
    <HEAD>
    <TITLE>';
	echo _("JM-booking"); 
	echo ' - ';
	echo _("Log in"); 
	echo '</TITLE>
<LINK REL="stylesheet" href="css/jm-booking.css" type="text/css">
<META HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="js/browser_detection.js"></script>
    </HEAD>
	';
	echo "<br><br><br><br>";
	//echo '<script>if(!moz||!moz_brow==\'Firefox\') alert(\'Du kjører ikke Firefox. Det er ikke anbefalt og det kan hende at enkelte funksjoner ikke fungerer. Ta helst å bytt over til Firefox!\');</script>';
	echo $testSystem['msgLogin'];
	//echo "<div style=\"border:1px solid #0000ff;\">";
	
	//echo "</div>";
	
	//echo "<br><br>";
	echo '<table style="border:0px solid #0000ff; border-collapse:collapse;" align=center cellspacing=0 cellpadding=0>';
	
	echo '<tr>';
	
	echo '<td align="center" style="border:1px solid #0000ff; padding: 30px;">';
	echo '<form method=POST action="'.$_SERVER['PHP_SELF'].'">';
	if(isset($_GET['redirect']))
		echo '<input type="hidden" name="redirect" value="'.$_GET['redirect'].'">'.chr(10);
	echo "<table width=300 border=0 align=center cellspacing=0 cellpadding=1>";
	echo '<tr><td colspan="2" style="text-align:center; font-size:18px; padding: 10px;"><b>Innlogging til booking</b></td></tr>';
	if(!$deactivated && isset($_POST['WEBAUTH_USER'])) {
		echo '<tr><td colspan="2"" align="center"><div class="error">'.
		_("Username and/or password is wrong").
		'</div></td></tr>';
	}
	if($deactivated) {
		echo '<tr><td colspan="2" align="center"><div class="error">'.
		_('The account is disabled').
		'</div></td></tr>';
	}
	echo "<tr><td>", _("Username"), "</td>";
	echo "<td><input id=\"dofocus\" type=\"text\" value=\"".$user."\" name=\"WEBAUTH_USER\"></td></tr>";
	
	echo "<tr><td>", _("Password"), "</td>";
	echo "<td><input id=\"dofocus2\" type=\"password\" name=\"WEBAUTH_PW\"></td></tr>";
	
	echo "<tr><td>&nbsp;</td><td><input type=\"submit\" value=\"", _("Log in"), "\"></td>";
	//echo "<td><a href=\"javascript:history.back()\">", _("Back"), "</a></td>";
	echo "</tr></table>";
	echo "</form>";
	echo '</td>';
	
	echo '<td style="text-align: center; vertical-align:middle; border:1px solid #0000ff; padding: 30px; width: 300px;">'.
	'<a href="/wiki/" style="font-size: 28px">Wiki</a><br>'.
	'Wiki for opplæring og rutiner på Vitenfabrikken</td>';
	echo "</tr></table>";
	
	if(isset($_POST['WEBAUTH_USER']))
		echo "<script language=JavaScript>document.getElementById('dofocus2').focus();</script>";
	else
		echo "<script language=JavaScript>document.getElementById('dofocus').focus();</script>";
	
	echo "</body></html>";
	
	exit();
}