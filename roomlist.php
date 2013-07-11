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


//room has been registred before if it was set in _GET
if (!isset($room))
{
	$room = 0;
}
else
{
	$theROOM = getRoom($room);
	if(!count($theROOM))
		$room = 0;
	elseif($theROOM['area_id'] != $area)
		$room = 0;
}

if($room == 0)
	$theROOM = array (
				'room_id'			=> 0,
				'room_name'			=> __('Whole area'),
				'area_id'			=> $area
			);

if (basename($_SERVER['PHP_SELF']) == 'day.php' || basename($_SERVER['PHP_SELF']) == 'day2.php')
	$thisFile = 'day.php';
elseif (basename($_SERVER['PHP_SELF']) == 'month.php')
	$thisFile = 'month.php';
else
	$thisFile = 'week.php';

# Table with areas, rooms, minicals.
?>
<table height="140" width="100%" class="hiddenprint">
    <tr>
<?php
$this_area_name = "";
$this_room_name = "";
$infolink="";
?>
        <!-- All areas -->
        <td width="200">
            <img src="./img/icons/house.png" style="border: 0px solid black; vertical-align: middle;">
                <span style="text-decoration: underline"><?=__("Areas") ?></span><br>
                <?php

$res = mysql_query("select id as area_id, area_name from mrbs_area order by area_name");
if (mysql_num_rows($res)) {
	while($row = mysql_fetch_assoc($res))
	{
        $this_area_name = htmlspecialchars($row['area_name']);
        ?>
            <a href="<?=$thisFile?>?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>&area=<?=$row['area_id']?>"
                <?=($row['area_id'] == $area)?' style="color: red;"':''?>><?=$this_area_name?></a><br>
        <?php
	}
}
        ?>
            <br><br><br>
        </td>

<?php
$cID=0;
?>
    <td width="200">
        <img src="./img/icons/shape_square.png" style="border: 0px solid black; vertical-align: middle;">

        <span style="text-decoration: underline"><?=__("Device")?></span><br>

    <a href="<?=$thisFile?>?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>&area=<?=$area?>&room=0"
        <?=($room == 0)?' style="color: red;"':''?>><?=__('Whole area')?></a><br>
<?php

$i = 1;
$Q_room = mysql_query("SELECT id, room_name FROM mrbs_room WHERE area_id=$area AND hidden='false' ORDER BY room_name");
while($R_room = mysql_fetch_assoc($Q_room))
{
	if ($i>0 && $i%6==0) {
		echo "</td><td width=200><br>";
    }

    $this_room_name = htmlspecialchars($R_room['room_name']);
    ?>
	<a href="<?=$thisFile?>?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>&area=<?=$area?>&room=<?=$R_room['id']?>"
        <?=($R_room['id'] == $room)?' style="color: red;"':''?>><?=$this_room_name?></a><br>

    <?php
	$i++;
}

if($thisFile == 'week.php')
{
	// Headings:
	echo '</td><td style="padding: 10px 10px 10px 10px;">'.chr(10);
	echo '<h1 align=center>'.__('Week').' '.$thisWeek.'</h1>'.chr(10);
	echo '<h3 align=center>'.$this_area_name.' - '.$theROOM['room_name'].'</h3>'.chr(10);
}
elseif($thisFile == 'day.php')
{
	// Headings:
	echo '</td><td style="padding: 10px 10px 10px 10px;">'.chr(10);
	echo '<h1 align=center>'.ucfirst(__(strftime("%A", $am7))).', '.date('j', $am7).'. '.__(strtolower(date('F', $am7))).' '.date('Y', $am7).'</h1>'.chr(10);
	echo '<h3 align=center>'.$this_area_name.' - '.$theROOM['room_name'].'</h3>'.chr(10);
}
elseif($thisFile == 'month.php')
{
	// Headings:
	echo '</td><td style="padding: 10px 10px 10px 10px;">'.chr(10);
	echo '<h1 align=center>'.ucfirst(strtolower(parseDate(strftime("%B %Y", $monthstart)))).'</h1>'.chr(10);
	echo '<h3 align=center>'.$this_area_name.' - '.$theROOM['room_name'].'</h3>'.chr(10);
}

/* ## ADDING CALENDAR ## */
$print_in_top = TRUE;
echo '</td><td align="right">'.chr(10);

include("trailer.inc.php");

echo "</td>\n";
echo "</tr></table>\n";

echo '<table class="print" width="100%">'.chr(10);
echo '<tr><td><b>'.__('Area').':</b> '.$this_area_name.', <b>'.__('Room').':</b> '.$theROOM['room_name'].'</td></tr>'.chr(10);
echo '<tr><td>'.__('Data collected/printed').' '.date('H:i:s d-m-Y').' '.__('by').' '.$login['user_name'].'</td></tr>'.chr(10);

echo '<tr><td>'.__('Type of view').': ';
if($thisFile == 'day.php') {        echo __('day'); }
elseif($thisFile == 'week.php') {   echo __('week'); }
elseif($thisFile == 'month.php') {  echo __('month'); }
else {                              echo __('unknown'); }
echo '</td></tr>'.chr(10);

echo '<tr><td>&nbsp;</td></tr>'.chr(10);

echo '<tr><td>';
if($thisFile == 'week.php')	{       echo '<h1>'.__('Week').' '.$thisWeek.':</h1>'; }
elseif($thisFile == 'day.php') {    echo '<h1>'.parseDate(strftime("%A", $am7)).', '.date('j', $am7). '. '. strtolower(parseDate(strftime("%B %Y", $am7))).':</h1>'; }
elseif($thisFile == 'month.php') {  echo '<h1>'.ucfirst(strtolower(parseDate(strftime("%B %Y", $monthstart)))).':</h1>'.chr(10); }
echo '</td></tr>'.chr(10);

echo '</table>';
