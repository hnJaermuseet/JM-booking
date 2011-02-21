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

// Include the datagetting (also used by charts.php)
require "include/entry_stat.php";

echo '<script src="js/jquery-1.3.2.js" type="text/javascript"></script>';
echo '<script src="js/jquery.blockUI.js" type="text/javascript"></script>';
echo '<script src="js/entry-stat.js" type="text/javascript"></script>';

echo '<div class="hiddenprint">';
echo '<a href="export_excel.php?filters='.filterSerialized($filters).'">'.
	iconFiletype('xls').
	' Eksporter til Excel</a><br><br>';
echo '</div>'.chr(10);
echo '<div style="display: none;" id="filters">'.filterSerialized($filters).'</div>';

echo '<div class="hiddenprint"><h2>Kommunefordelt</h2>';
echo '<a id="switchlink1" href="javascript:switchView(1, \'kommunefordelt\');">Vis kommunefordelt</a><br><br>';
echo '</div>';
echo '<div style="display:none;" id="switch1">';
//echo '	<h2 class="print">Kommunefordelt</h2>';
echo '	<table cellspacing="0" style="border-collapse: collapse;">'.chr(10);
echo '		<caption>Kommunefordelt statistikk</caption>'.chr(10);
echo '		<tr>'.chr(10);
echo '			<td class="border">'._('Municipal').'</th>'.chr(10);
echo '			<th class="border" headers="children">'.
'<img src="./img/icons/user_small.png" style="border: 0px solid black; vertical-align: middle;"> '.
_('Children').'</th>'.chr(10);
echo '			<th class="border" headers="adults">'.
'<img src="./img/icons/user_suit.png" style="border: 0px solid black; vertical-align: middle;"> '.
_('Adults').'</th>'.chr(10);
echo '			<th class="border">'._('Total').'</th>'.chr(10);
echo '			<th class="border">'.
'<img src="./img/icons/page_white.png" style="border: 0px solid black; vertical-align: middle;"> '.
_('Entries').'</th>'.chr(10);
echo '		</tr>'.chr(10);
foreach ($municipals3 as $mun_num => $name)
{
	if($mun_num == 0)
		$name = '<i>'._('Non').'</i>';
	$valarray = $municipals2[$mun_num];
	echo '		<tr>'.chr(10);
	echo '			<th class="border" headers="types">'.$name.'</th>'.chr(10);
	echo '			<td class="border" headers="children">'.$valarray['c'].'</td>'.chr(10);
	echo '			<td class="border" headers="adults">'.$valarray['a'].'</td>'.chr(10);
	echo '			<td class="border">'.$valarray['p'].'</td>'.chr(10);
	echo '			<td class="border">'.$valarray['e'].'</td>'.chr(10);
	echo '		</tr>'.chr(10);
}
echo '		<tr>'.chr(10);
echo '			<th class="border">'._('Total').'</th>'.chr(10);
echo '			<td class="border">'.$sum['c'].'</td>'.chr(10);
echo '			<td class="border">'.$sum['a'].'</td>'.chr(10);
echo '			<td class="border">'.$sum['p'].'</td>'.chr(10);
echo '			<td class="border">'.$sum['e'].'</td>'.chr(10);
echo '		</tr>'.chr(10);
echo '	</table>'.chr(10);
echo '<div class="chartplaceholder" id="municipal-children">Trykk her for å hente graf:<br>'.
	'kommunefordelt, antall barn</div>'.chr(10);
echo '<div class="chartplaceholder" id="municipal-people">Trykk her for å hente graf:<br>'.
	'kommunefordelt, antall barn og voksne</div>'.chr(10);
echo '<div class="chartplaceholder" id="municipal-entries">Trykk her for å hente graf:<br>'.
	'kommunefordelt, antall bookinger</div>'.chr(10);
echo '</div>'.chr(10).chr(10);



