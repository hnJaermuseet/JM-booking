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

require "include/invoice_top.php";

$section = 'main';
require "include/invoice_menu.php";

echo '<h1>'._('Main page').'</h1>'.chr(10).chr(10);

$filters = array();
$filters = addFilter($filters, 'invoice', '1');
$filters = addFilter($filters, 'invoice_status', '3');
if($area_spesific)
	$filters = addFilter($filters, 'area_id', $area_invoice['area_id']);
$SQL = genSQLFromFilters($filters, 'entry_id');
$num_invoice_exported = mysql_num_rows(mysql_query($SQL));


$Q_area = mysql_query("select id as area_id, area_name from mrbs_area order by area_name");

echo '<table class="prettytable">'.chr(10);
echo '	<tr>'.chr(10);
echo '		<th>'._('Area').'</th>'.chr(10);
echo '		<th>Ikke gjennomf&oslash;rt</th>'.chr(10);
echo '		<th>Ikke klargjort</th>'.chr(10);
echo '		<th>Klar til fakturautsending</th>'.chr(10);
echo '		<th>Sendt til regnskap</th>'.chr(10);
echo '	</tr>'.chr(10).chr(10);
while($R = mysql_fetch_assoc($Q_area))
{
	$filters = array();
	$filters = addFilter($filters, 'invoice', '1');
	$filters = addFilter($filters, 'invoice_status', '1');
	$filters = addFilter($filters, 'time_start', 'current', '>');
	$filters = addFilter($filters, 'area_id', $R['area_id']);
	$SQL = genSQLFromFilters($filters, 'entry_id');
	$area_num_invoice_soon = mysql_num_rows(mysql_query($SQL));
	
	$filters = array();
	$filters = addFilter($filters, 'invoice', '1');
	$filters = addFilter($filters, 'invoice_status', '1');
	$filters = addFilter($filters, 'time_start', 'current', '<');
	$filters = addFilter($filters, 'area_id', $R['area_id']);
	$SQL = genSQLFromFilters($filters, 'entry_id');
	$area_num_invoice_tobemade = mysql_num_rows(mysql_query($SQL));
	
	$filters = array();
	$filters = addFilter($filters, 'invoice', '1');
	$filters = addFilter($filters, 'invoice_status', '2');
	$filters = addFilter($filters, 'area_id', $R['area_id']);
	$SQL = genSQLFromFilters($filters, 'entry_id');
	$area_num_invoice_tobemade_ready = mysql_num_rows(mysql_query($SQL));
	
	$filters = array();
	$filters = addFilter($filters, 'invoice', '1');
	$filters = addFilter($filters, 'invoice_status', '3');
	$filters = addFilter($filters, 'area_id', $R['area_id']);
	$SQL = genSQLFromFilters($filters, 'entry_id');
	$area_num_invoice_exported = mysql_num_rows(mysql_query($SQL));
	
	unset($SQL, $filters);
	
	echo '	<tr>'.chr(10);
	echo '		<td><b>'.$R['area_name'].'</b></td>'.chr(10);
	echo '		<td class="rightalign"><a href="invoice_soon.php?area_id='.$R['area_id'].'">'.
		$area_num_invoice_soon.'</a></td>'.chr(10);
	echo '		<td class="rightalign"><a href="invoice_tobemade.php?area_id='.$R['area_id'].'">'.
		$area_num_invoice_tobemade.'</a></td>'.chr(10);
	echo '		<td class="rightalign"><a href="invoice_tobemade_ready.php?area_id='.$R['area_id'].'">'.
		$area_num_invoice_tobemade_ready.'</a></td>'.chr(10);
	echo '		<td class="rightalign"><a href="invoice_exported.php?area_id='.$R['area_id'].'">'.
		$area_num_invoice_exported.'</a></td>'.chr(10);
	echo '	</tr>'.chr(10).chr(10);
}

	
echo '	<tr>'.chr(10);
echo '		<td><b>SUM</b></td>'.chr(10);
echo '		<td class="rightalign"><b><a href="invoice_soon.php?area_id='.$R['area_id'].'">'.
	$num_invoice_soon.'</a></b></td>'.chr(10);
echo '		<td class="rightalign"><b><a href="invoice_tobemade.php?area_id='.$R['area_id'].'">'.
	$num_invoice_tobemade.'</a></b></td>'.chr(10);
echo '		<td class="rightalign"><b><a href="invoice_tobemade_ready.php?area_id='.$R['area_id'].'">'.
	$num_invoice_tobemade_ready.'</a></b></td>'.chr(10);
echo '		<td class="rightalign"><b><a href="invoice_exported.php?area_id='.$R['area_id'].'">'.
	$num_invoice_exported.'</a></b></td>'.chr(10);
echo '	</tr>'.chr(10).chr(10);
echo '</table>';
