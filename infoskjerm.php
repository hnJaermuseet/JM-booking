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

$require_login = FALSE;
require "glob_inc.inc.php";

if(isset($_GET['date']))
{
	$date = getTime ($_GET['date'], array ('d', 'm', 'y'));
	if($date == 0)
		$date = time();
}
else
	$date = time();
$start 	= mktime(16, 0, 0, date('m', $date), date('d', $date), date('Y', $date));
$end 	= mktime(06, 0, 0, date('m', $date), date('d', $date)+1, date('Y', $date));

$default_area = 6;
if(isset($_GET['area']))
{
	$area2 = getArea($_GET['area']);
	if(!count($area2))
		$area = $default_area;
	else
		$area = $area['area_id'];
}
else
{
	$area = $default_area;
	$area2 = getArea($area);
	if(!count($area2))
		$area = 0;
}

echo '<html><head><title>';
if(count($area2))
	echo $area2['area_name'].' - ';
echo date('d.m.Y', $start);
echo '</title></head>';

echo '<body background="img/infoskjerm-bg.png" style="margin: 0px; padding: 0px;">'.chr(10);


$Q_room = mysql_query("select id as room_id, room_name from `mrbs_room` where area_id = '".$area."' and hidden = 'false'");
$rooms = array();
while($R_room = mysql_fetch_assoc($Q_room))
	$rooms[$R_room['room_id']]			= $R_room['room_name'];


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
				$entries[$event['entry_id']] = $event;
				if($event['time_start'] < $start)
				{
					$event['time_start'] = $start;
				}
				$event['time_start'] = round_t_down($event['time_start'], $resolution);
				$timed_entries[$event['time_start']][$event['entry_id']] = $event['entry_id'];
			}
		}
	}
}

echo '<table width="1024" style="border-collapse: collapse;">'.chr(10);
echo ' <tr>'.chr(10);
echo '  <td height="135px">'.chr(10);
echo ' <tr>'.chr(10);
echo '  <td width="160" height="200px">&nbsp;</td>'.chr(10);
echo '  <td align="center" valign="top"><font style="font-size: 110px; font-family: arial;">'.$area2['area_name'].'</font></td>'.chr(10);
echo ' <tr>'.chr(10);
echo '<td width="1000" align="center" colspan="2" height="360px" style="padding: 40px 100px 40px 100px;">';
foreach ($entries as $entry)
{
	if($entry['infoscreen_txt'] != '')
		echo "<font style='font-size: 50px; font-family: arial;'>".$entry['infoscreen_txt']."</font>";
//	elseif($entry['customer_name'] != '')
//		echo "<font style='font-size: 50px; font-family: arial;'>Velkommen ".$entry['customer_name']."</font>";
}

echo '  </td>'.chr(10);
echo ' </tr>'.chr(10);
echo '</table>'.chr(10);

echo '</body>'.chr(10);
echo '</html>'.chr(10);
?>