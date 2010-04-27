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

function entrySetReady ($entry)
{
	if(!count($entry))
	{
		echo _('No entry found.');
		return FALSE;
	}
	
	/*
	 * Set new status
	 * Set new rev_num, time of edit, etc
	 */
	$rev_num = $entry['rev_num']+1;
	mysql_query("UPDATE `entry` SET `invoice_status` = '2', `user_last_edit` = '".$login['user_id']."', `time_last_edit` = '".time()."', `rev_num` = '$rev_num' WHERE `entry_id` = '".$entry['entry_id']."' LIMIT 1 ;");
	
	$log_data = array();
	if(!newEntryLog($entry['entry_id'], 'edit', 'invoice_readyfor', $rev_num, $log_data))
	{
		echo _('Can\'t log the changes for the entry.');
		echo '<br><br>';
		return FALSE;
	}
	
	return TRUE;
}

?>