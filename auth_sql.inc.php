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


/* getAuth($realm)
 * 
 * Request that the username/password be given for the specified realm
 * 
 * $realm - Which username/password do we want.
 * 
 * Nothing
 */
function authGet($realm)
{	
	global $testSystem, $deactivated;
	
	if(isset($_POST['WEBAUTH_USER']))
		$user = slashes(htmlspecialchars(strip_tags($_POST['WEBAUTH_USER']),ENT_QUOTES));
	else
		$user = '';
	
	if(!isset($_POST['sendepost_navn']))
		$_POST['sendepost_navn'] = '';
	if(!isset($_POST['sendepost_epost']))
		$_POST['sendepost_epost'] = '';
	if(!isset($_POST['sendepost_melding']))
		$_POST['sendepost_melding'] = '';
	
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
	echo "<form method=POST action=\"".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."\">";
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
	
	echo "<br><br>";
	echo "<form method=POST action=\"".$_SERVER['PHP_SELF']."?sendepost=1\">";
	echo '<table width=400 align=center cellspacing=0 cellpadding=1 style="border:1px solid #0000ff; padding: 30px;">';
	echo "<tr><td colspan=2 align=center><b>Sliter du med passordet eller har ikke bruker?</b><br>Send beskjed til Hallvard da vel.</td></tr>";
	if(isset($_GET['sendepost'])) {
		echo "<tr><td colspan=2 align=center bgcolor=#ff0000><font color=#ffffff>Du må taste inn i alle feltene under.</font></td></tr>";
	}
	if(isset($_GET['melding_sendt'])) {
		echo '<tr><td colspan=2 align=center bgcolor=#ff0000><font color=#ffffff>Melding sendt!</font></td></tr>';
	}
	echo "<tr><td>Ditt navn</td>";
	echo "<td><input type=\"text\" value=\"".$_POST['sendepost_navn']."\" name=\"sendepost_navn\"></td></tr>";
	
	echo "<tr><td>Din epost</td>";
	echo "<td><input type=\"text\" value=\"".$_POST['sendepost_epost']."\" name=\"sendepost_epost\"></td></tr>";
	
	echo "<tr><td>Melding</td>";
	echo '<td><textarea row="10" cols="20" name="sendepost_melding">'.$_POST['sendepost_melding'].'</textarea></td></tr>';
	
	echo "<tr><td>&nbsp;</td><td><input type=\"submit\" value=\"Send melding\"></td>";
	echo "</tr></table>";
	echo "</form>";
	
	if(isset($_POST['WEBAUTH_USER']))
		echo "<script language=JavaScript>document.getElementById('dofocus2').focus();</script>";
	else
		echo "<script language=JavaScript>document.getElementById('dofocus').focus();</script>";
	
	echo "</body></html>";
	exit;
}

function getUserName(){
    if(isset($_SESSION['WEBAUTH_VALID'])){
        return $_SESSION['WEBAUTH_USER'];
    }
	elseif(isset($_POST['WEBAUTH_USER'])) {
		return $_POST['WEBAUTH_USER'];
	}
	else
		return '';
}

function getUserID() {
	global $login;
    if(isset($login['user_id'])){
        return $login['user_id'];
    }
	else
		return 0;
}

function getUserPassword(){
    if(isset($_SESSION['WEBAUTH_VALID'])){
        return $_SESSION['WEBAUTH_PW'];
    }
	elseif(isset($_POST['WEBAUTH_PW']))
		return $_POST['WEBAUTH_PW'];
	else
		return '';
}

function isLoggedIn ()
{
	global $login;
	// $login['user_id'], $login['user_password']
	// password is in md5()
	
	if(!isset($login['user_id']) || $login['user_id'] == '' || $login['user_id'] == '0' || $login['user_password'] == '')
	{
		return FALSE;
	}
	else
	{
		$Q_login = mysql_query("select user_id from `users` where user_id = '".$login['user_id']."' and user_password = '".$login['user_password']."' limit 1");
		if(mysql_num_rows($Q_login) > '0')
		{
			return TRUE;
		}
		else
			return FALSE;
	}
	return FALSE;
}

function getUserinfoLoggedin ()
{
	global $login;
	global $userinfo;
	
	//$userinfo = getUserinfo ($login['user_id']);
	$userinfo = getUser($login['user_id']);
	$login = $userinfo;
}

function checkUser ($user_id = '0')
{
	if($user_id == '0')
		return FALSE;
	else
	{
		$Q_user = mysql_query("select * from `users` where user_id = '".$user_id."'");
		
		if(!mysql_num_rows($Q_user))
			return FALSE;
		else
			return TRUE;
	}
}

function getUserinfo ($user_id = '0')
{	
	$userinfo_to_return = array('user_id', 'user_name', 'user_email', 'user_invoice');
	if($user_id == '0')
	{
		return array();
	}
	else
	{
		$Q_userinfo = mysql_query("select * from `users` where user_id = '".$user_id."'");
		
		if(!mysql_num_rows($Q_userinfo))
			return array();
		else
		{
			$return = array();
			foreach ($userinfo_to_return as $info)
			{
				$return[$info] = mysql_result($Q_userinfo, 0, $info);
			}
			
			return $return;
		}
	}
}

?>