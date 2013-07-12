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

$supportMultipleAreas = true;
include_once('glob_inc.inc.php');

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
		$thistime = $thistime + (60*60*24); // add one day
		$thisweek = date('W', $thistime);
	}
	$_GET['day']	= date('d', $thistime);
	$_GET['month']	= date('m', $thistime);
	$_GET['year']	= date('Y', $thistime);
}

# If we don't know the right date then use today:
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

# Set the date back to the previous $weekstarts day (Sunday, if 0):
$time = mktime(0, 0, 0, $month, $day, $year);
$weekday = (date('w', $time) - $weekstarts + 7) % 7;
if ($weekday > 0){
	$timeNew = $time - $weekday * 86400;
	$time=$timeNew;
	$day   = date('d', $timeNew);
	$month = date('m', $timeNew);
	$year  = date('Y', $timeNew);
}

# print the page header
print_header($day, $month, $year, $area);

# Start and end of week:
$week_midnight = mktime(0, 0, 0, $month, $day, $year);
$week_start = $time;
$week_end = mktime(23, 59, 59, $month, $day+6, $year);


$selectedType = 'week';
$selected = date('W', mktime(0, 0, 0, $month, $day, $year));
$thisWeek = $selected;


include 'roomlist.php';
$heading = __('Week').' '.$thisWeek;
$thisFile = 'week.php';
$areaUrlString = getAreaUrlString($areas);
$rooms = getRoomIds($areas);
$roomUrlString = getRoomUrlString($rooms);
roomList($areas, $areaUrlString, $rooms, $roomUrlString, $heading, $thisFile, $year, $month, $day, $selectedType, $selected);

#y? are year, month and day of the previous week.
#t? are year, month and day of the next week.

$i= mktime(0,0,0,$month,$day-7,$year);
$yy = date('Y',$i);
$ym = date('m',$i);
$yd = date('d',$i);

$i= mktime(0,0,0,$month,$day+7,$year);
$ty = date('Y',$i);
$tm = date('m',$i);
$td = date('d',$i);

#Show Go to week before and after links
?>
<table width="100%" class="hiddenprint"><tr><td>
<a href="week.php?year=<?=$yy?>&month=<?=$ym?>&day=<?=$yd?>&area=<?=$areaUrlString?>&room=<?=$room?>">&lt;&lt;
<?=__('go to last week')?>
	</a></td><td align=center><a href="week.php?area=<?=$areaUrlString?>&room=<?=$room?>"><?=__('go to this week')?>
	</a></td><td align=right><a href="week.php?year=<?=$ty?>&month=<?=$tm?>&day=<?=$td?>&area=<?=$areaUrlString?>&room=<?=$room?>">
<?=__('go to next week')?>
	&gt;&gt;</a></td></tr></table>
<?php
$weekdays = array();
$daystart = $week_start;
$i = 1;
while(date('W', $daystart) == date('W', $week_start))
{
	$weekdays[$i] = $daystart;
	$daystart += 86400;
	$i++;
}

?>
<table width="100%">

<?php
foreach ($weekdays as $daynum => $weekday)
{
    ?>
    <tr>
    <?php
	if($daynum == 6 || $daynum == 7) {
		echo ' <td style="background-color: #FFFFCC;">'.chr(10);
    }
	else {
		echo ' <td>'.chr(10);
    }
	echo '<a class="graybg" href="day.php?year='.date('Y',$weekday).'&amp;month='.date('m',$weekday).'&amp;day='.date('d',$weekday).'&amp;area='.$areaUrlString.'&amp;room='.$roomUrlString.'">';
	echo '<b>'.__(strftime('%A', $weekday)).'</b>';
	echo '<br>'. date('j', $weekday).'. '.strtolower(__(date('F', $weekday)));
	echo '</td>'.chr(10);
	if($daynum == 6 || $daynum == 7) {
		?>
        <td style="background-color: #FFFFCC;">
        <?php
    }
	else {
		?>
        <td>
        <?php
    }
    $start	= mktime(0, 0, 0, date('m', $weekday), date('d', $weekday), date('Y', $weekday));
    $end	= mktime(23, 59, 59, date('m', $weekday), date('d', $weekday), date('Y', $weekday));
    $events = getRoomEventList($rooms, $start, $end);
    ?>
	<table width="100%" cellspacing="0" style="border-collapse: collapse;">
	<tr>
        <td class="dayplan" style="font-weight: bold;"><?=__('Time')?></td>
        <td class="dayplan" style="font-weight: bold;"><?=__('Room')?></td>
        <td class="dayplan" style="font-weight: bold;"><?=__('C/A')?></td>
        <td class="dayplan" style="font-weight: bold;" width="100%"><?=__('What')?></td>
    </tr>
    <?php
	if(!count($events['allEntries'])) {
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

        printWeekdayWithEntries($events['timedEntries'], $events['allEntries'], $start);
	}
    ?>
	</table>
	</td>
	</tr>
	<tr><td colspan="2"><hr></td></tr>
    <?php
}

function printWeekdayWithEntries($timed_entries, $entries, $start) {
    ksort($timed_entries);
    $last_time = $start;
    foreach ($timed_entries as $t => $thisentries)
    {
        foreach($thisentries as $entry_id)
        {
            if($last_time < $t)
            {
                ?>
                <tr>
                    <td class="dayplan" style="font-weight: bold;"><?=date('H:i', $last_time).'-'.date('H:i', $t)?></td>
                    <td class="dayplan">&nbsp;</td>
                    <td class="dayplan">&nbsp;</td>
                    <td class="dayplan" style="color: gray; font-style: italic;"><?=__('Nothing')?></td>
                </tr>
            <?php
            }
            ?>
            <tr>
                <td class="dayplan"><b><?=date('H:i', $entries[$entry_id]['time_start']).'-'.date('H:i', $entries[$entry_id]['time_end'])?></b></td>
                <td class="dayplan">
                    <?php
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
                                    $room_name[] = str_replace(' ', '&nbsp;', $room_tmp['room_name']);
                            }
                        }
                        if(!$Any_rooms)
                            echo '<i>'.str_replace(' ', '&nbsp;', __('Whole area')).'</i>';
                        else
                            echo implode(', ', $room_name);
                    }
                    ?>
                </td>
                <td class="dayplan" style="font-size: 10px"><?php
                    echo $entries[$entry_id]['num_person_child'].'&nbsp;/&nbsp;'.$entries[$entry_id]['num_person_adult'];
                    ?></td>
                <td class="dayplan"><a href="entry.php?entry_id='.$entry_id.'"><?=$entries[$entry_id]['entry_name']?></a></td>
            </tr>
            <?php
            if($last_time < $entries[$entry_id]['time_end']) {
                $last_time = $entries[$entry_id]['time_end'];
            }
        }
    }
}
?>
</table>
<?=debugPrintTimeTotal();?>