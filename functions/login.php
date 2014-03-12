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

// Settings check
if(!isset($login_internal_addresses) || !is_array($login_internal_addresses)) {
	echo '$login_internal_addresses not set in config or is not array.';
	exit;
}
if(!isset($login_password_external_complex)) {
	echo '$login_password_external_complex not set in config.';
	exit;
}
if(!isset($login_password_external_minchar)) {
	echo '$login_password_external_minchar not set in config.';
	exit;
}
if(!isset($login_password_external_maxage)) {
	echo '$login_password_external_maxage not set in config.';
	exit;
}
if(!isset($login_password_external_new_notamonglast3)) {
	echo '$login_password_external_new_notamonglast3 not set in config.';
	exit;
}


/**
 * Returns a hash of the password
 * 
 * @return string
 */
function getPasswordHash ($password)
{
	// TODO: add salt
	return md5($password);
}

// From auth_sql.inc.php
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

// From auth_sql.inc.php
function getUserPassword(){
    if(isset($_SESSION['WEBAUTH_VALID'])){
        return $_SESSION['WEBAUTH_PW'];
    }
	elseif(isset($_POST['WEBAUTH_PW']))
		return $_POST['WEBAUTH_PW'];
	else
		return '';
}

// From auth_sql.inc.php
function isLoggedIn ()
{
	global $login;
	// $login['user_id'], $login['user_password']
	// password is a hash (from getPasswordHash ())
		
	if(!isset($login['user_id']) || $login['user_id'] == '' || $login['user_id'] == '0' || $login['user_password'] == '')
	{
		return FALSE;
	}
	else
	{
		$external_failed = false;
		$Q_login = mysql_query("select user_id, deactivated, user_password_complex, user_password_lastchanged from `users` where user_id = '".$login['user_id']."' and user_password = '".$login['user_password']."' limit 1");
		if(mysql_num_rows($Q_login) > '0')
		{
			$is_external = isExternal();
			if($is_external)
			{
				try {
					$user_login = array('user_password_lastchanged' => mysql_result($Q_login, 0, 'user_password_lastchanged'));
					loginPWcheckAge($user_login);
				} catch (Exception $e) {
					return false;
				}
			}
			
			if(mysql_result($Q_login,0,'deactivated'))
			{
				return false;
			}
			elseif($is_external && !mysql_result($Q_login, 0, 'user_password_complex'))
			{
				return false;
			}
			elseif(!$external_failed)
			{
				return TRUE;
			}
		}
		else
			return FALSE;
	}
	return FALSE;
}

// From auth_sql.inc.php
function getUserinfoLoggedin ()
{
	global $login;
	
	$login = getUser($login['user_id']);
}

/**
 * Validates if a password is suitable for external use
 *
 * @param array   User (from getUser())
 * @param string  Password
 */
function loginPWcheckExternal ($user, $password)
{
	global  
		$login_password_external_complex,
		$login_password_external_minchar,
		$login_password_external_maxage;
	
	// Check length
	if(strlen($password) < $login_password_external_minchar)
	{
		throw new Exception(_h('Password too short. Must be at least ').$login_password_external_minchar. ' '._h('characters').'.');
	}
	
	loginPWcheckAge($user);
	
	// Check complexity
	// http://technet.microsoft.com/en-us/library/cc786468%28WS.10%29.aspx
	if($login_password_external_complex)
	{
		// TODO:
		// Must not contain user_name
		$names = split(' ', $user['user_name']); // Do not parse for all the delimiters
		foreach($names as $name)
		{
			if(strlen($name) > 1 && strpos(strtolower($password), strtolower($name)) !== FALSE)
			{
				throw new Exception(_h('Password can not contain on of the users names (first or last).'));
			}
		}
		
		// Must contain 3 of 4:
		$contains = 0;
		if(preg_match('([A-Z])', $password))
			$contains++;
		if(preg_match('([a-z])', $password))
			$contains++;
		if(preg_match('([0-9])', $password))
			$contains++;
		$found = false;
		$checkfor = '~!@#$%^&*_-+=`|\(){}[]:;"\'<>,.?/';
		for($i = 0; $i <+ strlen($checkfor); $i++)
		{
			if(strpos($password, $checkfor{$i}) !== FALSE)
			{
				$found = true;
			}
		}
		if($found)
			$contains++;
		
		if($contains < 3)
		{
			throw new Exception(_h('Password not complex enough. Must contain lower and upper case characters and a number.'));
		}
	}
}

function loginPWcheckAge ($user)
{
	global $login_password_external_maxage;
	
	// Check max age
	if(
		$user['user_password_lastchanged']+$login_password_external_maxage // last changed + life time
		<
		time())
	{
		throw new Exception(_h('Password is too old.'));
	}
}

/**
 * 
 * @param array   User (from getUser())
 * @param string  Password
 */
function loginPWcheckSetNew ($user, $password)
{
	global $login_password_external_new_notamonglast3;
	
	if($login_password_external_new_notamonglast3)
	{
		$hash = getPasswordHash($password);
		
		if(
			$hash == $user['user_password'] ||
			$hash == $user['user_password_1'] ||
			$hash == $user['user_password_2']
		)
		{
			throw new Exception(_h('New password can not be the same as one of the last 3 passwords.'));
		}
	}
}

/**
 * Are we accessing the system from an external or internal address
 * 
 * @uses $_SERVER
 * @return boolean
 */
function isExternal ()
{
		global $login_internal_addresses;
		
		foreach($login_internal_addresses as $addr)
		{
			if($addr == substr($_SERVER['REMOTE_ADDR'], 0, strlen($addr)))
				return false; // Internal
		}
		
		return true; // External
}