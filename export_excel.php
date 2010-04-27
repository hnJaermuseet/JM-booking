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
require "libs/Excelklasse/Writer.php";

//print ('http://booking.jaermuseet.local/entry_stat.php?filters='.filterSerialized($filters));
//exit();

/* ## Lager Excel-fil ## */
$navn = 'booking-statistikk-'.date('Ymd_His');

// Creating a workbook
$workbook = new Spreadsheet_Excel_Writer();

// sending HTTP headers
$workbook->send($navn.'.xls');

$formatHeading =& $workbook->addFormat(array('bold' => 1, 'align' => 'right'));
$formatHeadingBig =& $workbook->addFormat(array('bold' => 1, 'size' => '16'));
$formatBold =& $workbook->addFormat(array('bold' => 1));
$formatRight =& $workbook->addFormat(array('align' => 'right'));
$formatSum =& $workbook->addFormat(array('bold' => 1));
$formatSum->setBottom(1);
$formatSum->setTop(1);



/* #### MUNICIPALS #### */
$worksheet1 =& $workbook->addWorksheet('Kommune');
$worksheet1->hideGridlines();
$worksheet1->setColumn(0, 0, 17);
$worksheet1->setColumn(1, 3, 7);
$worksheet1->setColumn(4, 4, 17);

$linje = 0;
$worksheet1->write($linje, 0, "Kommunefordelt datautdrag fra Jærmuseets bookingsystem", $formatHeadingBig);
$linje++; $linje++;

$worksheet1->write($linje, 0, 'Kommunenavn', $formatHeading);
$worksheet1->write($linje, 1, 'Barn', $formatHeading);
$worksheet1->write($linje, 2, 'Voksne', $formatHeading);
$worksheet1->write($linje, 3, 'Totalt', $formatHeading);
$worksheet1->write($linje, 4, 'Antall bookinger', $formatHeading);
foreach ($municipals3 as $mun_num => $name)
{
	$linje++;
	if($mun_num == 0)
		$name = '';
	$valarray = $municipals2[$mun_num];
	$worksheet1->write($linje, 0, $name);
	$worksheet1->write($linje, 1, $valarray['c']);
	$worksheet1->write($linje, 2, $valarray['a']);
	$worksheet1->write($linje, 3, $valarray['p']);
	$worksheet1->write($linje, 4, $valarray['e']);
}
$linje++;
$worksheet1->write($linje, 0, 'Totalt', $formatSum);
$worksheet1->write($linje, 1, $sum['c'], $formatSum);
$worksheet1->write($linje, 2, $sum['a'], $formatSum);
$worksheet1->write($linje, 3, $sum['p'], $formatSum);
$worksheet1->write($linje, 4, $sum['e'], $formatSum);


/* #### ENTRYTYPES #### */
$worksheet2 =& $workbook->addWorksheet('Bookingtyper');
$worksheet2->hideGridlines();
$worksheet2->setColumn(0, 0, 17);
$worksheet2->setColumn(1, 3, 7);
$worksheet2->setColumn(4, 4, 17);

$linje = 0;
$worksheet2->write($linje, 0, "Typefordelt datautdrag fra Jærmuseets bookingsystem", $formatHeadingBig);
$linje++; $linje++;

$worksheet2->write($linje, 0, 'Type', $formatHeading);
$worksheet2->write($linje, 1, 'Barn', $formatHeading);
$worksheet2->write($linje, 2, 'Voksne', $formatHeading);
$worksheet2->write($linje, 3, 'Totalt', $formatHeading);
$worksheet2->write($linje, 4, 'Antall bookinger', $formatHeading);
foreach ($entrytypes2 as $id => $name)
{
	$linje++;
	if($id == 0)
		$name = '';
	$valarray = $entrytypes[$id];
	
	$worksheet2->write($linje, 0, $name);
	$worksheet2->write($linje, 1, $valarray['c']);
	$worksheet2->write($linje, 2, $valarray['a']);
	$worksheet2->write($linje, 3, $valarray['p']);
	$worksheet2->write($linje, 4, $valarray['e']);
}
$linje++;
$worksheet2->write($linje, 0, 'Totalt', $formatSum);
$worksheet2->write($linje, 1, $sum['c'], $formatSum);
$worksheet2->write($linje, 2, $sum['a'], $formatSum);
$worksheet2->write($linje, 3, $sum['p'], $formatSum);
$worksheet2->write($linje, 4, $sum['e'], $formatSum);


