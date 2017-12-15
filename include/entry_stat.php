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
include "libs/municipals_norway.php";

filterMakeAlternatives();

if(!isset($_GET['filters']))
	$_GET['filters'] = '';

$filters = filterGetFromSerialized($_GET['filters']);
if(!$filters)
	$filters = array();

$SQL = genSQLFromFilters($filters, '*');
$SQL .= " order by `time_start`";

$SQL_datanova = genSQLFromFiltersDatanova ($filters);

if(!isset($noprint))
{
	print_header($day, $month, $year, $area);
	
	echo '<h1>'.__('Entry statistics').'</h1>';
	echo '<div class="hiddenprint">'; filterLink($filters, 'entry_stat');	echo '</div><br>'.chr(10);
	filterPrint($filters);				echo '<br>'.chr(10);
	echo '<br>'.chr(10).chr(10);
}

$Q           = db()->prepare($SQL);
$Q->execute();
$Q_datanova  = db()->prepare($SQL_datanova);
$Q_datanova->execute();

// Municipal
// Bookingtype
	// People (adults / children)
	// Number of entries
$municipals2 = array();
$entrytypes = array();
$stats_day = array();
$stats_week = array();
$stats_month = array();
$stats_year = array();
$sum = array();
$sum['c'] = 0;
$sum['a'] = 0;
$sum['p'] = 0;
$sum['e'] = 0;

$municipals[0] = ''; // No municipal defined

$tamed_booking = true;
foreach($filters as $filter) {
	if($filter[0] == 'tamed_booking')
	{
		$tamed_booking  = $filter[1];
	}
}
$tamed_datanova = false;
foreach($filters as $filter) {
	if($filter[0] == 'tamed_datanova')
	{
		$tamed_datanova  = $filter[1];
	}
}
if($tamed_datanova)
{
	$municipals[-1] = 'L�st bes�kende';
}

/*
 * #### ENTRIES ####
 *
 * c = children
 * a = adult
 * p = people, total
 * e = entries
 */
