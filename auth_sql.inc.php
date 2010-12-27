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