/* ## STATS - ENTRYTYPES ## */
echo '<div class="hiddenprint"><h2>Typefordelt</h2>';
echo '<a id="switchlink2" href="javascript:switchView(2, \'typefordelt\');">Vis typefordelt</a><br><br>';
echo '</div>';
echo '<div style="display:none;" id="switch2">';
echo '	<table cellspacing="0" style="border-collapse: collapse;">'.chr(10);
echo '		<caption>Typefordelt statistikk</caption>';
echo '		<tr>'.chr(10);
echo '			<th class="border">'._('Entry type').'</th>'.chr(10);
echo '			<th class="border">'.
'<img src="./img/icons/user_small.png" style="border: 0px solid black; vertical-align: middle;"> '.
_('Children').'</th>'.chr(10);
echo '			<th class="border">'.
'<img src="./img/icons/user_suit.png" style="border: 0px solid black; vertical-align: middle;"> '.
_('Adults').'</th>'.chr(10);
echo '			<th class="border">'._('Total').'</th>'.chr(10);
echo '			<th class="border">'.
'<img src="./img/icons/page_white.png" style="border: 0px solid black; vertical-align: middle;"> '.
_('Entries').'</th>'.chr(10);
echo '		</tr>'.chr(10);
foreach ($entrytypes2 as $id => $name)
{
	if(substr($id, 0, 2) != 'dn' && $id == 0)
		$name = '<i>'._('Non').'</i>';
	$valarray = $entrytypes[$id];
	echo '		<tr>'.chr(10);
	echo '			<th class="border">'.$name.'</th>'.chr(10);
	echo '			<td class="border">'.$valarray['c'].'</td>'.chr(10);
	echo '			<td class="border">'.$valarray['a'].'</td>'.chr(10);
	echo '			<td class="border">'.$valarray['p'].'</td>'.chr(10);
	echo '			<td class="border">'.$valarray['e'].'</td>'.chr(10);
	echo '		</tr>'.chr(10);
}
echo '		<tr>'.chr(10);
echo '			<th class="border">'._('Total').'</th>'.chr(10);
echo '			<td class="border">'.$sum['c'].'</td>'.chr(10);
echo '			<td class="border">'.$sum['a'].'</td>'.chr(10);
echo '			<td class="border">'.$sum['p'].'</td>'.chr(10);
echo '			<td class="border">'.$sum['e'].'</td>'.chr(10);
echo '		</tr>'.chr(10);
echo '	</table>'.chr(10);
echo '<div class="chartplaceholder" id="entrytype-people">Trykk her for å hente graf:<br>'.
	'typefordelt, antall barn og voksne</div>'.chr(10);
echo '<div class="chartplaceholder" id="entrytype-entries">Trykk her for å hente graf:<br>'.
	'typefordelt, antall bookinger</div>'.chr(10);
echo '</div>'.chr(10).chr(10);



/* ## STATS - DAYS ## */
echo '<div class="hiddenprint"><h2>Dagsfordelt</h2>';
echo '<a id="switchlink3" href="javascript:switchView(3, \'dagsfordelt\');">Vis dagsfordelt</a><br><br>';
echo '</div>';
echo '<div style="display:none;" id="switch3">';
echo '	<table cellspacing="0" style="border-collapse: collapse;">'.chr(10);
echo '		<caption>Dagsfordelt statistikk</caption>'.chr(10);
echo '		<tr>'.chr(10);
echo '			<th class="border">Dagsfordelt</th>'.chr(10);
echo '			<th class="border">'.
'<img src="./img/icons/user_small.png" style="border: 0px solid black; vertical-align: middle;"> '.
_('Children').'</th>'.chr(10);
echo '			<th class="border">'.
'<img src="./img/icons/user_suit.png" style="border: 0px solid black; vertical-align: middle;"> '.
_('Adults').'</th>'.chr(10);
echo '			<th class="border">'._('Total').'</th>'.chr(10);
echo '			<th class="border">'.
'<img src="./img/icons/page_white.png" style="border: 0px solid black; vertical-align: middle;"> '.
_('Entries').'</th>'.chr(10);
echo '		</tr>'.chr(10);
foreach ($stats_day as $id => $valarray)
{
	if($id == 0)
		$name = '<i>'._('Non').'</i>';
	else
		$name = $valarray['Name'];
	
	echo '		<tr>'.chr(10);
	echo '			<th class="border">'.$name.'</th>'.chr(10);
	echo '			<td class="border">'.$valarray['c'].'</td>'.chr(10);
	echo '			<td class="border">'.$valarray['a'].'</td>'.chr(10);
	echo '			<td class="border">'.$valarray['p'].'</td>'.chr(10);
	echo '			<td class="border">'.$valarray['e'].'</td>'.chr(10);
	echo '		</tr>'.chr(10);
}
echo '		<tr>'.chr(10);
echo '			<th class="border">'._('Total').'</th>'.chr(10);
echo '			<td class="border">'.$sum['c'].'</td>'.chr(10);
echo '			<td class="border">'.$sum['a'].'</td>'.chr(10);
echo '			<td class="border">'.$sum['p'].'</td>'.chr(10);
echo '			<td class="border">'.$sum['e'].'</td>'.chr(10);
echo '		</tr>'.chr(10);
echo '	</table>'.chr(10);
echo '<div class="chartplaceholder" id="day-children">Trykk her for å hente graf:<br>'.
	'dagsfordelt, antall barn</div>'.chr(10);