while($tamed_booking && $R = $Q->fetch(PDO::FETCH_ASSOC))
{
	$entry = getEntryParseDatabaseArray($R);
	
	/* Municipals */
	if($entry['customer_municipal_num'] == '') {
        $entry['customer_municipal_num'] = 0;
    }
	
	if(!isset($municipals2[$entry['customer_municipal_num']]))
	{
		$municipals2[$entry['customer_municipal_num']] = array(
			'c' => $entry['num_person_child'],
			'a' => $entry['num_person_adult'],
			'p' => ($entry['num_person_child'] + $entry['num_person_adult']),
			'e' => 1
		);
		$municipals2[$entry['customer_municipal_num']]['Name'] = $municipals[$entry['customer_municipal_num']];
	}
	else
	{
		$municipals2[$entry['customer_municipal_num']]['c'] += $entry['num_person_child'];
		$municipals2[$entry['customer_municipal_num']]['a'] += $entry['num_person_adult'];
		$municipals2[$entry['customer_municipal_num']]['p'] += $entry['num_person_child'];
		$municipals2[$entry['customer_municipal_num']]['p'] += $entry['num_person_adult'];
		$municipals2[$entry['customer_municipal_num']]['e'] ++;
	}
	
	/* Entrytypes */
	if(!isset($entrytypes[$entry['entry_type_id']]))
	{
		$entrytypes[$entry['entry_type_id']] = array(
			'c' => $entry['num_person_child'],
			'a' => $entry['num_person_adult'],
			'p' => ($entry['num_person_child'] + $entry['num_person_adult']),
			'e' => 1
		);
		
		if($entry['entry_type_id'] != 0)
		{
			$entry_type = getEntryType($entry['entry_type_id']);
			$entrytypes[$entry['entry_type_id']]['Name'] = $entry_type['entry_type_name'];
		}
		else {
            $entrytypes[$entry['entry_type_id']]['Name'] = '';
        }
	}
	else
	{
		$entrytypes[$entry['entry_type_id']]['c'] += $entry['num_person_child'];
		$entrytypes[$entry['entry_type_id']]['a'] += $entry['num_person_adult'];
		$entrytypes[$entry['entry_type_id']]['p'] += $entry['num_person_child'];
		$entrytypes[$entry['entry_type_id']]['p'] += $entry['num_person_adult'];
		$entrytypes[$entry['entry_type_id']]['e'] ++;
	}

	/* Days */
	$thisone = date('Ymd', $entry['time_start']);
	if(!isset($stats_day[$thisone]))
	{
		$stats_day[$thisone] = array(
			'c' => $entry['num_person_child'],
			'a' => $entry['num_person_adult'],
			'p' => ($entry['num_person_child'] + $entry['num_person_adult']),
			'e' => 1
		);
		$stats_day[$thisone]['Name'] = date('d-m-Y', $entry['time_start']);
	}
	else
	{
		$stats_day[$thisone]['c'] += $entry['num_person_child'];
		$stats_day[$thisone]['a'] += $entry['num_person_adult'];
		$stats_day[$thisone]['p'] += $entry['num_person_child'];
		$stats_day[$thisone]['p'] += $entry['num_person_adult'];
		$stats_day[$thisone]['e'] ++;
	}
	
	/* Weeks */
	$thisone = date('YW', $entry['time_start']);
	if(!isset($stats_week[$thisone]))
	{
		$stats_week[$thisone] = array(
			'c' => $entry['num_person_child'],
			'a' => $entry['num_person_adult'],
			'p' => ($entry['num_person_child'] + $entry['num_person_adult']),
			'e' => 1
		);
		$stats_week[$thisone]['Name'] = 'Uke '.date('W, Y', $entry['time_start']);
	}
	else
	{
		$stats_week[$thisone]['c'] += $entry['num_person_child'];
		$stats_week[$thisone]['a'] += $entry['num_person_adult'];
		$stats_week[$thisone]['p'] += $entry['num_person_child'];
		$stats_week[$thisone]['p'] += $entry['num_person_adult'];
		$stats_week[$thisone]['e'] ++;
	}
	
	/* Months */
	$thisone = date('Ym', $entry['time_start']);
	if(!isset($stats_month[$thisone]))
	{
		$stats_month[$thisone] = array(
			'c' => $entry['num_person_child'],
			'a' => $entry['num_person_adult'],
			'p' => ($entry['num_person_child'] + $entry['num_person_adult']),
			'e' => 1
		);
		$stats_month[$thisone]['Name'] = __(date('F', $entry['time_start'])).' '.date('Y', $entry['time_start']);
	}
	else
	{
		$stats_month[$thisone]['c'] += $entry['num_person_child'];
		$stats_month[$thisone]['a'] += $entry['num_person_adult'];
		$stats_month[$thisone]['p'] += $entry['num_person_child'];
		$stats_month[$thisone]['p'] += $entry['num_person_adult'];
		$stats_month[$thisone]['e'] ++;
	}
	
	/* Years */
	$thisone = date('Y', $entry['time_start']);
	if(!isset($stats_year[$thisone]))
	{
		$stats_year[$thisone] = array(
			'c' => $entry['num_person_child'],
			'a' => $entry['num_person_adult'],
			'p' => ($entry['num_person_child'] + $entry['num_person_adult']),
			'e' => 1
		);
		$stats_year[$thisone]['Name'] = date('Y', $entry['time_start']);
	}
	else
	{
		$stats_year[$thisone]['c'] += $entry['num_person_child'];
		$stats_year[$thisone]['a'] += $entry['num_person_adult'];
		$stats_year[$thisone]['p'] += $entry['num_person_child'];
		$stats_year[$thisone]['p'] += $entry['num_person_adult'];
		$stats_year[$thisone]['e'] ++;
	}
	
	$sum['c'] += $entry['num_person_child'];
	$sum['a'] += $entry['num_person_adult'];
	$sum['p'] += $entry['num_person_child'];
	$sum['p'] += $entry['num_person_adult'];
	$sum['e'] ++;
}

/*
 * #### DATANOVA ####
 *
 * c = children
 * a = adult
 * p = people, total
 * e = entries
 */
