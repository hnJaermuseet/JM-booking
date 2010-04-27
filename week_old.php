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
	JM-booking - week display
*/

include_once("glob_inc.inc.php");

if(!isset($_GET['area']))
	$area=get_default_area();
else
	$area=(int)$_GET['area'];

if(isset($_GET['room']))
{
	$room=(int)$_GET['room'];
	$selected_room = $room;
}

if(!isset($_GET['timetohighlight']))
	$timetohighlight=(int)0;
else
	$timetohighlight=(int)$_GET['timetohighlight'];

// Got a week
if (isset($_GET['week']) && isset($_GET['year']))
{
	$_GET['week'] = (int)$_GET['week'];
	$_GET['year'] = (int)$_GET['year'];
	
	$thistime = mktime(0,0,0,1,1,$_GET['year']) + (60*60*24*7*($_GET['week']-1));
	$thisweek = date('W', $thistime);
	// Search for the right date...
	while($thisweek != $_GET['week'])
	{
		
		echo $thisweek.' - ';
		$thistime = $thistime + (60*60*24); // add one day
		$thisweek = date('W', $thistime);
	}
	$_GET['day']	= date('d', $thistime);
	$_GET['month']	= date('m', $thistime);
	$_GET['year']	= date('Y', $thistime);
}

# If we don't know the right date then use today:
if (!isset($_GET['day']) or !isset($_GET['month']) or !isset($_GET['year'])){
	$day   = date("d",time());
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

# Set the date back to the previous $weekstarts day (Sunday, if 0):
$time = mktime(0, 0, 0, $month, $day, $year);
$weekday = (date("w", $time) - $weekstarts + 7) % 7;
if ($weekday > 0){
	$timeNew = $time - $weekday * 86400;
	$time=$timeNew;
	$day   = date("d", $timeNew);
	$month = date("m", $timeNew);
	$year  = date("Y", $timeNew);
}



# print the page header
print_header($day, $month, $year, $area);

# Define the start of day and end of day (default is 7-7)
$am7=mktime($morningstarts,0,0,$month,$day,$year);
$pm7=mktime($eveningends,$eveningends_minutes,0,$month,$day,$year);

# Start and end of week:
$week_midnight = mktime(0, 0, 0, $month, $day, $year);
$week_start = $am7;
$week_end = mktime($eveningends, $eveningends_minutes, 0, $month, $day+6, $year);

$selectedType = 'week';
$selected = date('W', mktime(0, 0, 0, $month, $day, $year));
$thisWeek = $selected;


include "roomlist.php";

//this usually happens when the user changes the instance in another browser window or if the session was lost
if(!isset($room_capacity[$room])){
	echo _("This room couldn't be found."), "<br/>", $startpage;
	exit;
}


#show room comment if there is one
if($room_comment[$room]!="")
{
	echo "<table align=center cellspacing=2 cellpadding=0 bgcolor=#ff0000><tr><td><table width=100% bgcolor=#ffffff cellspacing=0 cellpadding=4><tr><td>",$room_comment[$room],"</td></tr></table></td></tr></table>";
}

#y? are year, month and day of the previous week.
#t? are year, month and day of the next week.

$i= mktime(0,0,0,$month,$day-7,$year);
$yy = date("Y",$i);
$ym = date("m",$i);
$yd = date("d",$i);

$i= mktime(0,0,0,$month,$day+7,$year);
$ty = date("Y",$i);
$tm = date("m",$i);
$td = date("d",$i);

#Show Go to week before and after links
echo '<table width=\"100%\"><tr><td>';
echo '<a href=\"week.php?year=$yy&month=$ym&day=$yd&area=$area&room=$room\">&lt;&lt; '.
_("go to last week").
	"</a></td><td align=center><a href=\"week.php?area=$area&room=$room\">",
	_("go to this week"),
	"</a></td><td align=right><a href=\"week.php?year=$ty&month=$tm&day=$td&area=$area&room=$room\">",
	_("go to next week"),
	"&gt;&gt;</a></td></tr></table>";


$first_slot = $morningstarts * 3600 / $resolution;
$last_slot = ($eveningends * 3600 + $eveningends_minutes * 60) / $resolution;

$dformat = "%A<br>%d. %B";

// Getting entries
$entries_room	= array();
$room_max_rows	= array();
$room_time		= array(); //  Used to keep track of when an entry starts to display
$room_time2		= array(); // Used to check if the entry is parallell to an other
$room_time3		= array(); // Used to keep track of where to put <td> and at what colspan
$rooms			= array();

for ($t = $week_start; $t < $week_end; $t += 86400)
{
	/* ## Make map of time ## */
	$am7=mktime($morningstarts,0,0,date('m', $t),date('d', $t),date('Y', $t));
	$pm7=mktime($eveningends,$eveningends_minutes,0,date('m', $t),date('d', $t),date('Y', $t));
	for ($i = $am7; $i <= $pm7; $i += $resolution)
	{
		$room_time[$t][date('Hi',$i)]	= array();
		$room_time2[$t][date('Hi',$i)]	= array();
		$room_time3[$t][date('Hi',$i)]	= array();
	}
	$entries_room[$room]	= array(); // Reset array
	$room_max_col[$t]	= 1;
	$rooms[$t]			= ucfirst(strtolower(parseDate(strftime($dformat, $t))));
	
	$start	= $am7;
	$end	= $pm7;
	$events_room = checktime_Room ($start, $end, $area, $room);
	if(isset($events_room[$room]))
	{
		foreach ($events_room[$room] as $entry_id)
		{
			// Fixing time for this event
			$event = getEntry ($entry_id);
			if(count($event))
			{
				//echo '<b>'.date('H:i:s dmY',$event['time_start']).'</b> start<br>'.chr(10);
				//echo '<b>'.date('H:i:s dmY',$event['time_end']).'</b> end<br>'.chr(10);
				// Saving originals
				$event['time_start_real']	= $event['time_start'];
				$event['time_end_real']		= $event['time_end'];
				
				if($event['time_start'] < $start)
				{
					$event['time_start'] = $start;
					$event['entry_name'] .= ' ('._('started').' '.date('H:i d-m-Y', $event['time_start_real']).')';
				}
				$event['time_start']	= round_t_down($event['time_start'], $resolution);
				//echo date('H:i:s dmY',$event['time_start']).' start før diff<br>'.chr(10);
				$diff = $event['time_end'] - $event['time_start'];
				//echo $diff.' '.($diff/60).'<br>'.chr(10);
				if($diff < (60 * 30))
				{
					$event['time_end'] = round_t_up($event['time_end'], (60*15));
				}
				//echo date('H:i:s dmY',$event['time_start']).' start last<br>'.chr(10);
				$event['time_end']		= round_t_up ($event['time_end'], $resolution);
				//echo date('H:i:s dmY',$event['time_end']).' end last<br>'.chr(10);
				//echo '<br><br>'.chr(10);
				if($end < $event['time_end'])
						$rowspan = (($end + $resolution - $event['time_start'])/$resolution);
					else
						$rowspan = (($event['time_end'] - $event['time_start'])/$resolution);
				$event['rowspan'] = $rowspan;
				$entries_room[$t][$event['entry_id']] = $event;
				
				// Finding when a <td> with an entry starts
				$room_time[$t][date('Hi',$event['time_start'])][$event['entry_id']] = $event['entry_id'];
				
				// "Mark" all resolutions that this entry is in as "used"
				for ($z = $event['time_start']; $z < $event['time_end']; $z += $resolution)
					$room_time2[$t][date('Hi',$z)][$event['entry_id']] = $event['entry_id'];
			}
		}
	}
	
	// Finding the max rows to display
	foreach ($room_time2[$t] as $entries)
	{
		if(count($entries) > $room_max_col[$t])
		{
			$room_max_col[$t] = count($entries);
		}
	}
	
	foreach ($room_time3[$t] as $s => $array)
	{
		// Fill with all cols
		$room_time3[$t][$s] = array();
		for ($i = 1; $i <= $room_max_col[$t]; $i++)
		{
			$room_time3[$t][$s][$i] = '';
		}
	}
	
	// Fill with entries
	foreach ($room_time[$t] as $s => $events)
	{
		foreach ($events as $entry_id)
		{
			$entry = $entries_room[$t][$entry_id];
			//echo '<br>'.$entry['rowspan'].' '.$entry['entry_id'].'<br>';
			$entry_placed = FALSE;
			$i = 1;
			$r = 0;
			$place = $entry['entry_id'];
			while(!$entry_placed)
			{
				$this_t = $entry['time_start'] + ($r * $resolution);
				//echo $this_t.' '.$r.' '.$i;
				if(isset($room_time3[$t][date('Hi',$this_t)][$i]) && $room_time3[$t][date('Hi',$this_t)][$i] == '')
				{
					$room_time3[$t][date('Hi',$this_t)][$i] = $place;
					$place = 'entry';
					$r++;
					//echo ' ok';
				}
				//else
				//	echo ' opptatt';
				
				if($r >= $entry['rowspan'])
				{
					$entry_placed = TRUE;
					//echo ' (alle '.$entry['rowspan'].' plassert)';
				}
				
				if($place != 'entry')
					$i++;
				
				//echo '<br>'.chr(10);
			}
		}
	}
}


echo chr(10).chr(10);
echo '<form id="bookingRadios" method="GET" action="edit_entry2.php">'.chr(10);

/* ## START DISPLAYING! ## */
echo '<table cellpadding="0" cellspacing="0" border="0" width="100%" class="timetable">';
echo "<tr><th width=\"1%\" class=\"time3\">&nbsp;</th>";

$room_column_width = (int)(95 / count($rooms));
foreach($rooms as $room_id => $room_name)
	echo '<th width="'.$room_column_width.'%" colspan="'.($room_max_col[$room_id] + 1).'" class="time3">'.str_replace(' ', '&nbsp;', $room_name)."</th>";

echo "</tr>\n";

# This is the main bit of the display. Outer loop is for the time slots,
# inner loop is for days of the week.

# $t is the date/time for the first day of the week (Sunday, if $weekstarts=0).

for ($t = $am7; $t <= $pm7; $t += $resolution)
{
	echo '<tr>'.chr(10);
	if($t % (60*60) == 0)
	{
		echo '<td rowspan="4" class="time3">'.chr(10);
		echo date("H:i", $t).'</td>'.chr(10);
	}
	
	// Drawing the rooms
	foreach ($rooms as $room_id => $room_name)
	{
		/*
		$this_colspan = $room_max_col[$room_id] - 1;
		if(count($room_time[$room_id][$t]))
		{
			foreach($room_time[$room_id][$t] as $entry_id)
			{
				$entry = $entries_room[$room_id][$entry_id];
				// Starting a entry
				echo '<td bgcolor="red" rowspan="'.$entry['rowspan'].'">'.$entry['entry_name'].'</td>'.chr(10);
			}
		}
		$this_colspan -= count($room_time2[$room_id][$t]);
		if($this_colspan > 0)
			echo '<td colspan="'.$this_colspan.'">&nbsp;</td>';
		*/
		
		if($t % (60*60) == 0)
			$td_style = 'time';
		else
			$td_style = 'timeweak';
		
		$ignore = array();
		foreach ($room_time3[$room_id][date('Hi',$t)] as $b => $i)
		{
			if(!in_array($b, $ignore))
			{
				if($i == '')
				{
					echo '<td class="'.$td_style.'"';
					$ok = FALSE;
					$a = $b + 1;
					while(!$ok)
					{
						if(isset($room_time3[$room_id][date('Hi',$t)][$a]) && $room_time3[$room_id][date('Hi',$t)][$a] == '')
						{
							$ignore[] = $a;
							$a++;
						}
						else
							$ok = TRUE;
					}
					echo ' colspan="'.($a - $b).'"';
					echo '>&nbsp;</td>'.chr(10); // Empty
				}
				elseif(is_numeric($i))
				{
					echo '<td bgcolor="lightgreen" rowspan="'.$entries_room[$room_id][$i]['rowspan'].'" class="event">'.
					'<a class="eventlink" href="entry.php?entry_id='.$i.'">'.
					$entries_room[$room_id][$i]['entry_name'].
					'</a></td>'.chr(10);
				}
				elseif($i == 'entry')
				{
					// Ignore, (entry already started)
				}
			}
		}
		
		echo '<td align="right" class="'.$td_style.'2"><img src="img/pixel.gif" width="15" height="1"><table cellpadding="0" cellspacing="0" border="0"><tr>';
		$wday	= date("d",$t);
		$wmonth	= date("m",$t);
		$wyear	= date("Y",$t);
		$hour	= date("H",$t);
		$minute	= date("i",$t);
		$minute2	= (int)($minute + ($resolution / 60));
		echo '<td><input type="radio" name="starttime" value="'.$wyear.';'.$wmonth.';'.$wday.';'.$hour.';'.$minute.';'.$room.';week"></td>';
		echo '<td><input type="radio" name="endtime" value="'.$wyear.';'.$wmonth.';'.$wday.';'.$hour.';'.$minute2.';'.$room.';week"></td>';
		echo "<td><a href=\"edit_entry2.php?view=day&amp;room=$room&amp;hour=$hour&amp;minute=$minute&amp;year=$wyear&amp;month=$wmonth"
		. "&amp;day=$wday\"><img src=\"img/new.gif\" width=\"10\" height=\"10\" border=\"0\" alt=\"\">&nbsp;</a></td>";
		echo '</tr></table></td>'.chr(10);
	}
	echo '</tr>'.chr(10);
}
echo "</table>";
echo '<input type="submit" value="'._('Make entry').'"><br><br>'.chr(10);
echo '</form>'.chr(10);

show_colour_key();
include("trailer.inc.php");