/* #### DAYS #### */
$worksheet3 =& $workbook->addWorksheet('Dag');
$worksheet3->hideGridlines();
$worksheet3->setColumn(0, 0, 10);
$worksheet3->setColumn(1, 3, 7);
$worksheet3->setColumn(4, 4, 17);

$linje = 0;
$worksheet3->write($linje, 0, "Dagsfordelt datautdrag fra Jærmuseets bookingsystem", $formatHeadingBig);
$linje++; $linje++;

$worksheet3->write($linje, 0, 'Dag', $formatHeading);
$worksheet3->write($linje, 1, 'Barn', $formatHeading);
$worksheet3->write($linje, 2, 'Voksne', $formatHeading);
$worksheet3->write($linje, 3, 'Totalt', $formatHeading);
$worksheet3->write($linje, 4, 'Antall bookinger', $formatHeading);
foreach ($stats_day as $id => $valarray)
{
	$linje++;
	if($id == 0)
		$name = '';
	else
		$name = $valarray['Name'];
	$worksheet3->write($linje, 0, $name, $formatRight);
	$worksheet3->write($linje, 1, $valarray['c']);
	$worksheet3->write($linje, 2, $valarray['a']);
	$worksheet3->write($linje, 3, $valarray['p']);
	$worksheet3->write($linje, 4, $valarray['e']);
}
$linje++;
$worksheet3->write($linje, 0, 'Totalt', $formatSum);
$worksheet3->write($linje, 1, $sum['c'], $formatSum);
$worksheet3->write($linje, 2, $sum['a'], $formatSum);
$worksheet3->write($linje, 3, $sum['p'], $formatSum);
$worksheet3->write($linje, 4, $sum['e'], $formatSum);



/* #### WEEKS #### */
$worksheet4 =& $workbook->addWorksheet('Uke');
$worksheet4->hideGridlines();
$worksheet4->setColumn(0, 0, 12);
$worksheet4->setColumn(1, 3, 7);
$worksheet4->setColumn(4, 4, 17);

$linje = 0;
$worksheet4->write($linje, 0, "Ukesfordelt datautdrag fra Jærmuseets bookingsystem", $formatHeadingBig);
$linje++; $linje++;

$worksheet4->write($linje, 0, 'Uke', $formatHeading);
$worksheet4->write($linje, 1, 'Barn', $formatHeading);
$worksheet4->write($linje, 2, 'Voksne', $formatHeading);
$worksheet4->write($linje, 3, 'Totalt', $formatHeading);
$worksheet4->write($linje, 4, 'Antall bookinger', $formatHeading);
foreach ($stats_week as $id => $valarray)
{
	$linje++;
	if($id == 0)
		$name = '';
	else
		$name = $valarray['Name'];
	$worksheet4->write($linje, 0, $name, $formatRight);
	$worksheet4->write($linje, 1, $valarray['c']);
	$worksheet4->write($linje, 2, $valarray['a']);
	$worksheet4->write($linje, 3, $valarray['p']);
	$worksheet4->write($linje, 4, $valarray['e']);
}
$linje++;
$worksheet4->write($linje, 0, 'Totalt', $formatSum);
$worksheet4->write($linje, 1, $sum['c'], $formatSum);
$worksheet4->write($linje, 2, $sum['a'], $formatSum);
$worksheet4->write($linje, 3, $sum['p'], $formatSum);
$worksheet4->write($linje, 4, $sum['e'], $formatSum);



