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

$section = 'tobemade_ready';
require "include/invoice_menu.php";

echo '<h1 style="margin-bottom: 0px;">Klar til fakturautsending';

if(isset($_GET['filters']))
{
	echo ' - endret filter';
	echo '</h1>'.chr(10).chr(10);
	
	$filters = filterGetFromSerialized($_GET['filters']);
	if(!$filters)
		$filters = array();
	
	$SQL = genSQLFromFilters($filters, 'entry_id');
	filterLink($filters, 'invoice_tobemade_ready');	echo '<br>'.chr(10);
	filterPrint($filters);				echo '<br>'.chr(10);
	echo '<br>'.chr(10).chr(10);
	
	$tamed_booking = true;
	foreach($filters as $filter) {
		if($filter[0] == 'tamed_booking')
		{
			$tamed_booking  = $filter[1];
		}
	}
	entrylist_invoice_tobemade_ready($SQL, $tamed_booking);
}
else
{
	echo '</h1>'.chr(10).chr(10);
	
	echo '<span class="hiddenprint">';
	$Q_area = db()->prepare("select id as area_id, area_name from mrbs_area order by area_name");
    $Q_area->execute();
	$num_area = $Q_area->rowCount();
	
	$counter_area = 0;
	echo '<span style="font-size: 0.8em;">Filtrer p&aring; anlegg: ';
	while($R = $Q_area->fetch(PDO::FETCH_ASSOC))
	{
		$counter_area++;
		if($area_spesific && $area_invoice['area_id'] == $R['area_id'])
			echo '<b>';
		echo '<a href="'.$_SERVER['PHP_SELF'].'?area_id='.$R['area_id'].'">'.$R['area_name'].'</a>';
		if($area_spesific && $area_invoice['area_id'] == $R['area_id'])
			echo '</b>';
		if($counter_area != $num_area)
		echo ' -:- ';
	}
	echo '<br /><br /></span>';
	
	$filters = array();
	$filters = addFilter($filters, 'invoice', '1');
	$filters = addFilter($filters, 'invoice_status', '2');
	if($area_spesific)
		$filters = addFilter($filters, 'area_id', $area_invoice['area_id']);
	
	$SQL = genSQLFromFilters($filters, 'entry_id');
	filterLink($filters, 'invoice_tobemade_ready');	echo '<br>'.chr(10);
	echo '</span>';
	filterPrint($filters);				echo '<br>'.chr(10);
	echo '<br>'.chr(10).chr(10);
	
	$tamed_booking = true;
	foreach($filters as $filter) {
		if($filter[0] == 'tamed_booking')
		{
			$tamed_booking  = $filter[1];
		}
	}
	entrylist_invoice_tobemade_ready($SQL, $tamed_booking, $area_spesific);
}