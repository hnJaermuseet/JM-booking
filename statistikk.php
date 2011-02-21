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

include_once("glob_inc.inc.php");

$return_to = '';
if(isset($_GET['return_to']))
{
	switch ($_GET['return_to'])
	{
		case 'entry_stat':
			$return_to = 'entry_stat'; break;
		case 'entry_list':
			$return_to = 'entry_list'; break;
		case 'customer_list':
			$return_to = 'customer_list'; break;
		default:
			$return_to = 'entry_stat'; break;
	}
}

filterMakeAlternatives();
if(isset($_GET['filter'])
 && isset($_GET['time_start_nu'])
 && isset($_GET['time_end_nu'])
 && is_array($_GET['filter']))
{
	// TODO: Sjekk alle variabler som blir sendt, en del må være array
	/*
	 * Behandler greiene
	 * 
	 * - entry_type[] -> hele felter/filter
	 * - time_start_nu -> filervalue1_0
	 * - time_end_nu -> filervalue1_1
	 * - num_person_count -> eget filter
	 */
	
	$i = 2;
	if(isset($_GET['entry_type']) && is_array($_GET['entry_type']))
	{
		foreach($_GET['entry_type'] as $entry_type_id) {
			$_GET['rows'][$i] = $i;
			$_GET['filter'][$i] = 'entry_type_id';
			$_GET['filtervalue1_'.$i] = $entry_type_id;
			$i++;
		}
	}
	
	if($_GET['time_start_nu'] == '1') {
		$_GET['filtervalue1_0'] = 'current';
	} else {
		$_GET['filtervalue1_0'] = $_GET['time_start'];
	}
	
	if($_GET['time_end_nu'] == '1') {
		$_GET['filtervalue1_1'] = 'current';
	} else {
		$_GET['filtervalue1_1'] = $_GET['time_end'];
	}

	if(isset($_GET['num_person_count']) && $_GET['num_person_count'] == '1') {
		$_GET['rows'][$i] = $i;
		$_GET['filter'][$i] = 'num_person_count';
		$_GET['filtervalue1_'.$i] = '1';
		$i++;
	}
	
	if(isset($_GET['area_id']) && $_GET['area_id'] != '0') {
		$_GET['rows'][$i] = $i;
		$_GET['filter'][$i] = 'area_id';
		$_GET['filtervalue1_'.$i] = $_GET['area_id'];
		$i++;
	}
	
	// Hva blir tatt med
	$_GET['rows'][$i] = $i;
	$_GET['filter'][$i] = 'tamed_booking';
	if(isset($_GET['tamed_booking'])) {
		$_GET['filtervalue1_'.$i] = '1';
	} else {
		$_GET['filtervalue1_'.$i] = '0';
	}
	$i++;
	$_GET['rows'][$i] = $i;
	$_GET['filter'][$i] = 'tamed_datanova';
	if(isset($_GET['tamed_datanova'])) {
		$_GET['filtervalue1_'.$i] = '1';
	} else {
		$_GET['filtervalue1_'.$i] = '0';
	}
	$i++;
	
	
	// Kategorier - Datanova
	if(isset($_GET['dn_kategori']) && is_array($_GET['dn_kategori']))
	{
		foreach($_GET['dn_kategori'] as $dn_kategori_id) {
			$_GET['rows'][$i] = $i;
			$_GET['filter'][$i] = 'dn_kategori_id';
			$_GET['filtervalue1_'.$i] = $dn_kategori_id;
			$i++;
		}
	}
	
	
	// Behandler med filter-metoden
	$filters = readFiltersFromGet();
	if($return_to != '')
	{
		$filters_serialized = serialize($filters);
		switch ($return_to)
		{
			case 'entry_stat':
				header('Location: entry_stat.php?filters='.$filters_serialized); break;
			case 'entry_list':
				header('Location: entry_list.php?filters='.$filters_serialized); break;
			case 'customer_list':
				header('Location: entry_list.php?listtype=customer_list&filters='.$filters_serialized); break;
		}
		exit();
	}
}


print_header($day, $month, $year, $area);

echo '<h1>Statistikkuthenting</h1>'.chr(10).chr(10);

echo '<form method="get" name="filters" action="'.$_SERVER['PHP_SELF'].'">'.chr(10);

