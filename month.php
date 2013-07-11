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
 * JM-booking - Month
 */


include_once("glob_inc.inc.php");

if(isset($_GET['room']))
	$room=(int)$_GET['room'];

# If we don't know the right date then use today:
if (!isset($_GET['day']))
	$day = date('d', time());
if(!isset($_GET['month']) or !isset($_GET['year'])){
	$month = date("m",time());
	$year  = date("Y",time());
}
else {
	# Make the date valid if day is more then number of days in month:
	$day=(int)$_GET['day'];
	$month=(int)$_GET['month'];
	$year=(int)$_GET['year'];
	while (!checkdate($month, $day, $year))
		$day--;
}

# Set the date back to the start of month
$monthstart = mktime(0, 0, 0, $month, 1, $year);

# print the page header
print_header($day, $month, $year, $area);

$selectedType = 'month';
$selected = date('mY', mktime(0, 0, 0, $month, $day, $year));
$thisMonth = $selected;

include "roomlist.php";

$monthTime	= mktime (0, 0, 0, $month, 1, $year);
$monthLast	= mktime (0, 0, 0, ($month+1), 1, $year);
$numDays	= date('t', $monthTime);
$startWeek	= date('W', $monthTime);

$Q_room = mysql_query("select id as room_id, room_name from `mrbs_room` where area_id = '".$area."' and hidden = 'false'");
$rooms = array();
while($R_room = mysql_fetch_assoc($Q_room))
	$rooms[$R_room['room_id']]			= $R_room['room_name'];


echo '<table cellpadding="0" cellspacing="0" border="0" width="100%" class="timetable">'.chr(10);
//echo ' <tr><td class="time3" colspan="9"><center><h2><a class="graybg" href="month.php?year='.$year.'&amp;month='.$month.'&amp;day=1&amp;area='.$area.'&amp;room='.$room.'">';
//echo _(date('M', $monthTime)).' '.$year;
//echo '</h2></center></td>';
//echo '</tr>'.chr(10);