/* #### MONTHS #### */
$worksheet5 =& $workbook->addWorksheet('Måned');
$worksheet5->hideGridlines();
$worksheet5->setColumn(0, 0, 14);
$worksheet5->setColumn(1, 3, 7);
$worksheet5->setColumn(4, 4, 17);

$linje = 0;
$worksheet5->write($linje, 0, "Månedsfordelt datautdrag fra Jærmuseets bookingsystem", $formatHeadingBig);
$linje++; $linje++;

$worksheet5->write($linje, 0, 'Måned', $formatHeading);
$worksheet5->write($linje, 1, 'Barn', $formatHeading);
$worksheet5->write($linje, 2, 'Voksne', $formatHeading);
$worksheet5->write($linje, 3, 'Totalt', $formatHeading);
$worksheet5->write($linje, 4, 'Antall bookinger', $formatHeading);
foreach ($stats_month as $id => $valarray)
{
	$linje++;
	if($id == 0)
		$name = '';
	else
		$name = $valarray['Name'];
	$worksheet5->write($linje, 0, $name, $formatRight);
	$worksheet5->write($linje, 1, $valarray['c']);
	$worksheet5->write($linje, 2, $valarray['a']);
	$worksheet5->write($linje, 3, $valarray['p']);
	$worksheet5->write($linje, 4, $valarray['e']);
}
$linje++;
$worksheet5->write($linje, 0, 'Totalt', $formatSum);
$worksheet5->write($linje, 1, $sum['c'], $formatSum);
$worksheet5->write($linje, 2, $sum['a'], $formatSum);
$worksheet5->write($linje, 3, $sum['p'], $formatSum);
$worksheet5->write($linje, 4, $sum['e'], $formatSum);



/* #### YEARS #### */
$worksheet6 =& $workbook->addWorksheet('År');
$worksheet6->hideGridlines();
$worksheet6->setColumn(0, 0, 10);
$worksheet6->setColumn(1, 3, 7);
$worksheet6->setColumn(4, 4, 17);

$linje = 0;
$worksheet6->write($linje, 0, "Årsfordelt datautdrag fra Jærmuseets bookingsystem", $formatHeadingBig);
$linje++; $linje++;

$worksheet6->write($linje, 0, 'År', $formatHeading);
$worksheet6->write($linje, 1, 'Barn', $formatHeading);
$worksheet6->write($linje, 2, 'Voksne', $formatHeading);
$worksheet6->write($linje, 3, 'Totalt', $formatHeading);
$worksheet6->write($linje, 4, 'Antall bookinger', $formatHeading);
foreach ($stats_year as $id => $valarray)
{
	$linje++;
	if($id == 0)
		$name = '';
	else
		$name = $valarray['Name'];
	$worksheet6->write($linje, 0, $name, $formatRight);
	$worksheet6->write($linje, 1, $valarray['c']);
	$worksheet6->write($linje, 2, $valarray['a']);
	$worksheet6->write($linje, 3, $valarray['p']);
	$worksheet6->write($linje, 4, $valarray['e']);
}
$linje++;
$worksheet6->write($linje, 0, 'Totalt', $formatSum);
$worksheet6->write($linje, 1, $sum['c'], $formatSum);
$worksheet6->write($linje, 2, $sum['a'], $formatSum);
$worksheet6->write($linje, 3, $sum['p'], $formatSum);
$worksheet6->write($linje, 4, $sum['e'], $formatSum);


/* #### METADATA #### */
$worksheetMeta =& $workbook->addWorksheet('Metadata');
$worksheetMeta->hideGridlines();
$worksheetMeta->setColumn(0, 0, 20);
$worksheetMeta->setColumn(1, 1, 15);
$worksheetMeta->setColumn(2, 2, 20);
$linje = 0;
$worksheetMeta->write($linje, 0, "Om datautdrag fra Jærmuseets bookingsystemet", $formatHeadingBig);
$linje++; $linje++;

