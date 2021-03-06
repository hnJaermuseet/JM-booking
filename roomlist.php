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

function getRoomEventList(array $events_room, $start, $end) {
    $all_entries = array();
    $timed_entries = array();
    foreach ($events_room as $room_id => $entries)
    {
        foreach ($entries as $event)
        {
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
                $all_entries[$event['entry_id']] = $event;
            }
        }
    }
    return array(
        'timedEntries' => $timed_entries,
        'allEntries' => $all_entries
    );
}

function getAllRoomsForAreas (array $areas) {
    $all_rooms = array();

    $area_id_queries = array();
    foreach($areas as $area) {
        $area_id_queries[] = 'area_id = \''.$area['area_id'].'\'';
    }
    $Q_room = db()->prepare('select id as room_id, room_name, area_id from `mrbs_room` where ('.implode(' OR ', $area_id_queries).') AND hidden = \'false\'');
    $Q_room->execute();
    if($Q_room->rowCount() > 0)
    {
        while ($R_room = $Q_room->fetch()) {
            $all_rooms[$R_room['room_id']] = $R_room;
        }
    }

    return $all_rooms;
}

function getRoomIds(array $areas) {
    $room_ids = array();
    if(isset($_GET['room']))
    {
        $split = explode(',', $_GET['room']);
        foreach($split as $room) {
            $room_ids[] = (int)$room;
        }
    }
    else {
        $room_ids[] = 0;
    }


    $wholeAreaRoom = array();
    foreach($areas as $area) {
        if(!isset($wholeAreaRoom[0])) {
            $wholeAreaRoomKey = 0;
        }
        else {
            $wholeAreaRoomKey = 0-$area['area_id'];
        }

        $wholeAreaRoom[$wholeAreaRoomKey] = array (
            'room_id'			=> 0,
            'room_name'			=> __('Whole area'),
            'area_id'			=> $area['area_id']
        );
    }

    $rooms = array();
    foreach($room_ids as $room_id) {

        $theROOM = getRoom($room_id);
        if(!count($theROOM) || !array_key_exists($theROOM['area_id'], $areas)) {
            // -> Room not found OR on a different area
            return $wholeAreaRoom;
        }
        $rooms[$theROOM['room_id']] = $theROOM;
    }

    if(!count($rooms)) {
        return $wholeAreaRoom;
    }

    return $rooms;
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

function getAreaUrlString(array $areas) {
    $area_ids = array();
    foreach($areas as $area) {
        $area_ids[] = $area['area_id'];
    }
    return implode(',', $area_ids);
}

function roomList(array $areas, $areaUrlString, array $rooms, $roomUrlString, $heading, $thisFile, $year, $month, $day, $selectedType, $selected) {
    global $login;

    $room_names = array();
    $allRoomsNameAlreadyAdded = false;
    foreach($rooms as $room) {
        if($room['room_id'] == 0) {
            if($allRoomsNameAlreadyAdded) {
                continue;
            }
            $allRoomsNameAlreadyAdded = true;
        }
        $room_names[] = $room['room_name'];

    }

# Table with areas, rooms, minicals.
?>
<table height="140" width="100%" class="hiddenprint" id="roomlist">
    <tr>
        <?php
        $area_names = array();
        foreach($areas as $area_tmp) {
            $area_names[] = $area_tmp['area_name'];
        }
        $this_area_name = '';
        ?>
        <!-- All areas -->
        <td width="200">
            <img src="./img/icons/house.png" style="border: 0 solid black; vertical-align: middle;" alt="<?=__('Areas') ?>">
            <span style="text-decoration: underline"><?=__("Areas") ?></span><br>
            <?php

            $res = db()->prepare('select id as area_id, area_name from mrbs_area order by area_name');
            $res->execute();
            if ($res->rowCount() > 0) {
                while($row = $res->fetch())
                {
                    $area_name = htmlspecialchars($row['area_name']);
                    $area_selected = (array_key_exists($row['area_id'], $areas));
                    ?>
                        <input type="checkbox" name="roomlist_areaSelector"
                              value="<?=$row['area_id']?>"<?=$area_selected?' checked="checked"':''?>>
                        <a href="<?=$thisFile?>?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>&area=<?=$row['area_id']?>"
                            <?=$area_selected?' style="color: red;"':''?>><?=$area_name?></a><br>
                    <?php
                }
            }
            ?>
            <br><br><br>
        </td>
        <td width="200">
            <?php
            function printRoomSelector($room_id, $room_name) {
                global $thisFile, $year, $month, $day, $areaUrlString, $rooms;
                $room_selected = (array_key_exists($room_id, $rooms));
                ?>
                <input type="checkbox" name="roomlist_roomSelector"
                   value="<?=$room_id?>"<?=$room_selected?' checked="checked"':''?>>
                <a href="<?=$thisFile?>?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>&area=<?=$areaUrlString?>&room=<?=$room_id?>"
                <?=$room_selected?' style="color: red;"':''?>><?=$room_name?></a><br>
                <?php
            }
            ?>
            <img src="./img/icons/shape_square.png" style="border: 0 solid black; vertical-align: middle;" alt="<?=__('Device')?>">

            <span style="text-decoration: underline"><?=__("Device")?></span><br>
            <?php printRoomSelector(0, __('Whole area')) ?>
            <?php

            /*
            $area_ids = array();
            foreach($areas as $area) {
                $area_ids[] = $area['area_id'];
            }
            if (count($area_ids) > 0) {
                $marks = str_repeat("?,", count($area_ids) - 1) . '?';
                $Q_room = db()->prepare('SELECT id, room_name FROM mrbs_room WHERE area_id IN (' . $marks . ') AND hidden=\'false\' ORDER BY room_name');
                $Q_room->execute($area_ids);
            }
            else {
                $Q_room = db()->prepare('SELECT id, room_name FROM mrbs_room WHERE hidden=\'false\' ORDER BY room_name');
                $Q_room->execute();
            }
            $i = 0;
            while($R_room = $Q_room->fetch())
            */
            $i = 1;
            $area_ids = array();
            foreach($areas as $area) {
                $area_ids[] = 'area_id = \''.$area['area_id'].'\'';
            }
            if (count($area_ids)) {
                $Q_room = db()->prepare('SELECT id, room_name FROM mrbs_room WHERE (' . implode(' OR ', $area_ids) . ') AND hidden=\'false\' ORDER BY room_name');
            }
            else {
                $Q_room = db()->prepare('SELECT id, room_name FROM mrbs_room WHERE hidden=\'false\' ORDER BY room_name');
            }
            $Q_room->execute();
            while($R_room = $Q_room->fetch())
            {
                if ($i>0 && $i%6==0) {
                    echo "</td><td width=200><br>";
                }

                $this_room_name = htmlspecialchars($R_room['room_name']);

                printRoomSelector($R_room['id'], $this_room_name);

                $i++;
            }
        ?>
        </td>

        <!-- Headings -->
        <td style="padding: 10px 10px 10px 10px;">
        <h1 align=center><?=$heading?></h1>
        <h3 align=center><?=implode(', ', $area_names).' - '.implode(', ', $room_names) ?></h3>
        <?php

/* ## ADDING CALENDAR ## */
$print_in_top = TRUE;
echo '</td><td align="right">'.chr(10);

include("trailer.inc.php");
printMonths($areaUrlString, $rooms, $roomUrlString, $year, $month, $day, $selected, $selectedType);

echo "</td>\n";
echo "</tr></table>\n";

echo '<table class="print" width="100%">'.chr(10);
echo '<tr><td><b>'.__('Area').':</b> '.implode(', ', $area_names).', <b>'.__('Room').':</b> '.implode(', ', $room_names).'</td></tr>'.chr(10);
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
?>
    <form method="GET" action="<?=$thisFile?>" id="roomlistForm">
        <input type="hidden" name="year"  value="<?=$year?>"          />
        <input type="hidden" name="month" value="<?=$month?>"         />
        <input type="hidden" name="day"   value="<?=$day?>"           />
        <input type="hidden" name="area"  value="<?=$areaUrlString?>" />
        <input type="hidden" name="room"  value="<?=$roomUrlString?>" />
    </form>
<?php
echo '<script type="text/javascript" src="js/entry-overview.js"></script>';

}