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

/*
	Invoice-include - middel/menu
*/


filterMakeAlternatives();
$filters = array();
$filters = addFilter($filters, 'invoice', '1');
$filters = addFilter($filters, 'invoice_status', '1');
$filters = addFilter($filters, 'time_start', 'current', '>');
if($area_spesific)
	$filters = addFilter($filters, 'area_id', $area_invoice['area_id']);
$SQL = genSQLFromFilters($filters, 'entry_id');
$num_invoice_soon = mysql_num_rows(mysql_query($SQL));

$filters = array();
$filters = addFilter($filters, 'invoice', '1');
$filters = addFilter($filters, 'invoice_status', '1');
$filters = addFilter($filters, 'time_start', 'current', '<');
if($area_spesific)
	$filters = addFilter($filters, 'area_id', $area_invoice['area_id']);
$SQL = genSQLFromFilters($filters, 'entry_id');
$num_invoice_tobemade = mysql_num_rows(mysql_query($SQL));

$filters = array();
$filters = addFilter($filters, 'invoice', '1');
$filters = addFilter($filters, 'invoice_status', '2');
if($area_spesific)
	$filters = addFilter($filters, 'area_id', $area_invoice['area_id']);
$SQL = genSQLFromFilters($filters, 'entry_id');
$num_invoice_tobemade_ready = mysql_num_rows(mysql_query($SQL));

unset($SQL, $filters);

print_header($day, $month, $year, $area);

$add_to_href = '';
if($area_spesific)
	$add_to_href = '?area_id='.$area_invoice['area_id'];

//layout
echo '<div class="hiddenprint">';
echo '<h2>'._("Invoice");
if($area_spesific)
	echo ' - viser '.$area_invoice['area_name'];
echo '</h2>'.chr(10);

if(!$area_spesific && $area_failed)
{
	echo '<div class="notice">'._h('The area you tried to access does not exist. Viewing data for all areas instead.').'</div>';
}
echo '<a href="invoice_main.php"';
if($section=="main") echo "style='color:red'";
echo '>'._('Main page').'</a> -:- ';

echo '<a href="invoice_soon.php'.$add_to_href.'"';
if($section=="soon") echo "style='color:red'";
echo '>Ikke gjennomført</a> ('.$num_invoice_soon.') -:- ';

echo '<a href="invoice_tobemade.php'.$add_to_href.'"';
if($section=="tobemade") echo "style='color:red'";
echo '>Ikke klargjort</a> ('.$num_invoice_tobemade.') -:- ';

echo '<a href="invoice_tobemade_ready.php'.$add_to_href.'"';
if($section=="tobemade_ready") echo "style='color:red'";
echo '>Klar til fakturering</a> ('.$num_invoice_tobemade_ready.') -:- ';

echo '<a href="invoice_exported.php'.$add_to_href.'"';
if($section=="exported") echo "style='color:red'";
echo '>Sendt til regnskap</a> -:- ';

echo '<a href="invoiced_list.php'.$add_to_href.'"';
if($section=="invoiced") echo "style='color:red'";
echo '>Fakturagrunnlag</a> -:- ';

echo '<hr>'.chr(10);
echo '</div>';

echo '<div class="print" style="font-size: 8px"><i>Generert '.date('H:i:s d-m-Y').', '.$login['user_name'].'</i></div>';
