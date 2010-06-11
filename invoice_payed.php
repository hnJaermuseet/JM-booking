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

echo 'Invoice_payed is disabled. Not in use. '.
'Please contact hn@jaermuseet.no about the link you followed to come here.';
exit();

require "include/invoice_top.php";

$section = 'payed';
require "include/invoice_menu.php";

echo '<h1>'._('Payed').'</h1>'.chr(10).chr(10);
$Q_invoice = mysql_query("select * from `invoice` where invoice_payed = '1' order by invoice_id desc");
invoicelist_payed($Q_invoice);

if(isset($_GET['debug']))
{
	$Q_invoice = mysql_query("select * from `invoice` where invoice_payed = '1' order by invoice_id desc");
	if(!mysql_num_rows($Q_invoice))
	{
		echo '<i>'._('There are no invoices that are registered as payed.').'</i>';
	}
	{
		echo '<table>';
		while($R_invoice = mysql_fetch_assoc($Q_invoice))
		{
			$R_invoice['invoice_content'] = unserialize($R_invoice['invoice_content']);
			echo '<tr><td>'.$R_invoice['invoice_id'].'</td><td>'.nl2br(print_r($R_invoice, true)).'</td></tr>';
		}
		echo '</table>';
	}
}
?>