echo '<div class="chartplaceholder" id="day-people">Trykk her for å hente graf:<br>'.
	'dagsfordelt, antall barn og voksne</div>'.chr(10);
echo '<div class="chartplaceholder" id="day-entries">Trykk her for å hente graf:<br>'.
	'dagsfordelt, antall bookinger</div>'.chr(10);
echo '</div>'.chr(10).chr(10);



/* ## STATS - WEEKS ## */
echo '<div class="hiddenprint"><h2>Ukesfordelt</h2>';
echo '<a id="switchlink4" href="javascript:switchView(4, \'ukesfordelt\');">Vis ukesfordelt</a><br><br>';
echo '</div>';
echo '<div style="display:none;" id="switch4">';
echo '	<table cellspacing="0" style="border-collapse: collapse;">'.chr(10);
echo '		<caption>Ukesfordelt statistikk</caption>'.chr(10);
echo '		<tr>'.chr(10);
echo '			<th class="border">Ukesfordelt</th>'.chr(10);
echo '			<th class="border">'.
'<img src="./img/icons/user_small.png" style="border: 0px solid black; vertical-align: middle;"> '.
_('Children').'</th>'.chr(10);
echo '			<th class="border">'.
'<img src="./img/icons/user_suit.png" style="border: 0px solid black; vertical-align: middle;"> '.
_('Adults').'</th>'.chr(10);
echo '			<th class="border">'._('Total').'</th>'.chr(10);
echo '			<th class="border">'.
'<img src="./img/icons/page_white.png" style="border: 0px solid black; vertical-align: middle;"> '.
_('Entries').'</th>'.chr(10);
echo '		</tr>'.chr(10);
foreach ($stats_week as $id => $valarray)
{
	if($id == 0)
		$name = '<i>'._('Non').'</i>';
	else
		$name = $valarray['Name'];
	
	echo '		<tr>'.chr(10);
	echo '			<th class="border">'.$name.'</th>'.chr(10);
	echo '			<td class="border">'.$valarray['c'].'</td>'.chr(10);
	echo '			<td class="border">'.$valarray['a'].'</td>'.chr(10);
	echo '			<td class="border">'.$valarray['p'].'</td>'.chr(10);
	echo '			<td class="border">'.$valarray['e'].'</td>'.chr(10);
	echo '		</tr>'.chr(10);
}
echo '		<tr>'.chr(10);
echo '			<th class="border">'._('Total').'</th>'.chr(10);
echo '			<td class="border">'.$sum['c'].'</td>'.chr(10);
echo '			<td class="border">'.$sum['a'].'</td>'.chr(10);
echo '			<td class="border">'.$sum['p'].'</td>'.chr(10);
echo '			<td class="border">'.$sum['e'].'</td>'.chr(10);
echo '		</tr>'.chr(10);
echo '	</table>'.chr(10);
echo '<div class="chartplaceholder" id="week-people">Trykk her for å hente graf:<br>'.
	'ukesfordelt, antall barn og voksne</div>'.chr(10);
echo '<div class="chartplaceholder" id="week-entries">Trykk her for å hente graf:<br>'.
	'ukesfordelt, antall bookinger</div>'.chr(10);
echo '</div>'.chr(10).chr(10);



