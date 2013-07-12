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

function getRoomEventList($rooms, $start, $end, $area) {
    $entries = array();
    $timed_entries = array();
    foreach ($rooms as $room_id => $room)
    {
        $events_room = checktime_Room ($start, $end, $area, $room_id);
        if(isset($events_room[$room_id]))
        {
            foreach ($events_room[$room_id] as $entry_id)
            {
                $event = getEntry ($entry_id);
                if(count($event))
                {
                    $a = '';
                    if($event['time_start'] < $start)
                    {
                        $a .= __('started').' '.date('H:i d-m-Y', $event['time_start']);
                        $event['time_start'] = $start;
                    }
                    if($event['time_end'] > $end)
                    {
                        if($a != '') {
                            $a .= ', ';
                        }
                        $a .= 'slutter '.date('H:i d-m-Y', $event['time_end']);
                        $event['time_end'] = $end;
                    }
                    if($a != '') {
                        $event['entry_name'] .= ' ('.$a.')';
                    };
                    $timed_entries[$event['time_start']][$event['entry_id']] = $event['entry_id'];
                    $entries[$event['entry_id']] = $event;
                }
            }
        }
    }
    return array(
        'timedEntries' => $timed_entries,
        'allEntries' => $entries
    );
}

function getRoomIds($area_id) {
    if(isset($_GET['room']))
    {
        $room = (int)$_GET['room'];
    }
    else {
        $room = 0;
    }

    $wholeAreaRoom = array(0 => array (
        'room_id'			=> 0,
        'room_name'			=> __('Whole area'),
        'area_id'			=> $area_id
    ));

    if($room != 0) {
        // -> Room given AND it is a non int value

        $theROOM = getRoom($room);
        if(!count($theROOM) || $theROOM['area_id'] != $area_id) {
            // -> Room not found OR on a different area
            return $wholeAreaRoom;
        }
        return array($room => $theROOM);
    }
    else {
        return $wholeAreaRoom;
    }
}

/**
 * @param  array[] $rooms   Rooms array (array of arrays), room_id => array('room_id' => 12, 'room_name' => 'Something', (...))
 * @return string    The rooom URL parameter. E.g. "1,14,3"
 */
function getRoomUrlString($rooms) {
    $rooms2 = array();
    foreach($rooms as $room) {
        $rooms2[] = $room['room_id'];
    }
    return implode(',', $rooms2);
}

function roomList($area, $rooms, $roomUrlString, $heading, $thisFile, $year, $month, $day, $selectedType, $selected) {
    global $login;

    $room_names = array();
    foreach($rooms as $room) {
        $room_names[] = $room['room_name'];
    }

# Table with areas, rooms, minicals.
?>
<table height="140" width="100%" class="hiddenprint">
    <tr>
        <?php
        $this_area_name = '';
        ?>
        <!-- All areas -->
        <td width="200">
            <img src="./img/icons/house.png" style="border: 0 solid black; vertical-align: middle;" alt="<?=__('Areas') ?>">
            <span style="text-decoration: underline"><?=__("Areas") ?></span><br>
            <?php

            $res = mysql_query("select id as area_id, area_name from mrbs_area order by area_name");
            if (mysql_num_rows($res)) {
                while($row = mysql_fetch_assoc($res))
                {
                    $area_name = htmlspecialchars($row['area_name']);
                    ?>
                        <a href="<?=$thisFile?>?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>&area=<?=$row['area_id']?>"
                            <?=($row['area_id'] == $area)?' style="color: red;"':''?>><?=$area_name?></a><br>
                    <?php
                    if($row['area_id'] == $area) {
                        $this_area_name = $area_name;
                    }
                }
            }
            ?>
            <br><br><br>
        </td>
        <td width="200">
            <img src="./img/icons/shape_square.png" style="border: 0 solid black; vertical-align: middle;" alt="<?=__('Device')?>">

            <span style="text-decoration: underline"><?=__("Device")?></span><br>

            <a href="<?=$thisFile?>?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>&area=<?=$area?>&room=0"
                <?=(array_key_exists(0, $rooms))?' style="color: red;"':''?>><?=__('Whole area')?></a><br>
            <?php

            $i = 1;
            $Q_room = mysql_query('SELECT id, room_name FROM mrbs_room WHERE area_id="'.$area.'" AND hidden=\'false\' ORDER BY room_name');
            while($R_room = mysql_fetch_assoc($Q_room))
            {
                if ($i>0 && $i%6==0) {
                    echo "</td><td width=200><br>";
                }

                $this_room_name = htmlspecialchars($R_room['room_name']);
                ?>
                <a href="<?=$thisFile?>?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>&area=<?=$area?>&room=<?=$R_room['id']?>"
                    <?=(array_key_exists($R_room['id'], $rooms))?' style="color: red;"':''?>><?=$this_room_name?></a><br>

                <?php
                $i++;
            }
        ?>
        </td>

        <!-- Headings -->
        <td style="padding: 10px 10px 10px 10px;">
        <h1 align=center><?=$heading?></h1>
        <h3 align=center><?=$this_area_name.' - '.implode(', ', $room_names) ?></h3>
        <?php

/* ## ADDING CALENDAR ## */
$print_in_top = TRUE;
echo '</td><td align="right">'.chr(10);

include("trailer.inc.php");
printMonths($area, $rooms, $roomUrlString, $year, $month, $day, $selected, $selectedType);

echo "</td>\n";
echo "</tr></table>\n";

echo '<table class="print" width="100%">'.chr(10);
echo '<tr><td><b>'.__('Area').':</b> '.$this_area_name.', <b>'.__('Room').':</b> '.implode(', ', $room_names).'</td></tr>'.chr(10);
echo '<tr><td>'.__('Data collected/printed').' '.date('H:i:s d-m-Y').' '.__('by').' '.$login['user_name'].'</td></tr>'.chr(10);

echo '<tr><td>'.__('Type of view').': ';
if($thisFile == 'day.php') {        echo __('day'); }
elseif($thisFile == 'week.php') {   echo __('week'); }
elseif($thisFile == 'month.php') {  echo __('month'); }
echo '</td></tr>'.chr(10);

echo '<tr><td>&nbsp;</td></tr>'.chr(10);

echo '<tr><td>';
echo '<h1>'.$heading.':</h1>'.chr(10);
echo '</td></tr>'.chr(10);

echo '</table>';

}