echo '<table><tr>';
echo '<td style="border: 1px solid black; padding: 5px;">
<b>Fra og med</b><br>
<input type="hidden" name="rows[]" value="0">
<input type="hidden" name="filter[0]" value="time_start">
<input type="hidden" name="filtervalue2_0" value=">=">
<input type="radio" name="time_start_nu" value="0" checked="checked"><input type="text" name="time_start" value="'.date('Y-01-01 00:00').'"><br>
<label><input type="radio" name="time_start_nu" value="1"> Nåværende tidspunkt*</label></td>';
echo '<td style="border: 1px solid black; padding: 5px;">
<b>Til og med</b><br>
<input type="hidden" name="rows[]" value="1">
<input type="hidden" name="filter[1]" value="time_end">
<input type="hidden" name="filtervalue2_1" value="<=">
<input type="radio" name="time_end_nu" value="0"><input type="text" name="time_end" value="'.date('Y-12-31 23:59').'"><br>
<label><input type="radio" name="time_end_nu" value="1" checked="checked"> Nåværende tidspunkt*</label></td>';
echo '</tr><tr style="margin-top: 5px;">';

echo '<td style="border: 1px solid black; padding: 10px; text-align: center;">
<span style="font-size: 18px;">Fra booking</span><br />
<label><input type="checkbox" value="1" name="tamed_booking" checked="checked"> Ta med tall fra booking</label></td>';
echo '<td style="border: 1px solid black; padding: 10px; text-align: center;">
<span style="font-size: 18px;">Fra kasseapparat</span><br />
<label><input type="checkbox" value="1" name="tamed_datanova" checked="checked"> Ta med tall fra kasseapparat</label></td>';
echo '</tr><tr>';

// Bookingtyper
echo '<td style="border: 1px solid black; padding: 5px;">
<b>Bookingtyper:</b><br>
<i>Hvis ingen er valgt, så hentes alle ut</i><br>
';
$Q_typer = mysql_query("select * from `entry_type` order by `entry_type_name`");
while($R = mysql_fetch_assoc($Q_typer)) {
	echo '<label><input type="checkbox" name="entry_type[]" value="'.$R['entry_type_id'].'"> '.$R['entry_type_name'].'</label><br>';
}
echo '</td>
';

// Datanova-typer
echo '<td style="border: 1px solid black; padding: 5px;">
<b>Kategorier fra kasseapparat:</b><br>
<i>Hvis ingen er valgt, så hentes alle ut</i><br>
';
$Q_typer = mysql_query("select * from `import_dn_kategori` order by `kat_navn`");
while($R = mysql_fetch_assoc($Q_typer)) {
	echo '<label><input type="checkbox" name="dn_kategori[]" value="'.$R['kat_id'].'"> '.$R['kat_navn'].'</label><br>';
}
echo '</td>
';
echo '</tr><tr>';
echo '<td style="border: 1px solid black; padding: 5px;">
<b>Vis resultat i:</b><br>';
echo '<label><input type="radio" name="return_to" value="entry_list"> '._('Entry list').'</label><br>'.chr(10);
echo '<label><input type="radio" name="return_to" value="entry_stat" checked="checked"';
echo '> '._('Entry stats').'</label><br>'.chr(10);
echo '<label><input type="radio" name="return_to" value="customer_list"> Kundeliste</label><br>'.chr(10);
echo '</td>';
echo '</tr><tr>';
echo '<td style="border: 1px solid black; padding: 5px;">
<label><input type="checkbox" name="num_person_count" value="1" checked="checked"> <b>De som skal telles i booking</b></label>';

// Area
$Q_area = mysql_query("select id as area_id, area_name from `mrbs_area` order by area_name");
echo '<br><br><b>'._('Area').':</b><br><select name="area_id">';
echo '<option value="0">Alle anleggene</option>';
while ($R = mysql_fetch_assoc($Q_area)) {
	echo '<option value="'.$R['area_id'].'"';
	if(($area == '' || $area == 0) && $R['area_name'] == 'Vitenfabrikken')
		echo ' selected="selected"';
	elseif($area == $R['area_id'])
		echo ' selected="selected"';
	echo '>'.$R['area_name'].'</option>';
}
echo '</select>';

echo '</td></tr><tr>';
echo '<td style="border: 1px solid black; padding: 15px; text-align: center;" colspan="2">';
echo '
<input class="ui-button ui-state-default ui-corner-all" '.
			'type="submit" style="font-size: 18px;" value="Vis statistikk"></td>';

echo '</tr></table>';
echo '</form><br /><br />';
echo '* Vil alltid være nåværende tidspunkt. Hvis du åpner samme liste om 3 dager, så vil du hente fra/til det tidspunktet.';
?>