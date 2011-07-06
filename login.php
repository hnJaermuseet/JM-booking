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
$external_failed = false; $complex_failed = false; $age_failed = false;
$is_external = isExternal();
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
		$pass	= getPasswordHash ($pass);
		
		// Checking against database
		$Q_login = mysql_query("select user_id, deactivated, user_password_complex, user_password_lastchanged from `users` where user_name_short = '".$user."' and user_password = '".$pass."' limit 1");
		if(mysql_num_rows($Q_login) > '0')
		{
			if($is_external)
			{
				try {
					$user_login = array('user_password_lastchanged' => mysql_result($Q_login, 0, 'user_password_lastchanged'));
					loginPWcheckAge($user_login);
				} catch (Exception $e) {
					$external_failed = true;
					$age_failed = true;
				}
			}
			
			if(mysql_result($Q_login,0,'deactivated'))
			{
				$deactivated = true;
			}
			elseif($is_external && !mysql_result($Q_login, 0, 'user_password_complex'))
			{
				$external_failed = true;
				$complex_failed = true;
			}
			elseif(!$external_failed)
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
	
	
	$forgot_pw_failed = false;
	$forgot_pw_found = false;
	$forgot_pw_keyfound = false;
	$forgot_pw_keyokey = false;
	$forgot_pw_user = '';
	if(isset($_GET['forgot_pw_user']))
		$forgot_pw_user = $_GET['forgot_pw_user'];
	if(isset($_POST['forgot_pw_user']))
		$forgot_pw_user = $_POST['forgot_pw_user'];
	
	if(isset($_GET['forgot_pw']) && $_GET['forgot_pw'] == '1' && $forgot_pw_user != '')
	{
		// Forgot password
		$user	= slashes(htmlspecialchars(strip_tags($forgot_pw_user),ENT_QUOTES)); // Username
		$Q_login = mysql_query("
			select user_id, deactivated, user_newpassword_key, user_newpassword_validto from `users` where 
				deactivated = '0' and 
				(
					user_name_short = '".$user."' ||
					user_email = '".$user."'
				)
				limit 1");
		if(mysql_num_rows($Q_login) > 0)
		{
			$user_id = mysql_result($Q_login, 0, 'user_id');
			$forgot_pw_found = true;
			
			if(isset($_GET['key']))
			{
				$forgot_pw_keyfound = true;
				$newpw_failed_msg = ''; $newpw_pw = ''; $newpw_failed = false;
				$newpw_user = getUser($user_id);
				$newpw_key = slashes(htmlspecialchars(strip_tags($_GET['key']),ENT_QUOTES));
				if(!count($newpw_user)) { echo 'Systemfeil. Arg... Sorry :-('; exit; }
				if(
					$newpw_key == mysql_result($Q_login, 0, 'user_newpassword_key') &&
					mysql_result($Q_login, 0, 'user_newpassword_validto') >= time()
				)
				{
					$forgot_pw_keyokey = true;
					
					if(!isset($_POST['password_new']))
					{
						// Extend life time of key
						$valid_to = time()+ 60*15; // 15 min
						
						mysql_query("
							update `users`
							set 
								user_newpassword_validto = '$valid_to'
							where
								user_id = '$user_id'");
					}
					else
					{
						// Setting the new password
						$newpw_user['user_password_lastchanged'] = time(); // All new
						$newpw_pw = $_POST['password_new'];
						try {
							loginPWcheckExternal ($newpw_user, $newpw_pw);
							loginPWcheckSetNew ($newpw_user, $newpw_pw);
						}
						catch (Exception $e)
						{
							$newpw_failed_msg = $e->getMessage();
							$newpw_failed = true;
						}
						
						if(
							!$newpw_failed ||
							($newpw_failed && isset($_POST['ignore_msg']) && $_POST['ignore_msg'] == '1')
						)
						{
							$sql = 
								'UPDATE `users` SET '.
									'`user_password`              = \''.getPasswordHash($newpw_pw).'\', '.
									'`user_password_1`            = \''.$newpw_user['user_password'].'\', '.
									'`user_password_2`            = \''.$newpw_user['user_password_1'].'\', '.
									'`user_password_3`            = \''.$newpw_user['user_password_2'].'\', '.
									'`user_password_lastchanged`  = \''.time().'\', '.
									'`user_newpassword_validto`   = \'\', '.
									'`user_password_complex`      = \''.!$newpw_failed.'\''.
								' WHERE `user_id` = '.$newpw_user['user_id'].' LIMIT 1 ;';
							mysql_query($sql);
							if(mysql_error())
							{
								echo 'Error<br>';
								echo mysql_error();
								exit;
							}
							
							header('Location: logout.php?newpw_ok=1');
							exit;
						}
					}
				}
			}
			else
			{
				// Generate key and valid_to
				$key = sha1(microtime(true).mt_rand(10000,90000));
				$valid_to = time()+ 60*15; // 15 min
				
				mysql_query("
					update `users`
					set 
						user_newpassword_key = '$key',
						user_newpassword_validto = '$valid_to'
					where
						user_id = '$user_id'");
				
				$smarty = new Smarty;
				
				templateAssignSystemvars('smarty');
				$smarty->assign('forgot_pw_user', $user);
				$smarty->assign('key', $key);
				$smarty->assign('valid_to', $valid_to);
				$message = $smarty->fetch('file:mail-forgot_pw.tpl');
				$subject = 'Glemt passord - Jærmuseets bookingsystem';
				
				emailSend($user_id, $subject, $message);
			}
		}
		else
		{
			$forgot_pw_failed = true;
		}
		
	}
	
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
	
	if($forgot_pw_keyfound)
		echo '		<td align="center" style="border:1px solid #0000ff; padding: 30px; width: 600px;" colspan="2">'.chr(10);
	else
		echo '		<td align="center" style="border:1px solid #0000ff; padding: 30px; width: 300px;">'.chr(10);
	

	if(!$forgot_pw_keyfound && isset($_GET['forgot_pw']) && $_GET['forgot_pw'] == '1')
	{
		echo '			<form method="POST" action="'.$_SERVER['PHP_SELF'].'?forgot_pw=1">'.chr(10);
		if(isset($_GET['redirect']))
			echo '<input type="hidden" name="redirect" value="'.$_GET['redirect'].'">'.chr(10);
		echo chr(10).
			'<table width="300" border="0" align="center" cellspacing="0" cellpadding="1">'.chr(10).
			'	<tr>'.chr(10).
			'		<td colspan="2" style="font-size:14px; padding: 10px;">'.
			'<b>Typisk å glemme passordet,<br />skal hjelpe deg jeg...</b></td>'.chr(10).
			'	</tr>'.chr(10).chr(10);
		
		echo
			'	<tr>'.chr(10).
			'		<td colspan="2">'.
			'Trenger å vite brukernavnet ditt eller eposten din. Skal så sende deg en kode på epost, '.
			'så skal du få lage nytt passord.<br /><br /></td>'.chr(10).
			'	</tr>'.chr(10).chr(10);
	
		if($forgot_pw_failed) {
			echo '	<tr><td colspan="2" align="center"><div class="error">'.
			'<b>'._h('Did not find any user with that username or with that email address.').'</b><br /><br />'.
			_h('Please try again.').
			'</div></td></tr>'.chr(10).chr(10);
		}
		echo '	<tr>'.chr(10).
			'		<td>'._('Username').' /<br />'._h('E-mail').'</td>'.chr(10).
			'		<td><input id="dofocus" type="text" value="'.$user.'" name="forgot_pw_user"></td>'.chr(10).
			'	</tr>'.chr(10).chr(10);
		
		echo '	<tr>'.chr(10).
			'		<td>&nbsp;</td>'.chr(10).
			'		<td><input type="submit" value="'._h('Send e-mail with code').'"></td>'.chr(10).
			'	</tr>'.chr(10).chr(10);
		echo '</table>'.chr(10).chr(10);
		echo '			</form>'.chr(10);
		echo '<br /><br />';
	}
	elseif($forgot_pw_keyfound)
	{
		if(!$forgot_pw_keyokey)
		{
			echo '<div class="error">'.
					'Koden du oppgav er for gammel (mer enn 15 minutter gammel), '.
					'eller eksisterer ikke for brukeren.<br /><br />'.
					
					'Sjekk også at du ikke har fått en ny etter den du trykket på nå.<br /><br />'.
					'Hvis du ikke får det til, '.
					'så <a href="'.$_SERVER['PHP_SELF'].'?forgot_pw=1">få tilsendt en ny</a>.'.
				'</div>';
		}
		else
		{ // start new pw form
			echo '<h1>'._h('Change password for').' '.$newpw_user['user_name'].'</h1>'.chr(10).chr(10);
			echo '<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>';
			
			if(isset($_GET['ok']) && $_GET['ok'] == '1')
			{
				echo '<div class="success" style="width: 400px;">'._h('Password has been changed.').'</div>';
			}
			else
			{
				echo 'For ekstern pålogging (ikke Jærmuseets maskiner),<br />'.
					'så kreves det at passordet inneholder store og små bokstaver samt tall.<br />';
				echo '<form action="'.$_SERVER['PHP_SELF'].'?forgot_pw=1&amp;forgot_pw_user='.$user.'&amp;key='.$newpw_key.'" method="post">'.chr(10);
				echo '<b>'._h('New password').':</b><br />'.chr(10);
				echo '<input type="password" id="dofocus" name="password_new" value="'.$newpw_pw.'"><br /><br />'.chr(10).chr(10);
				
				if(!isset($_POST['password_new']))
				{
					echo '<input type="submit" value="'._h('Save password').'">'.chr(10);
				}
				elseif($newpw_failed)
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
						'Password can not be used if you want to be able to log in externally. '.
						'Log in from internal computers will still be possible.'
					).'<br /><br />'.
					'<b>- '.$newpw_failed_msg.'</b><br /><br />'.
					
					_h('Press "Save password" again to use the choosen password.').
					'<br />'.
					'Du kan fint ha dette passordet hvis du bare vil logge på fra Jærmuseets maskiner'.
					'</div>';
					echo '<input type="hidden" value="1" name="ignore_msg">';
					echo '<input type="submit" value="'._h('Save password').'">'.chr(10);
				}
				/*
				elseif($failed_msg != '')
				{
					echo '<div class="success" style="width: 400px;">'.
					_h('The password is good enougth for external logins.').'</div>';
					echo '<input type="submit" value="'._h('Save password').'">'.chr(10);
				}*/
				echo '</form>'.chr(10);
			}
		} // end new pw form
	}
	else
	{ // start login form
		echo '			<form method="POST" action="'.$_SERVER['PHP_SELF'].'">'.chr(10);
		if(isset($_GET['redirect']))
			echo '<input type="hidden" name="redirect" value="'.$_GET['redirect'].'">'.chr(10);
		echo chr(10).
			'<table width="300" border="0" align="center" cellspacing="0" cellpadding="1">'.chr(10).
			'	<tr>'.chr(10).
			'		<td colspan="2" style="text-align:center; font-size:18px; padding: 10px;"><b>Innlogging til booking</b></td>'.chr(10).
			'	</tr>'.chr(10).chr(10);
		
		if(isset($_GET['newpw_ok']) && $_GET['newpw_ok'] == '1')
		{
			echo '	<tr><td colspan="2" align="center"><div class="success" style="width: 400px;">'.
			'<b>'._h('Your password has been changed.').'</b><br /><br />'.
			_h('Please log in again.').
			'</div></td></tr>'.chr(10).chr(10);
		}
		if($deactivated) {
			echo '	<tr><td colspan="2" align="center"><div class="error">'.
			_('The account is disabled').
			'</div></td></tr>'.chr(10).chr(10);
		}
		elseif($external_failed && $complex_failed) {
			echo '	<tr><td colspan="2" align="center"><div class="error">'.
			_h('You do not have access to the system because your password is not complex enough for external login.').' '.
			_h(
				'Please get yourself a new password or use an internal computer instead. '.
				'Your password will still work when using internal computers.'
			).
			'</div></td></tr>'.chr(10).chr(10);
		}
		elseif($external_failed && $age_failed) {
			echo '	<tr><td colspan="2" align="center"><div class="error">'.
			_h('You do not have access to the system because your password is too old for external login.').' '.
			_h(
				'Please get yourself a new password the next time you are using an internal computer. '.
				'Your password will still work when using internal computers.'
			).
			'</div></td></tr>'.chr(10).chr(10);
		}
		elseif(isset($_POST['WEBAUTH_USER'])) {
			echo '	<tr><td colspan="2"" align="center"><div class="error">'.
			_('Username and/or password is wrong').
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
		echo '<br /><br />';
		echo '<a href="'.$_SERVER['PHP_SELF'].'?forgot_pw=1">'._h('Forgot password').'?</a>';
	} // End login form
	
	if(!$forgot_pw_keyfound)
	{
		echo '		</td>'.chr(10);
		
		echo 
		'		<td style="text-align: center; vertical-align:middle; border:1px solid #0000ff; padding: 30px; width: 300px;">'.chr(10);
		
		if(isset($_GET['forgot_pw']) && $_GET['forgot_pw'] == '1')
		{
			if(!$forgot_pw_found)
				echo '&nbsp;';
			else
			{
				echo '<div class="success"><b>Supert!<br />Nå: Trykk på lenken i e-posten</b><br />'.
					'En e-post er sendt til deg med en lenke du må trykke på. '.
					'Når du går inn på denne lenken, så vil du får muligheten til å bytte passord.</div>';
			}
		}
		else
		{
			echo 
				'			<a href="/wiki/" style="font-size: 28px">Wiki</a><br>'.chr(10).
				'			Wiki for opplæring og rutiner på Vitenfabrikken'.chr(10);
		}
	}
	
	echo
	'		</td>'.chr(10).
	'	</tr>'.chr(10).
	'</table>'.chr(10).chr(10);
	
	if(!(isset($_GET['forgot_pw']) && $_GET['forgot_pw'] == '1'))
	{
		if(isset($_POST['WEBAUTH_USER']))
			echo '<script language="javascript">document.getElementById(\'dofocus2\').focus();</script>';
		else
			echo '<script language="javascript">document.getElementById(\'dofocus\').focus();</script>';
	}
	
	echo chr(10).
		'</body>'.chr(10).
		'</html>';
	
	if($is_external)
	{
		echo '<!-- External -->';
	}
	else
	{
		echo '<!-- Internal -->';
	}
	exit();
}