while($tamed_datanova && $R = $Q_datanova->fetch(PDO::FETCH_ASSOC))
{
	$entry = array(
			'num_person_child' => $R['antall_barn'],
			'num_person_adult' => $R['antall_voksne'],
			'time_start' => $R['dag'],
			
			'entry_type_id' => 'dn_'.$R['kat_id'],
			'customer_municipal_num' => -1,
			
			'dn_kat_id' => $R['kat_id'], // Datanova category
		);
	
	/* Municipals */
	if($entry['customer_municipal_num'] == '')
		$entry['customer_municipal_num'] = 0;
	
	if(!isset($municipals2[$entry['customer_municipal_num']]))
	{
		$municipals2[$entry['customer_municipal_num']] = array(
			'c' => $entry['num_person_child'],
			'a' => $entry['num_person_adult'],
			'p' => ($entry['num_person_child'] + $entry['num_person_adult']),
			'e' => 1
		);
		$municipals2[$entry['customer_municipal_num']]['Name'] = $municipals[$entry['customer_municipal_num']];
	}
	else
	{
		$municipals2[$entry['customer_municipal_num']]['c'] += $entry['num_person_child'];
		$municipals2[$entry['customer_municipal_num']]['a'] += $entry['num_person_adult'];
		$municipals2[$entry['customer_municipal_num']]['p'] += $entry['num_person_child'];
		$municipals2[$entry['customer_municipal_num']]['p'] += $entry['num_person_adult'];
		$municipals2[$entry['customer_municipal_num']]['e'] ++;
	}
	
	/* Entrytypes */
	if(!isset($entrytypes[$entry['entry_type_id']]))
	{
		$entrytypes[$entry['entry_type_id']] = array(
			'c' => $entry['num_person_child'],
			'a' => $entry['num_person_adult'],
			'p' => ($entry['num_person_child'] + $entry['num_person_adult']),
			'e' => 1
		);
		
		if(substr($entry['entry_type_id'], 0, 2) == 'dn')
		{
			$Q_typer = db()->prepare('select * from `import_dn_kategori` where `kat_id` = :kat_id limit 1');
            $Q_typer->bindValue(':kat_id',  $entry['dn_kat_id'], PDO::PARAM_INT);
            $Q_typer->execute();
			$entry_type_name = 'Kasse - ukjent';
			while($R_type = $Q_typer->fetch())
			{
				$entry_type_name = 'Kasse - '. $R_type['kat_navn'];
			}
		}
		elseif($entry['entry_type_id'] != 0)
		{
			
			//if($entry['entry_type_id'] == -1)
			//	$entry_type_name = 'L�st bes�kende';
			$entry_type = getEntryType($entry['entry_type_id']);
			$entry_type_name = $entry_type['entry_type_name'];
		}
		else {
            $entry_type_name = '';
        }
		
		$entrytypes[$entry['entry_type_id']]['Name'] = $entry_type_name;
	}
	else
	{
		$entrytypes[$entry['entry_type_id']]['c'] += $entry['num_person_child'];
		$entrytypes[$entry['entry_type_id']]['a'] += $entry['num_person_adult'];
		$entrytypes[$entry['entry_type_id']]['p'] += $entry['num_person_child'];
		$entrytypes[$entry['entry_type_id']]['p'] += $entry['num_person_adult'];
		$entrytypes[$entry['entry_type_id']]['e'] ++;
	}
	
	/* Days */
	$thisone = date('Ymd', $entry['time_start']);
	if(!isset($stats_day[$thisone]))
	{
		$stats_day[$thisone] = array(
			'c' => $entry['num_person_child'],
			'a' => $entry['num_person_adult'],
			'p' => ($entry['num_person_child'] + $entry['num_person_adult']),
			'e' => 1
		);
		$stats_day[$thisone]['Name'] = date('d-m-Y', $entry['time_start']);
	}
	else
	{
		$stats_day[$thisone]['c'] += $entry['num_person_child'];
		$stats_day[$thisone]['a'] += $entry['num_person_adult'];
		$stats_day[$thisone]['p'] += $entry['num_person_child'];
		$stats_day[$thisone]['p'] += $entry['num_person_adult'];
		$stats_day[$thisone]['e'] ++;
	}
	
	/* Weeks */
	$thisone = date('YW', $entry['time_start']);
	if(!isset($stats_week[$thisone]))
	{
		$stats_week[$thisone] = array(
			'c' => $entry['num_person_child'],
			'a' => $entry['num_person_adult'],
			'p' => ($entry['num_person_child'] + $entry['num_person_adult']),
			'e' => 1
		);
		$stats_week[$thisone]['Name'] = 'Uke '.date('W, Y', $entry['time_start']);
	}
	else
	{
		$stats_week[$thisone]['c'] += $entry['num_person_child'];
		$stats_week[$thisone]['a'] += $entry['num_person_adult'];
		$stats_week[$thisone]['p'] += $entry['num_person_child'];
		$stats_week[$thisone]['p'] += $entry['num_person_adult'];
		$stats_week[$thisone]['e'] ++;
	}
	
	/* Months */
	$thisone = date('Ym', $entry['time_start']);
	if(!isset($stats_month[$thisone]))
	{
		$stats_month[$thisone] = array(
			'c' => $entry['num_person_child'],
			'a' => $entry['num_person_adult'],
			'p' => ($entry['num_person_child'] + $entry['num_person_adult']),
			'e' => 1
		);
		$stats_month[$thisone]['Name'] = __(date('F', $entry['time_start'])).' '.date('Y', $entry['time_start']);
	}
	else
	{
		$stats_month[$thisone]['c'] += $entry['num_person_child'];
		$stats_month[$thisone]['a'] += $entry['num_person_adult'];
		$stats_month[$thisone]['p'] += $entry['num_person_child'];
		$stats_month[$thisone]['p'] += $entry['num_person_adult'];
		$stats_month[$thisone]['e'] ++;
	}
	
	/* Years */
	$thisone = date('Y', $entry['time_start']);
	if(!isset($stats_year[$thisone]))
	{
		$stats_year[$thisone] = array(
			'c' => $entry['num_person_child'],
			'a' => $entry['num_person_adult'],
			'p' => ($entry['num_person_child'] + $entry['num_person_adult']),
			'e' => 1
		);
		$stats_year[$thisone]['Name'] = date('Y', $entry['time_start']);
	}
	else
	{
		$stats_year[$thisone]['c'] += $entry['num_person_child'];
		$stats_year[$thisone]['a'] += $entry['num_person_adult'];
		$stats_year[$thisone]['p'] += $entry['num_person_child'];
		$stats_year[$thisone]['p'] += $entry['num_person_adult'];
		$stats_year[$thisone]['e'] ++;
	}
	
	$sum['c'] += $entry['num_person_child'];
	$sum['a'] += $entry['num_person_adult'];
	$sum['p'] += $entry['num_person_child'];
	$sum['p'] += $entry['num_person_adult'];
	$sum['e'] ++;
}