/* ## STATS - MONTHS ## */ 
echo '<div class="hiddenprint"><h2>Månedsfordelt</h2>';
echo '<a id="switchlink5" href="javascript:switchView(5, \'månedsfordelt\');">Vis månedsfordelt</a><br><br>';
echo '</div>';
echo '<div style="display:none;" id="switch5">';
echo '	<table cellspacing="0" style="border-collapse: collapse;">'.chr(10);
echo '		<caption>Månedsfordelt statistikk</caption>'.chr(10);
echo '		<tr>'.chr(10);
echo '			<th class="border">Månedsfordelt</th>'.chr(10);
echo '			<th class="border">'.
'<img src="./img/icons/user_small.png" style="border: 0px solid black; vertical-align: middle;"> '.
_('Children').'</th>'.chr(10);
echo '			<th class="border">'.
'<img src="./img/icons/user_suit.png" style="border: 0px solid black; vertical-align: middle;"> '.
_('Adults').'</th>'.chr(10);
echo '			<th class="border">'._('Total').'</th>'.chr(10);
echo '			<th class="border">'.
'<img src="./img/icons/page_white.png" style="border: 0px solid black; vertical-align: middle;"> '.
_('Entries').'</th>'.chr(10);
echo '		</tr>'.chr(10);
foreach ($stats_month as $id => $valarray)
{
	if($id == 0)
		$name = '<i>'._('Non').'</i>';
	else
		$name = $valarray['Name'];
	
	echo '		<tr>'.chr(10);
	echo '			<th class="border">'.$name.'</th>'.chr(10);
	echo '			<td class="border">'.$valarray['c'].'</td>'.chr(10);
	echo '			<td class="border">'.$valarray['a'].'</td>'.chr(10);
	echo '			<td class="border">'.$valarray['p'].'</td>'.chr(10);
	echo '			<td class="border">'.$valarray['e'].'</td>'.chr(10);
	echo '		</tr>'.chr(10);
}
echo '		<tr>'.chr(10);
echo '			<th class="border">'._('Total').'</th>'.chr(10);
echo '			<td class="border">'.$sum['c'].'</td>'.chr(10);
echo '			<td class="border">'.$sum['a'].'</td>'.chr(10);
echo '			<td class="border">'.$sum['p'].'</td>'.chr(10);
echo '			<td class="border">'.$sum['e'].'</td>'.chr(10);
echo '		</tr>'.chr(10);
echo '	</table>'.chr(10);
echo '<br><br>'.chr(10);
echo '<div class="chartplaceholder" id="month-people">Trykk her for å hente graf:<br>'.
	'månedsfordelt, antall barn og voksne</div>'.chr(10);
echo '<div class="chartplaceholder" id="month-entries">Trykk her for å hente graf:<br>'.
	'månedsfordelt, antall bookinger</div>'.chr(10);
echo '</div>'.chr(10).chr(10);



/* ## STATS - YEARS ## */ 
echo '<div class="hiddenprint"><h2>Årsfordelt</h2>';
echo '<a id="switchlink6" href="javascript:switchView(6, \'årsfordelt\');">Vis årsfordelt</a><br><br>';
echo '</div>';
echo '<div style="display:none;" id="switch6">';
echo '	<table cellspacing="0" style="border-collapse: collapse;">'.chr(10);
echo '		<caption>Årsfordelt statistikk</caption>'.chr(10);
echo '		<tr>'.chr(10);
echo '			<th class="border">Årsfordelt</th>'.chr(10);
echo '			<th class="border">'.
'<img src="./img/icons/user_small.png" style="border: 0px solid black; vertical-align: middle;"> '.
_('Children').'</th>'.chr(10);
echo '			<th class="border">'.
'<img src="./img/icons/user_suit.png" style="border: 0px solid black; vertical-align: middle;"> '.
_('Adults').'</th>'.chr(10);
echo '			<th class="border">'._('Total').'</th>'.chr(10);
echo '			<th class="border">'.
'<img src="./img/icons/page_white.png" style="border: 0px solid black; vertical-align: middle;"> '.
_('Entries').'</th>'.chr(10);
echo '		</tr>'.chr(10);
foreach ($stats_year as $id => $valarray)
{
	if($id == 0)
		$name = '<i>'._('Non').'</i>';
	else
		$name = $valarray['Name'];
	
	echo '		<tr>'.chr(10);
	echo '			<th class="border">'.$name.'</th>'.chr(10);
	echo '			<td class="border">'.$valarray['c'].'</td>'.chr(10);
	echo '			<td class="border">'.$valarray['a'].'</td>'.chr(10);
	echo '			<td class="border">'.$valarray['p'].'</td>'.chr(10);
	echo '			<td class="border">'.$valarray['e'].'</td>'.chr(10);
	echo '		</tr>'.chr(10);
}
echo '		<tr>'.chr(10);
echo '			<th class="border">'._('Total').'</th>'.chr(10);
echo '			<td class="border">'.$sum['c'].'</td>'.chr(10);
echo '			<td class="border">'.$sum['a'].'</td>'.chr(10);
echo '			<td class="border">'.$sum['p'].'</td>'.chr(10);
echo '			<td class="border">'.$sum['e'].'</td>'.chr(10);
echo '		</tr>'.chr(10);
echo '	</table>'.chr(10);
echo '<br><br>'.chr(10);
echo '<div class="chartplaceholder" id="year-people">Trykk her for å hente graf:<br>'.
	'årsfordelt, antall barn og voksne</div>'.chr(10);
echo '<div class="chartplaceholder" id="year-entries">Trykk her for å hente graf:<br>'.
	'årsfordelt, antall bookinger</div>'.chr(10);
echo '</div>'.chr(10).chr(10);

?>