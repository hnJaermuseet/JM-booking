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


# week.php - Week-at-a-time view

include_once("glob_inc.inc.php");

if(!isset($_GET['area']))
	$area=get_default_area();
else
	$area=(int)$_GET['area'];
if(isset($_GET['room']))
	$room=(int)$_GET['room'];

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

include "roomlist.php";

# Show area and room with extra=0
echo "<h2 align=center>",_("Please select a room"),"</h2><table width=300 cellspacing=1 cellpadding=10 bgcolor=#000000 align=center><tr><td bgcolor=#ffffff>";

$res=sql_query("select r.id, r.room_name,r.infourl,r.infotext from mrbs_room as r left join mrbs_multicat as m ON m.RID=r.ID where m.CID ='$category' AND m.extra=0 AND hidden='false' order by m.uorder");
while($row=mysql_fetch_row($res)){
	echo "<font size=4><li><a href=\"week.php?year=$year&month=$month&day=$day&area=$area&category=$category&room=$row[0]\">$row[1]</a></font>&nbsp;&nbsp;<font size=1>".($row[2]==""?"":"<a href=\"$row[2]\" target=_blank>("._("Info").")</a>")."<br><font size=1><b>$row[3]</b><br><br>";
}
echo "</td></tr></table></td></tr></table>";

# Show area and room with extra=1 if needed

$res=sql_query("select r.id, r.room_name,r.infourl,r.infotext from mrbs_room as r left join mrbs_multicat as m ON m.RID=r.ID where m.CID ='$category' AND m.extra=1 AND hidden='false' order by m.uorder");
if(mysql_num_rows($res)>0){
	echo "<h2 align=center>",_("More rooms"),"</h2><table width=300 cellspacing=1 cellpadding=10 bgcolor=#000000 align=center><tr><td bgcolor=#ffffff>";

	while($row=mysql_fetch_row($res)){
        	echo "<font size=4><li><a href=\"week.php?year=$year&month=$month&day=$day&area=$area&category=$category&room=$row[0]\">$row[1]</a></font>&nbsp;&nbsp;<font size=1>".($row[2]==""?"":"<a href=\"$row[2]\" target=_blank>("._("Info").")</a>")."<br><font size=1><b>$row[3]</b><br><br>";
	}
	echo "</td></tr></table></td></tr></table>";
}
#end of display
include("trailer.inc.php");
?>
