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

if (!isset($printed_in_top))
{
	/* New overview of dates */
	
	/*
		This one will display
		- current month and the 2 next months
		- Links to switch months
		
		Current month is set by the date currently selected
	*/
	
	if(!isset($year))
		$year = date('Y');
	if(!isset($month))
		$month = date('m');
	if(!isset($day))
		$day = date('d');

	if(!isset($selectedType))
	{
		$selectedType = 'day';
		$selected = $day;
	}
	if(!isset($selected))
		$selected = 0;

    // TODO: wrap in a function to isolate and contain variables
	echo '<table><tr><td style="border: 1px solid black;">'.chr(10);
	printMonth ($area, $rooms, $roomUrlString, $year, $month, $selected, $selectedType);
	echo '</td><td style="border: 1px solid black;">'.chr(10);
	printMonth ($area, $rooms, $roomUrlString, $year, $month + 1, 0);
	echo '</td><td style="border: 1px solid black;">'.chr(10);
	printMonth ($area, $rooms, $roomUrlString, $year, $month + 2, 0);
	echo '</td></tr></table>'.chr(10);

}

if(!isset($print_in_top))
{
echo '
</BODY>
</HTML>
';
}
else
{
	unset($print_in_top);
	$printed_in_top = TRUE;
}