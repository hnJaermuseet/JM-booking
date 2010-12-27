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
				
				header('Location: index.php');
				exit();
			}
		}
		else
		{
			
		}
	}
}

if(isset($_GET['sendepost']) &&
	isset($_POST['sendepost_navn']) &&
	isset($_POST['sendepost_epost']) &&
	isset($_POST['sendepost_melding']))
{
	require "libs/mail.class.php";
	$epostform_feilfunnet = false;
	
	$_POST['sendepost_navn']	= htmlspecialchars(strip_tags($_POST['sendepost_navn']),ENT_QUOTES);
	$_POST['sendepost_epost']	= htmlspecialchars(strip_tags($_POST['sendepost_epost']),ENT_QUOTES);
	$_POST['sendepost_melding']	= htmlspecialchars(strip_tags($_POST['sendepost_melding']),ENT_QUOTES);
	if($_POST['sendepost_navn'] == '' || $_POST['sendepost_epost'] == '' || $_POST['sendepost_melding'] == '')
	{
		$epostform_feilfunnet = true;
	}
	else
	{
		// Sender epost med spørsmål
		$mail = new mail();
		$mail->AddReplyTo($_POST['sendepost_epost'], $_POST['sendepost_navn']);
		$mail->AddAddress('hn@jaermuseet.no');
		$mail->Subject	= "JM-booking - Loginmelding";
		
		$smarty = new Smarty;
		$smarty->assign('navn', $_POST['sendepost_navn']);
		$smarty->assign('epost', $_POST['sendepost_epost']);
		$smarty->assign('melding', $_POST['sendepost_melding']);
		$mail->Body = $smarty->fetch('epost/loginmelding.tpl');
		
		if(!$mail->Send())
		{
			echo 'Det oppsto en feil ved sendingen av e-posten.<br>'.chr(10);
			echo '<b>Error:</b> '.$mail->ErrorInfo;
			exit();
		}
		
		header('Location: '.$_SERVER['PHP_SELF'].'?melding_sendt=1');
		exit();
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
	
	echo '<br><br>';
	echo '<form method=POST action="'.$_SERVER['PHP_SELF'].'?sendepost=1">';
	echo '<div style="text-align: center;">';
	echo '<div style="border:1px solid #0000ff; padding: 30px; width:450px; margin-left: auto; margin-right: auto;">';
	echo '<table width="400" align="center" cellspacing="0" cellpadding="0">'.chr(10);
	echo '	<tr>'.chr(10);
	echo '		<td colspan="2" align=center><b>Sliter du med passordet eller har ikke bruker?</b><br>Send beskjed til Hallvard da vel.</td></tr>';
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
	echo '</tr>'.chr(10).
	'</table>'.
	'</div>'.
	'</div>'.
	'</form>';
	
	if(isset($_POST['WEBAUTH_USER']))
		echo "<script language=JavaScript>document.getElementById('dofocus2').focus();</script>";
	else
		echo "<script language=JavaScript>document.getElementById('dofocus').focus();</script>";
	
	echo "</body></html>";
	exit;
}

	authGet('');
	exit();
}