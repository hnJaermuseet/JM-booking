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

include_once('glob_inc.inc.php');

if(isset($_GET['room']))
{
	$room=(int)$_GET['room'];
	$selected_room = $room;
}

if (!isset($_GET['day']) or !isset($_GET['month']) or !isset($_GET['year'])){
	$day   = date('d',time());
	$month = date('m',time());
	$year  = date('Y',time());
}
else {
	# Make the date valid if day is more then number of days in month:
	$day=(int)$_GET['day'];
	$month=(int)$_GET['month'];
	$year=(int)$_GET['year'];
	while (!checkdate($month, $day, $year))
		$day--;
}

# print the page header
print_header($day, $month, $year, $area);

$am7=mktime($morningstarts,0,0,$month,$day,$year);
$pm7=mktime($eveningends,$eveningends_minutes,0,$month,$day,$year);

include 'roomlist.php';
$heading = ucfirst(__(strftime("%A", $am7))).', '.date('j', $am7).'. '.strtolower(__(date('F', $am7))).' '.date('Y', $am7);
$thisFile = 'day.php';
roomList($area, $room, $heading, $thisFile, $year, $month, $day);

/* ## Tomorrow and yesterday ## */
#y- are year, month and day of yesterday
#t- are year, month and day of tomorrow

$i= mktime(0,0,0,$month,$day-1,$year);
$yy = date('Y',$i);
$ym = date('m',$i);
$yd = date('d',$i);

$i= mktime(0,0,0,$month,$day+1,$year);
$ty = date('Y',$i);
$tm = date('m',$i);
$td = date('d',$i);


/* ## What type of dayview is used? ## */
if(isset($_GET['dayview']) && $_GET['dayview'] == '1') {
	$dayview = 1;
}
else {
	$dayview = 2;
}