// Sort by names
$municipals3 = array();
foreach ($municipals2 as $mun_num => $valarray)
{
	$municipals3[$mun_num] = 
		$municipals[$mun_num];
}
asort($municipals3);
$entrytypes2 = array();
foreach ($entrytypes as $entry_type_id => $valarray)
{
	$entrytypes2[$entry_type_id] = $valarray['Name'];
	
}
asort($entrytypes2);

ksort($stats_day);
ksort($stats_week);
ksort($stats_month);
ksort($stats_year);


/*
 * Fix days/months/weeks/years with no bookings
 * 
 * $stats_day = YYYYMMDD
 * $stats_week = YYYYWW / YYYYW
 * $stats_month = YYYYMM
 * $stats_year = YYYY
 */
function getTimeFromYYYYMMDDPlussOne ($YYYYMMDD) {
	return mktime(0, 0, 0, substr($YYYYMMDD, 4, 2), substr($YYYYMMDD, 6)+1, substr($YYYYMMDD, 0, 4));
}
function getTimeFromYYYYMMDD ($YYYYMMDD) {
	return mktime(0, 0, 0, substr($YYYYMMDD, 4, 2), substr($YYYYMMDD, 6), substr($YYYYMMDD, 0, 4));
}
function getTimeFromYYYYMMPlussOne ($YYYYMM) {
	return mktime(0, 0, 0, substr($YYYYMM, 4)+1, 1, substr($YYYYMM, 0, 4));
}
function getTimeFromYYYYMM ($YYYYMM) {
	return mktime(0, 0, 0, substr($YYYYMM, 4), 1, substr($YYYYMM, 0, 4));
}
$last = '';
foreach($stats_day as $id => $val)
{
	while($last != '' && $id > $last)
	{
		$last = date('Ymd', getTimeFromYYYYMMDDPlussOne($last));
		if(!isset($stats_day[$last]))
		{
			$stats_day[$last] = array(
				'c' => 0,
				'a' => 0,
				'p' => 0,
				'e' => 0
			);
			$stats_day[$last]['Name'] = date('d-m-Y', getTimeFromYYYYMMDD($last));
		}
	}
	$last = $id;
}

$last = '';
foreach($stats_week as $id => $val)
{
	while($last != '' && $id > $last)
	{
		$week = substr($last, 4)+1;
		$year = substr($last, 0, 4);
		if($week > 52) {
			$year++;
			$week--;
		}
		if(strlen($week) == 1)
			$week = '0'.$week;
		$last = $year.$week;
		if(!isset($stats_week[$last]))
		{
			$stats_week[$last] = array(
				'c' => 0,
				'a' => 0,
				'p' => 0,
				'e' => 0
			);
			$stats_week[$last]['Name'] =  'Uke '.$week.', '.$year;
		}
	}
	$last = $id;
}

$last = '';
foreach($stats_month as $id => $val)
{
	while($last != '' && $id > $last)
	{
		$last = date('Ym', getTimeFromYYYYMMPlussOne($last));
		if(!isset($stats_month[$last]))
		{
			$stats_month[$last] = array(
				'c' => 0,
				'a' => 0,
				'p' => 0,
				'e' => 0
			);
			$stats_month[$last]['Name'] = __(date('F', getTimeFromYYYYMM($last))).' '.
				date('Y', getTimeFromYYYYMM($last));
		}
	}
	$last = $id;
}

$last = '';
foreach($stats_year as $id => $val)
{
	while($last != '' && $id > $last)
	{
		$last++;
		if(!isset($stats_year[$last]))
		{
			$stats_year[$last] = array(
				'c' => 0,
				'a' => 0,
				'p' => 0,
				'e' => 0
			);
			$stats_year[$last]['Name'] = $last;
		}
	}
	$last = $id;
}
ksort($stats_day);
ksort($stats_week);
ksort($stats_month);
ksort($stats_year);

?>