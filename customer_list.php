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

require "glob_inc.inc.php";
filterMakeAlternatives();

print_header($day, $month, $year, $area);

$Q = mysql_query("SELECT customer_id FROM `customer` WHERE `slettet` = '0' ORDER BY customer_name");
echo '<h1>'._('Customers').'</h1>'.chr(10);

echo '- '.iconHTML('group_add').' <a href="customer_edit.php?returnToCustomerView=1">'._('Create new customer').'</a><br><br>'.chr(10);

echo '<table>'.chr(10);
//echo '	<tr>'.chr(10);
//echo '		<td><b>'._('Customer').'</b></td>'.chr(10);
//echo '		<td>&nbsp;</td>'.chr(10);
//echo '	</tr>'.chr(10).chr(10);
while($R = mysql_fetch_assoc($Q))
{
	$customer = getCustomer($R['customer_id']);
	if(count($customer))
	{
		$filter = addFilter(array(), 'customer_id', $customer['customer_id']);
		$filters_serialized = filterSerialized($filter);
		
		echo '	<tr>'.chr(10);
		echo '		<td><b>'.
			'<a href="customer.php?customer_id='.$customer['customer_id'].'">'.
			iconHTML('group').' '.
			$customer['customer_name'].'</a></b></td>'.chr(10);
		echo '		<td><font size="1">'.
		'<a href="customer_edit.php?customer_id='.$customer['customer_id'].'&amp;returnToCustomerList=1">'.
		iconHTML('group_edit').' '.
		_('Edit').'</a>'.
		' -:- <a href="entry_list.php?filters='.$filters_serialized.'">'.
		iconHTML('page_white').' '.
		_('View entries').'</a> ('.
		mysql_num_rows(mysql_query(genSQLFromFilters ($filter, 'entry_id'))).')'.
		'</font></td>'.chr(10);
		echo '	</tr>'.chr(10).chr(10);
	}
}
echo '</table>'.chr(10);
?>