$worksheetMeta->write($linje, 0, "Dataene i dette regnearket ble hentet fra Jærmuseets bookingsystem");
$linje++;
$worksheetMeta->write($linje, 0, "Dataene ble hentet:", $formatBold);
$worksheetMeta->write($linje, 1, ' '.date('d.m.Y H:i:s'));
$linje++;
//$worksheetMeta->write($linje, 0, "Filter brukt (som tekst):");
//$worksheetMeta->write($linje, 1, strip_tags(filterToText($filters)));
$worksheetMeta->write($linje, 0, 'For å reprodusere disse regnearkene (oppdatere, få opp liste med bookinger, osv) i bookingsystem, så kan du sette opp filterne under i søkemotoren (Søk i menyene).');
$linje++;$linje++;

$worksheetMeta->write($linje, 0, "Filter brukt:",$formatBold);
$worksheetMeta->write($linje, 1, "Verdi 1:",$formatBold);
$worksheetMeta->write($linje, 2, "Verdi 2:",$formatBold);
foreach($filters as $filter)
{
	$linje++;
	$worksheetMeta->write($linje, 0, $alternatives[$filter[0]]['name']);
	//$worksheetMeta->write($linje, 1, $filter[1]);
	//$worksheetMeta->write($linje, 2, $filter[2]);
	
	switch ($alternatives[$filter[0]]['type']) {
		case 'bool':
		case 'select':
		case 'id':
		case 'id2':
		case 'text':
			$worksheetMeta->write($linje, 1, "");
		break;
		
		case 'date':
		case 'num':
			switch($filter[2]) {
				case '=':	$worksheetMeta->write($linje, 1, _('is'));							break;
				case '>';	$worksheetMeta->write($linje, 1, _('is bigger than'));				break;
				case '>=':	$worksheetMeta->write($linje, 1, _('is bigger than or same as'));	break;
				case '<':	$worksheetMeta->write($linje, 1, _('is less than'));				break;
				case '<=':	$worksheetMeta->write($linje, 1, _('is less than or same as'));		break;
			}
	}
	
	if($alternatives[$filter[0]]['type'] == 'date' && $filter[1] == 'current') {
		$worksheetMeta->write($linje, 2, $filter[1]);
	}
	elseif($alternatives[$filter[0]]['type'] == 'date') {
		$worksheetMeta->write($linje, 2, date('H:i d-m-Y', $filter[1]));
	}
	elseif($alternatives[$filter[0]]['type'] == 'bool') {
		if($filter[1])
			$worksheetMeta->write($linje, 2, _('true'));
		else
			$worksheetMeta->write($linje, 2, _('false'));
	}
	elseif($alternatives[$filter[0]]['type'] == 'select') {
		$worksheetMeta->write($linje, 2, $alternatives[$filter[0]]['choice'][$filter[1]]);
	}
	elseif($alternatives[$filter[0]]['type'] == 'text') {
		$worksheetMeta->write($linje, 2, '"'.$filter[1].'"');
	}
	elseif($alternatives[$filter[0]]['type'] == 'id') {
		if(isset($alternatives[$filter[0]]['table']) && count($alternatives[$filter[0]]['table']))
		{
			$table = $alternatives[$filter[0]]['table'];
			$Q_id = mysql_query('
				SELECT 
					'.$table['id_field'].' AS id, 
					'.$table['value_field'].' AS value
				FROM '.$table['table'].'
				WHERE '.$table['id_field'].' = "'.$filter[1].'"');
			if(mysql_num_rows($Q_id))
				$worksheetMeta->write($linje, 2, mysql_result($Q_id, '0', 'value').' (id '.mysql_result($Q_id, '0', 'id').')');
			else
				$worksheetMeta->write($linje, 2, 'id '.$filter[1]);
		}
		else
			$worksheetMeta->write($linje, 2, 'id '.$filter[1]);
	}
	else {
		$worksheetMeta->write($linje, 2, $filter[1]);
	}
}
		
$workbook->close();
?>