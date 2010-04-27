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


// include the authentification wrappers
include "auth_$auth[type].inc.php";

/* getAuthorised($user, $pass, $level)
 * 
 * Check to see if the user name/password is valid
 * 
 * $userid  - The user ID
 * $pass  - The users password
 * $level - The access level required
 * 
 * Returns:
 *   0        - The user does not have the required access
 *   non-zero - The user has the required access
 */
function getAuthorised($userid, $pass, $level){
	global $auth;
	
	echo '<h1>DONT USE THIS FUNCTION!</h1>';
	return 0; // This function is disabled
	
	if(!authValidateUser($userid, $pass))
		return 0;
	
	return authGetUserLevel($userid) >= $level;
}

/* getWritable($creator, $user)
 * 
 * Determines if a user is able to modify an entry
 *
 * $creator - The creator of the entry
 * $user    - Who wants to modify it
 *
 * Returns:
 *   0        - The user does not have the required access
 *   non-zero - The user has the required access
 */
function getWritable($creator, $user){
	global $auth;
	// Always allowed to modify your own stuff
	if($creator == $user)
		return 1;
	
	if(authGetUserLevel($user, $auth["admin"]) >= 2)
		return 1;
	
	// Unathorised access
	return 0;
}

/* showAccessDenied()
 * 
 * Displays an appropate message when access has been denied
 * 
 * Retusns: Nothing
 */
function showAccessDenied($day, $month, $year, $area, $admin)
{
	global $lang,$section;
	
	if($admin)
	{
		require "include/admin_middel.php";
	}
	else
		print_header($day, $month, $year, $area);
	
	echo chr(10).
	'<H1>'._("Access denied").'</H1>'.chr(10).
	'	<P>'.chr(10).
	'		'._("You don't have the neccessary rights to do this action.").chr(10).
	'	</P>'.chr(10).
	'</BODY>'.chr(10).
	'</HTML>';
	exit();
}
?>