/* ## Get rooms ## */
$Q_room = mysql_query("select id as room_id, room_name from `mrbs_room` where area_id = '".$area."' and hidden = 'false'");
if(!mysql_num_rows($Q_room))
{
	echo '<h1>'.__('This area has no rooms').'</h1>';
}
else
{
	$entries_room	= array();
    $room_max_col	= array();
	$room_time		= array(); //  Used to keep track of when an entry starts to display
	$room_time2		= array(); // Used to check if the entry is parallell to an other
	$room_time3		= array(); // Used to keep track of where to put <td> and at what colspan
	$rooms			= array();
	while($R_room = mysql_fetch_assoc($Q_room))
	{
		if($dayview == 1)
		{
			$start	  = mktime(0,0,0,$month,$day,$year);
			$end	  = mktime(23,59,59,$month,$day,$year);
			$am7_tmp  = $am7;
			$pm7_tmp  = $pm7;
			$am7      = $start;
			$pm7      = $end;
		} else {
			$start	= $am7;
			$end	= $pm7;
		}
		
		/* ## Make map of time ## */
		for ($t = $am7; $t <= $pm7; $t += $resolution)
		{
			$room_time[$R_room['room_id']][$t] = array();
			$room_time2[$R_room['room_id']][$t] = array();
			$room_time3[$R_room['room_id']][$t] = array();
		}
		$entries_room[$R_room['room_id']]	= array();
		$room_max_col[$R_room['room_id']]	= 1;
		$rooms[$R_room['room_id']]			= $R_room['room_name'];
		
		if($dayview == 1)
		{
			$am7 = $am7_tmp;
			$pm7 = $pm7_tmp;
		}
		
		$events_room = checktime_Room ($start, $end, $area, $R_room['room_id']);
		if(isset($events_room[$R_room['room_id']]))
		{
			foreach ($events_room[$R_room['room_id']] as $entry_id)
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
						$event['entry_name'] .= ' ('.__('started').' '.date('H:i d-m-Y', $event['time_start_real']).')';
					}
					$event['time_start']	= round_t_down($event['time_start'], $resolution);
					//echo date('H:i:s dmY',$event['time_start']).' start before diff<br>'.chr(10);
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
					if($end < $event['time_end']) {
						$rowspan = (($end + $resolution - $event['time_start'])/$resolution);
                    }
					else {
						$rowspan = (($event['time_end'] - $event['time_start'])/$resolution);
                    }
					$event['rowspan'] = $rowspan;
					$entries_room[$R_room['room_id']][$event['entry_id']] = $event;
					
					// Finding when a <td> with an entry starts
					$room_time[$R_room['room_id']][$event['time_start']][$event['entry_id']] = $event['entry_id'];
					
					// "Mark" all resolutions that this entry is in as "used"
					for ($t = $event['time_start']; $t < $event['time_end'] && $t <= $pm7; $t += $resolution)
					{
						$room_time2[$R_room['room_id']][$t][$event['entry_id']] = $event['entry_id'];
					}
				}
			}
		}
		
		// Finding the max rows to display
		foreach ($room_time2[$R_room['room_id']] as $entries)
		{
			if(count($entries) > $room_max_col[$R_room['room_id']])
			{
				$room_max_col[$R_room['room_id']] = count($entries);
			}
		}
		//echo 'Max: '.$room_max_col[$R_room['room_id']];
		//$room_max_col[$R_room['room_id']];
		
		foreach ($room_time3[$R_room['room_id']] as $t => $array)
		{
			// Fill with all cols
			$room_time3[$R_room['room_id']][$t] = array();
			for ($i = 1; $i <= $room_max_col[$R_room['room_id']]; $i++)
			{
				$room_time3[$R_room['room_id']][$t][$i] = '';
			}
		}
		
		// Fill with entries
		foreach ($room_time[$R_room['room_id']] as $t => $events)
		{
			foreach ($events as $entry_id)
			{
				$entry = $entries_room[$R_room['room_id']][$entry_id];
				//echo '<br>'.$entry['rowspan'].' '.$entry['entry_id'].'<br>';
				$entry_placed = FALSE;
				$i = 1;
				$r = 0;
				$place = $entry['entry_id'];
				while(!$entry_placed)
				{
					$this_t = $entry['time_start'] + ($r * $resolution);
					//echo $this_t.' '.$r.' '.$i;
					if($room_time3[$R_room['room_id']][$this_t][$i] == '')
					{
						$room_time3[$R_room['room_id']][$this_t][$i] = $place;
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
	
	
	echo "<table width=\"100%\" border=\"0\" class=\"hiddenprint\"><tr><td><a href=\"".$_SERVER['PHP_SELF']."?year=$yy&month=$ym&day=$yd&area=$area&room=$room\">&lt;&lt; " . _h('Go to previous day') . "</a></td>
	<td align=center><a href=\"".$_SERVER['PHP_SELF']."?area=$area&amp;room=$room\">" . _h('Go to today') . "</a></td>
	<td align=right><a href=\"".$_SERVER['PHP_SELF']."?year=$ty&amp;month=$tm&amp;day=$td&amp;area=$area&amp;room=$room\">" . _h('Go to next day') . ' &gt;&gt;</a></td></tr></table>';
	
	echo chr(10).chr(10);

    // TODO: put this if in roomlist.php
    if($room != 0)
    {
        $rooms = array();
        $rooms[$room] = getRoom($room);
    }

    $start	= mktime(0,0,0,$month,$day,$year);
    $end	= mktime(23,59,59,$month,$day,$year);
    $events = getRoomEventList($rooms, $start, $end, $area);
	$entries = $events['allEntries'];
	$timed_entries = $events['timedEntries'];


	
	
	if($dayview == 1)
	{
		echo '<span class="hiddenprint"><a href="day.php?day='.$day.'&amp;month='.$month.'&amp;year='.$year.'&amp;area='.$area.'&amp;room='.$room.'">'._h('Go to other dayview').'</a><br></span>';
		
		/* ## START DISPLAYING! ## */
		echo '<table cellpadding="0" cellspacing="0" border="0" width="100%" class="timetable">';
		echo '<tr><th width="1%" class="time3">&nbsp;</th>';
	
		$room_column_width = (int)(95 / mysql_num_rows($Q_room));
		foreach($rooms as $room_id => $room_name) {
			echo '<th width="'.$room_column_width.'%" colspan="'.($room_max_col[$room_id] + 1).'" class="time3">' . htmlspecialchars($room_name). '</th>';
        }
		
		echo '</tr>'.chr(10);
		
		$maxstart  = mktime(0,0,0,$month,$day,$year);
		$maxend    = mktime(23,59,59,$month,$day,$year);
		foreach($entries as $t => $entry)
		{
			if($am7 > $entry['time_start'])
			{
				if($maxstart > $entry['time_start']) {
					$am7 = $maxstart;
                }
				else {
					$am7 = $entry['time_start'];
                }
			}
			if($pm7 < $entry['time_end'])
			{
				if($maxend < $entry['time_end']) {
					$pm7 = $maxend;
                }
				else {
					$pm7 = $entry['time_end'];
                }
			}
		}
		
		for ($t = $am7; $t <= $pm7; $t += $resolution)
		{
			echo '<tr>'.chr(10);
			if($t % (60*60) == 0)
			{
				echo '<td rowspan="4" class="time3">'.chr(10);
				echo date('H:i', $t).'</td>'.chr(10);
			}
			
			// Drawing the rooms
			foreach ($rooms as $room_id => $room_name)
			{
				if($t % (60*60) == 0) {
					$td_style = 'time';
                }
				else {
					$td_style = 'timeweak';
                }
				
				$ignore = array();
				foreach ($room_time3[$room_id][$t] as $b => $i)
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
								if(isset($room_time3[$room_id][$t][$a]) && $room_time3[$room_id][$t][$a] == '')
								{
									$ignore[] = $a;
									$a++;
								}
								else {
									$ok = TRUE;
                                }
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
				
				echo '<td align="right" class="'.$td_style.'2"><img src="img/pixel.gif" width="15" height="16"><table cellpadding="0" cellspacing="0" border="0"><tr>';
				$wday	= date('d',$t);
				$wmonth	= date('m',$t);
				$wyear	= date('Y',$t);
				$hour	= date('H',$t);
				$minute	= date('i',$t);
				$minute2	= (int)($minute + ($resolution / 60));
				echo '</tr></table></td>'.chr(10);
			}
			echo '</tr>'.chr(10);
		}
		echo '</table>';
	}
	else
	{
		echo '<span class="hiddenprint"><a href="day.php?day='.$day.'&amp;month='.$month.'&amp;year='.$year.'&amp;area='.$area.'&amp;room='.$room.'&amp;dayview=1">'._h('Go to other dayview').'</a><br></span>';
		
		echo '<table width="100%" cellspacing="0" style="border-collapse: collapse;">';
		echo '<tr><td class="dayplan"><b>'.__('Time').'</b></td><td class="dayplan"><b>'.__('Room').'</b></td><td class="dayplan"><b>'.__('C/A').'</b></td><td class="dayplan" width="100%"><b>'.__('What').'</b></td></tr>';
		if(!count($entries)) {
			?>
                <tr>
                    <td class="dayplan" style="font-weight: bold;">00:00-23:59</td>
                    <td class="dayplan">&nbsp;</td>
                    <td class="dayplan">&nbsp;</td>
                    <td class="dayplan" style="color: gray; font-style: italic"><?=__('Nothing')?></td>
                </tr>
            <?php
        }
		else
		{
            ksort($timed_entries);
            $last_time = $start;
            foreach ($timed_entries as $t => $thisentries)
			{
				foreach($thisentries as $entry_id)
				{
					if($last_time < $t)
					{
						echo '<tr><td class="dayplan"><b>'.date('H:i', $last_time).'-'.date('H:i', $t).'</b></td><td class="dayplan">&nbsp;</td><td class="dayplan">&nbsp;</td><td class="dayplan"><font color="gray"><i>'.__('Nothing').'</i></font></td></tr>';
					}
					echo '<tr><td class="dayplan"><b>'.date('H:i', $entries[$entry_id]['time_start']).'-'.date('H:i', $entries[$entry_id]['time_end']).'</b></td><td class="dayplan">';
					// Rooms
					$room_name = array();
					if(!count($entries[$entry_id]['room_id'])) {
						echo '<i>'.__('Whole area').'</i>';
                    }
					else
					{
						$Any_rooms = false;
						foreach ($entries[$entry_id]['room_id'] as $rid)
						{
							if($rid != '0')
							{
								$Any_rooms = true;
								$room_tmp = getRoom($rid);
								if(count($room_tmp))
									$room_name[] = $room_tmp['room_name'];
							}
						}
						if(!$Any_rooms) {
							echo '<i>'.str_replace(' ', '&nbsp;', __('Whole area')).'</i>';
                        }
						else {
							echo str_replace(' ', '&nbsp;', implode(', ', $room_name));
                        }
					}
					echo '</td>';
					echo '<td class="dayplan" style="font-size: 10px;">';
					echo $entries[$entry_id]['num_person_child'].'&nbsp;/&nbsp;'.$entries[$entry_id]['num_person_adult'];
					echo '</td>';
					echo '<td class="dayplan"><a href="entry.php?entry_id='.$entry_id.'">'.$entries[$entry_id]['entry_name'].'</a></td></tr>';
					if($last_time < $entries[$entry_id]['time_end']) {
						$last_time = $entries[$entry_id]['time_end'];
                    }
				}
			}
		}
		echo '</table>'.chr(10);
	}
}

include('trailer.inc.php');

?>

<?=debugPrintTimeTotal();?>