echo ' <tr>'.chr(10);
echo '  <td class="time3"><center>'.__('Week').'</center></td>'.chr(10);
echo '  <td class="time3"><center>'.__('Monday').'</center></td>'.chr(10);
echo '  <td class="time3"><center>'.__('Tuesday').'</center></td>'.chr(10);
echo '  <td class="time3"><center>'.__('Wednesday').'</center></td>'.chr(10);
echo '  <td class="time3"><center>'.__('Thursday').'</center></td>'.chr(10);
echo '  <td class="time3"><center>'.__('Friday').'</center></td>'.chr(10);
echo '  <td class="time3"><center>'.__('Saturday').'</center></td>'.chr(10);
echo '  <td class="time3"><center>'.__('Sunday').'</center></td>'.chr(10);
echo ' </tr>'.chr(10);
$printedWeeks = array();
$firstWeek = true;
for ($i = 1; $i < $numDays + 1; $i++)
{
	
	$thisWeek = date('W', mktime(0, 0, 0, $month, $i, $year));
	// If this week isn't printed, lets print it
	if(!in_array($thisWeek, $printedWeeks))
	{
		if($firstWeek)
		{
			$firstWeek = false;
		}
		else
			echo '    </tr>'.chr(10);
			
		echo '    <tr>'.chr(10);
		echo '     <td class="time3"><center><h2>';
		echo '<a class="graybg" href="week.php?year='.$year.'&amp;month='.$month.'&amp;day='.$i.'&amp;area='.$area.'&amp;room='.$room.'" style="padding: 2px 5px 2px 5px;">';
		echo $thisWeek;
		echo '</a>';
		echo '</h2></center></td>'.chr(10);
		
		// Checking the weekday and adding spaces
		switch (date('w', mktime (0, 0, 0, $month, $i, $year)))
		{
			case '0': // Sunday
				echo '     <td class="time3" style="background-color: grey;">&nbsp;</td>'.chr(10);
			case '6': // Saturday
				echo '     <td class="time3" style="background-color: grey;">&nbsp;</td>'.chr(10);
			case '5': // Friday
				echo '     <td class="time3" style="background-color: grey;">&nbsp;</td>'.chr(10);
			case '4': // Thursday
				echo '     <td class="time3" style="background-color: grey;">&nbsp;</td>'.chr(10);
			case '3': // Wednesday
				echo '     <td class="time3" style="background-color: grey;">&nbsp;</td>'.chr(10);
			case '2': // Tuesday
				echo '     <td class="time3" style="background-color: grey;">&nbsp;</td>'.chr(10);
			case '1': // Mondag, non added
				break;
		}
		
		$printedWeeks[] = $thisWeek;
	}
	
	echo '     <td class="time3">';
	echo '<img src="img/pixel.gif" width="100" height="1">';
	echo '<table width="100%"><tr><td>';
	echo '<center><a href="day.php?year='.$year.'&amp;month='.$month.'&amp;day='.$i.'&amp;area='.$area.'&amp;room='.$room.'">';
	$ymd = $year;
	if(strlen($month) == 1)
		$ymd .= '0';
	$ymd .= $month;
	if(strlen($i) == 1)
		$ymd .= '0';
	$ymd .= $i;
	
	echo $i;
	echo '</a></center>';
	echo '</td></tr>'.chr(10);
	echo '<tr><td>';
	
	$entries = array();
	$timed_entries = array();
	
	if($room != 0)
	{
		$rooms = array();
		$rooms[$room] = getRoom($room);
	}
	foreach ($rooms as $room_id => $room3)
	{
		$start	= mktime(0,0,0,$month,$i,$year);
		$end	= mktime(23,59,59,$month,$i,$year);
		$events_room = checktime_Room ($start, $end, $area, $room_id);
		if(isset($events_room[$room_id]))
		{
			foreach ($events_room[$room_id] as $entry_id)
			{
				$event = getEntry ($entry_id);
				if(count($event))
				{
					/*
					if($event['time_start'] < $start)
					{
						$event['entry_name'] .= ' ('._('started').' '.date('H:i d-m-Y', $event['time_start']).')';
						$event['time_start'] = $start;
					}*/
					$a = '';
					if($event['time_start'] < $start)
					{
						$a .= __('started').' '.date('d-m-Y', $event['time_start']);
						$event['time_start'] = $start;
					}
					if($event['time_end'] > $end)
					{
						if($a != '')
							$a .= ', ';
						$a .= 'slutter '.date('d-m-Y', $event['time_end']);
						$event['time_end'] = $end;
					}
					if($a != '')
						$event['entry_name'] .= ' ('.$a.')';
					//$event['time_start'] = round_t_down($event['time_start'], $resolution);
					$timed_entries[$event['time_start']][$event['entry_id']] = $event['entry_id'];
					$entries[$event['entry_id']] = $event;
				}
			}
		}
	}
	
	$thistime = mktime(0, 0, 0, $month, $i, $year);
	if(count($entries))
	{
		ksort($timed_entries);
		foreach ($timed_entries as $t => $thisentries)
		{
			foreach($thisentries as $entry_id)
			{
				$entry = $entries[$entry_id];
				echo '<table cellpadding="0" cellspacing="0"><tr><td style="font-size: x-small;">';
				echo '<b>';
				if(date('Ymd', $thistime) != date('Ymd', $entry['time_start']))
					//echo ', '.date('d-m-Y', $entry['time_start']);
					echo '00:00';
				else
					echo date('H:i', $entry['time_start']);
				echo '&nbsp;-&nbsp;';
				if(date('Ymd', $thistime) != date('Ymd', $entry['time_end']))
					//echo ', '.date('d-m-Y', $entry['time_end']);
					echo '23:59';
				else
					echo date('H:i', $entry['time_end']);
				echo '</b>&nbsp;';
				echo '</td><td style="font-size: x-small;">';
				echo '<a href="entry.php?entry_id='.$entry['entry_id'].'">'.$entry['entry_name'].'</a>';
				echo '</td></tr></table>'.chr(10);
			}
		}
	}
	else
		echo '&nbsp;';
	echo '</td></tr>'.chr(10);
	echo '</table>'.chr(10);
}

switch (date('w', $thistime))
{
	case '1': // Sunday
		echo '     <td class="time3" style="background-color: grey;">&nbsp;</td>'.chr(10);
	case '2': // Saturday
		echo '     <td class="time3" style="background-color: grey;">&nbsp;</td>'.chr(10);
	case '3': // Friday
		echo '     <td class="time3" style="background-color: grey;">&nbsp;</td>'.chr(10);
	case '4': // Thursday
		echo '     <td class="time3" style="background-color: grey;">&nbsp;</td>'.chr(10);
	case '5': // Wednesday
		echo '     <td class="time3" style="background-color: grey;">&nbsp;</td>'.chr(10);
	case '6': // Tuesday
		echo '     <td class="time3" style="background-color: grey;">&nbsp;</td>'.chr(10);
	case '0': // Mondag, non added
		break;
}
echo ' </tr>'.chr(10);
echo '</table>'.chr(10);