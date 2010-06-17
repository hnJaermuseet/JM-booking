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

$section = 'soon';
require "include/invoice_menu.php";

echo '<h1>Bookinger som ikke er gjennomført enda'.chr(10).chr(10);

if(isset($_GET['filters']))
{
	echo ' - endret filter';
	echo '</h1>'.chr(10).chr(10);
	
	$filters = filterGetFromSerialized($_GET['filters']);
	if(!$filters)
		$filters = array();
	
	$SQL = genSQLFromFilters($filters, 'entry_id');
	filterLink($filters, 'invoice_soon');	echo '<br>'.chr(10);
	filterPrint($filters);				echo '<br>'.chr(10);
	echo '<br>'.chr(10).chr(10);
	
	entrylist_invoice_soon($SQL);
}
else
{
	echo '</h1>'.chr(10).chr(10);
	
	$filters = array();
	$filters = addFilter($filters, 'invoice', '1');
	$filters = addFilter($filters, 'invoice_status', '1');
	$filters = addFilter($filters, 'time_end', 'current', '>');
	
	$SQL = genSQLFromFilters($filters, 'entry_id');
	filterLink($filters, 'invoice_soon');	echo '<br>'.chr(10);
	filterPrint($filters);				echo '<br>'.chr(10);
	echo '<br>'.chr(10).chr(10);
	
	entrylist_invoice_soon($SQL);
}
?>