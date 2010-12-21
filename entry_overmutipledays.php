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
 * JM-booking - List of entries over mutiple days
 * Purpose: debugging
 */


include_once("glob_inc.inc.php");

echo '<h1>Entries over mutiple days</h1>';

$Q_entries = mysql_query("select entry_id, time_start, time_end from `entry`");

while($R_entry = mysql_fetch_assoc($Q_entries))
{
	if(($R_entry['time_end'] - $R_entry['time_start']) > 60*60*60*24)
		echo $R_entry['entry_id'];
}


debugPrintLog();