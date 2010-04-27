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

$section = 'payed_not';
require "include/invoice_menu.php";

if(isset($_GET['debug']) && isset($_GET['invoice_id']))
{
	$invoice = new invoice($_GET['invoice_id']);
	$invoice->get();
	
	$invoice->createPDF();
	echo 'Jau...';
}

echo '<h1>'._('Not payed').'</h1>'.chr(10).chr(10);

$Q_invoice = mysql_query("select invoice_id from `invoice` where invoice_payed = 0 order by invoice_id");
invoicelist_payed_not($Q_invoice);

if(isset($_GET['debug']))
{
	$Q_invoice = mysql_query("select invoice_id from `invoice` where invoice_payed = 0 order by invoice_id");
	
	echo '<br><br>';
	if(!mysql_num_rows($Q_invoice))
	{
		echo '<i>'._('There are no invoices that are registered as not payed.').'</i>';
	}
	else
	{
		echo '<table>';
		while($R_invoice = mysql_fetch_assoc($Q_invoice))
		{
			$invoice = new invoice($R_invoice['invoice_id']);
			$invoice->get();
			//templateAssignInvoice('smarty', $invoice);
			echo '<tr><td>'.
			'<a href="'.$_SERVER['PHP_SELF'].'?invoice_id='.$invoice->invoice_id.'&debug=1">'.
			$invoice->invoice_id.'</a></td>'.
			'<td>'.
			'<a href="invoice_view.php?invoice_id='.$invoice->invoice_id.'">'.
			$invoice->invoice_id.'</a></td>'.
			'<td>'.str_replace("  ", "&nbsp; ", nl2br(print_r($invoice, true))).'</td></tr>';
		}
		echo '</table>';
	}
}
?>