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

$noprint = true;
require "include/entry_stat.php";


/* ## Charts ## */
include("libs/pChart/pData.class.php");
include("libs/pChart/pChart.class.php");
include("libs/jmData.class.php");

$time = time();

if(!isset($_GET['what']))
{
	echo 'Ingen graf-type definert.';
	exit();
}

$filename = '';

/**
 * An sorted $array1 rearranges $array2
 *
 * @param array $array1
 * @param array $array2
 * @return array
 */
function sortArraySpecial ($array1, $array2)
{
	$returnarray = array();
	foreach($array1 as $id => $name)
	{
		$returnarray[$id] = $array2[$id];
	}
	return $returnarray;
}

// Dataset definition
switch ($_GET['what'])
{
	case 'municipal-children':
		$dataset = new jmData;
		$municipals2 = sortArraySpecial($municipals3, $municipals2);
		$dataset->importArray($municipals2);
		$dataset->setChildren();
		drawChart('Kommunefordelt - antall barn', 'municipal', true, true, true);
	break;
	
	case 'municipal-people':
		$dataset = new jmData;
		$municipals2 = sortArraySpecial($municipals3, $municipals2);
		$dataset->importArray($municipals2);
		$dataset->setPeople();
		drawChart('Kommunefordelt - antall barn og voksne', 'municipal', true, false, true);
	break;
	
	case 'municipal-entries':
		$dataset = new jmData;
		$municipals2 = sortArraySpecial($municipals3, $municipals2);
		$dataset->importArray($municipals2);
		$dataset->setEntries();
		drawChart('Kommunefordelt - antall bookinger', 'municipal', true, false, true);
	break;
	
	case 'entrytype-people':
		$dataset = new jmData;
		$entrytypes = sortArraySpecial($entrytypes2, $entrytypes);
		$dataset->importArray($entrytypes);
		$dataset->setPeople();
		drawChart('Typefordelt - antall barn og voksne', 'day', true, false, true);
	break;
	
	case 'entrytype-entries':
		$dataset = new jmData;
		$entrytypes = sortArraySpecial($entrytypes2, $entrytypes);
		$dataset->importArray($entrytypes);
		$dataset->setEntries();
		drawChart('Typefordelt - antall bookinger', 'day', false, false, true);
	break;
	
	case 'day-children':
		$dataset = new jmData;
		$dataset->importArray($stats_day);
		$dataset->setChildren();
		drawChart('Dagfordelt - antall barn', 'day', true, true, true);
	break;
	
	case 'day-people':
		$dataset = new jmData;
		$dataset->importArray($stats_day);
		$dataset->setPeople();
		drawChart('Dagfordelt - antall barn og voksne', 'day', true, false, true);
	break;
	
	case 'day-entries':
		$dataset = new jmData;
		$dataset->importArray($stats_day);
		$dataset->setEntries();
		drawChart('Dagfordelt - antall bookinger', 'day', false, false, true);
	break;
	
	case 'week-people':
		$dataset = new jmData;
		$dataset->importArray($stats_week);
		$dataset->setPeople();
		if($stats_week > 5)
			$rotate = true;
		else
			$rotate = false;
		drawChart('Ukefordelt - antall barn og voksne', 'week', true, false, $rotate);
	break;
	
	case 'week-entries':
		$dataset = new jmData;
		$dataset->importArray($stats_week);
		$dataset->setEntries();
		if($stats_week > 5)
			$rotate = true;
		else
			$rotate = false;
		drawChart('Ukefordelt - antall bookinger', 'week', false, false, $rotate);
	break;
	
	case 'month-people':
		$dataset = new jmData;
		$dataset->importArray($stats_month);
		$dataset->setPeople();
		if($stats_week > 5)
			$rotate = true;
		else
			$rotate = false;
		drawChart('Månedsfordelt - antall barn og voksne', 'month', true, false, $rotate);
	break;
	
	case 'month-entries':
		$dataset = new jmData;
		$dataset->importArray($stats_month);
		$dataset->setEntries();
		if($stats_week > 5)
			$rotate = true;
		else
			$rotate = false;
		drawChart('Månedsfordelt - antall bookinger', 'month', false, false, $rotate);
	break;
	
	case 'year-people':
		$dataset = new jmData;
		$dataset->importArray($stats_year);
		$dataset->setPeople();
		drawChart('Årsfordelt - antall barn og voksne', 'year', true);
	break;
	
	case 'year-entries':
		$dataset = new jmData;
		$dataset->importArray($stats_year);
		$dataset->setEntries();
		drawChart('Årsfordelt - antall bookinger', 'year', false);
	break;
	
	default:
		echo 'Ingen graftype definert.';
		exit();
}

echo 'ok - '.$filename;

function drawChart ($tittel, $what, $people=true, $childrenonly = false, $rotate = false)
{
	global $time, $dataset, $filename, $chart_path;
	
	// Initialise the graph
	if(!$rotate)
		$Test = new pChart(700,230);
	else
		$Test = new pChart(700,330);
	$Test->setFontProperties(__DIR__ . '/fonts/tahoma.ttf', 8);
	
	$Test->setGraphArea(70,30,680,200);
	if(!$rotate)
	{
		$Test->drawFilledRoundedRectangle(7,7,693,223,5,240,240,240);
		$Test->drawRoundedRectangle(5,5,695,225,5,230,230,230);
	}
	else
	{
		$Test->drawFilledRoundedRectangle(7,7,693,223+100,5,240,240,240);
		$Test->drawRoundedRectangle(5,5,695,225+100,5,230,230,230);
	}
	$Test->drawGraphArea(255,255,255,TRUE);
	if(!$rotate)
		$Test->drawScale($dataset->GetData(),$dataset->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2,TRUE);
	else
		$Test->drawScale($dataset->GetData(),$dataset->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,90,2,TRUE);
	$Test->drawGrid(4,TRUE,230,230,230,50);
	
	// Draw the 0 line
	$Test->setFontProperties(__DIR__ . '/fonts/tahoma.ttf', 6);
	$Test->drawTreshold(0,143,55,72,TRUE,TRUE);
	
	// Draw the line graph
	//$Test->drawLineGraph($dataset->GetData(),$dataset->GetDataDescription());
	//$Test->drawPlotGraph($dataset->GetData(),$dataset->GetDataDescription(),3,2,255,255,255);
	$Test->drawBarGraph($dataset->GetData(),$dataset->GetDataDescription(),TRUE);  
	
	// Finish the graph
	$Test->setFontProperties(__DIR__ . '/fonts/tahoma.ttf', 8);
	$Test->drawLegend(75,35,$dataset->GetDataDescription(),255,255,255);
	$Test->setFontProperties(__DIR__ . '/fonts/tahoma.ttf', 10);
	$Test->drawTitle(60,22,$tittel,50,50,50,585);
	
	if($people && $childrenonly)
		$what2 = 'children';
	elseif($people)
		$what2 = 'people';
	else
		$what2 = 'entries';
	
	$filename = $chart_path.'/'.$time.'-'.$what.'-'.$what2.'.png';
	$Test->Render($filename);
}