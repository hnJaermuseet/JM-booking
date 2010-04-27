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

if(isset($_POST['WEBAUTH_USER']))
{
	if(authValidateUser(getUserName(), getUserPassword()))
	{
		header('Location: index.php');
		exit();
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
	authGet('');
	exit();
}