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
	
	// printMonth ($year, $month, $theSelectedDay/Week/Month, $selectedType)
	if(!isset($selectedType))
	{
		$selectedType = 'day';
		$selected = $day;
	}
	if(!isset($selected))
		$selected = 0;
	
	echo '<table><tr><td style="border: 1px solid black;">'.chr(10);
	printMonth ($year, $month, $selected, $selectedType);
	echo '</td><td style="border: 1px solid black;">'.chr(10);
	printMonth ($year, $month + 1, 0);
	echo '</td><td style="border: 1px solid black;">'.chr(10);
	printMonth ($year, $month + 2, 0);
	echo '</td></tr></table>'.chr(10);
	
	/* The old overview */
	/*
	echo "<style>";
	echo ".trail_td1{";
	echo "text-align:center; border-bottom-style:solid;border-bottom-width:1px;";
	echo "border-right-style:solid;border-right-width:1px";
	echo "}";
	echo ".trail_td2{";
	echo "border-left-style:solid;border-left-width:1px;";
	echo "border-top-style:solid;border-top-width:1px;";
	echo "text-align:center; border-bottom-style:solid;border-bottom-width:1px;";
	echo "border-right-style:solid;border-right-width:1px";
	echo "}";
	echo "</style>";
	echo "<br><br><table cellpadding='0' cellspacing='0' width='100%'>";
	echo "<tr><td class='trail_td2'><b>", _("Show day"), "</b></td>";
	if(!isset($year))
		$year = strftime("%Y");

	if(!isset($month))
		$month = strftime("%m");

	if(!isset($day))
		$day = strftime("%d");
	if (empty($area))
		$params = "";
	else
		$params = "&area=$area";

	if (!empty($room)) 
		$params .= "&room=$room";
	
	for($i = -5; $i <= 6; $i++){
		$ctime = mktime(0, 0, 0, $month, $day + $i, $year);
		$str = parseDate(strftime(empty($dateformat)? "%b %d" : "%d %b", $ctime));
		$cyear  = date("Y", $ctime);
		$cmonth = date("m", $ctime);
		$cday   = date("d", $ctime);
		echo "<td class='trail_td1' style='border-top-style:solid;border-top-width:1px;'><a href=\"day.php?year=$cyear&month=$cmonth&day=$cday$params\">$str</a></td>\n";
	}
	echo "</tr><tr><td class='trail_td1' style='border-left-style:solid;border-left-width:1px;'><b>", _("Show week"), "</b></td>";

	$ctime = mktime(0, 0, 0, $month, $day, $year);
	# How many days to skip back to first day of week:
	$skipback = (date("w", $ctime) - $weekstarts + 7) % 7;
	
	for ($i = -5; $i <= 6; $i++){
		$ctime = mktime(0, 0, 0, $month, $day + 7 * $i - $skipback, $year);
		$str = parseDate(strftime(empty($dateformat)? "%b %d" : "%d %b", $ctime));
		$cday   = date("d", $ctime);
		$cmonth = date("m", $ctime);
		$cyear  = date("Y", $ctime);
		echo "<td class='trail_td1'><a href=\"week.php?year=$cyear&month=$cmonth&day=$cday$params\">$str</a></td>\n";
	}
	echo "<tr><td class='trail_td1' style='border-left-style:solid;border-left-width:1px;'><b>", _("Show month"), "</b></td>";
	for ($i = -3; $i <= 8; $i++){
		$ctime = mktime(0, 0, 0, $month + $i, 1, $year);
		$str = parseDate(strftime("%b %Y", $ctime));
		$str = ereg_replace(" ","&#160;",$str);
		$cmonth = date("m", $ctime);
		$cyear  = date("Y", $ctime);
		echo "<td class='trail_td1'><a href=\"week.php?day=1&year=$cyear&month=$cmonth$params\">$str</a></td>\n";
	}
	echo "</tr></table>";
	*/
}

if(!isset($print_in_top))
{
/*
echo '
<div style="margin-top: 50px; width: 100%; text-align: center; background-color: #c0e0ff; border: 1px solid #5b69a6;">
  '; 
	echo '<b>JM-booking (ikke utgitt, til intern bruk - med andre ord ingen GPL free software her...)</b><br>';
  	if ($version!="unknown") printf(_("This page was created by ARBS Version %s, a free software under the GPL license."), $version);
  	else echo _("This page was created by ARBS, a free software under the GPL license.");
  echo '
</div>';
*/
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