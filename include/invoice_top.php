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
	Invoice-includes
*/

include_once("glob_inc.inc.php");

if(!$login['user_invoice'])
{
	print_header($day, $month, $year, $area);
	echo '<h1>'._('Invoice').'</h1>'.chr(10).chr(10);
	echo _('No access');
	
	exit();
}

require "libs/invoice.class.php";

filterMakeAlternatives();
$filters = array();
$filters = addFilter($filters, 'invoice', '1');
$filters = addFilter($filters, 'invoice_status', '1');
$filters = addFilter($filters, 'time_start', 'current', '<');
$SQL = genSQLFromFilters($filters, 'entry_id');
$num_invoice_tobemade = mysql_num_rows(mysql_query($SQL));

$filters = array();
$filters = addFilter($filters, 'invoice', '1');
$filters = addFilter($filters, 'invoice_status', '2');
$SQL = genSQLFromFilters($filters, 'entry_id');
$num_invoice_tobemade_ready = mysql_num_rows(mysql_query($SQL));

unset($SQL, $filters);

$num_invoice_not_payed = mysql_num_rows(mysql_query("select invoice_id from `invoice` where invoice_payed = 0 "));

/* Functions for printing of bookinglists */

function entrylist_invoice_tobemade_ready ($SQL)
{
	$Q = mysql_query($SQL.' order by `time_start`');
	if(!mysql_num_rows($Q))
	{
		echo _('No entries found.');
	}
	else
	{
		echo '<font color="red">'.mysql_num_rows($Q).'</font> '._('entries found.');
		echo '<br>'.chr(10).chr(10);
		echo '<table style="border-collapse: collapse;">'.chr(10);
		echo ' <tr>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Arrangementsdato</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>'._('Name').'</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>'._('Area').'</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Sum</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>&nbsp;</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Satt faktureringsklar av</b></td>'.chr(10);
		echo ' </tr>'.chr(10);
		while($R = mysql_fetch_assoc($Q))
		{
			$entry = getEntry($R['entry_id']);
			
			echo ' <tr>'.chr(10);
			
			// Starts
			echo '  <td style="border: 1px solid black;">';
			echo '<a href="day.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'.date('d',$entry['time_start']).'</a>-';
			echo '<a href="month.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'._(date('m',$entry['time_start'])).'</a>-';
			echo date('Y', $entry['time_start']);
			echo '</td>'.chr(10);
			
			// Name
			echo '  <td style="border: 1px solid black;"><a href="entry.php?entry_id='.$entry['entry_id'].'">'.
			$entry['entry_name'].'</a></td>'.chr(10);
			
			// Area
			echo '  <td style="border: 1px solid black;">';
			$area = getArea($entry['area_id']);
			if(count($area))
				echo $area['area_name'];
			echo '</td>'.chr(10);
			
			// Invoice
			echo '  <td style="border: 1px solid black;">kr '.smarty_modifier_commify($entry['faktura_belop_sum'],2,","," ").'</td>'.chr(10);
			
			echo '  <td style="border: 1px solid black;">';
			echo '<a href="invoice_create.php?entry_ids=;'.$entry['entry_id'].';">';
			echo iconHTML('coins').' ';
			echo 'Opprett faktura</a>';
			echo '</td>'.chr(10); 
			
			// Searching for who did set the entry ready for invoice
			$Q_user = mysql_query("SELECT * FROM `entry_log`
				WHERE `log_action` = 'edit' AND `log_action2` = 'invoice_readyfor' AND `entry_id` = '".$entry['entry_id']."'
				ORDER BY `log_time` DESC LIMIT 1");
			$user = array();
			if(mysql_num_rows($Q_user))
				$user = getUser(mysql_result($Q_user, '0', 'user_id'));
			echo '  <td style="border: 1px solid black;">';
			if(count($user))
				echo '<a href="user.php?user_id='.$user['user_id'].'">'.$user['user_name'].'</a>';
			else
				echo '&nbsp;';
			echo '</td>'.chr(10); 
			
			echo ' </tr>'.chr(10);
		}
	}
}

function entrylist_invoice_tobemade ($SQL)
{
	$Q = mysql_query($SQL.' order by `time_start`');
	if(!mysql_num_rows($Q))
	{
		echo _('No entries found.');
	}
	else
	{
		echo '<font color="red">'.mysql_num_rows($Q).'</font> '._('entries found.');
		echo '<br>'.chr(10).chr(10);
		echo '<table style="border-collapse: collapse;">'.chr(10);
		echo ' <tr>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Arrangementsdato</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>'._('Name').'</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>'._('Area').'</b></td>'.chr(10);
		echo ' </tr>'.chr(10);
		while($R = mysql_fetch_assoc($Q))
		{
			$entry = getEntry($R['entry_id']);
			
			echo ' <tr>'.chr(10);
			
			// Starts
			echo '  <td style="border: 1px solid black;">';
			echo '<a href="day.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'.date('d',$entry['time_start']).'</a>-';
			echo '<a href="month.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'._(date('m',$entry['time_start'])).'</a>-';
			echo date('Y', $entry['time_start']);
			echo '</td>'.chr(10);
			
			// Name
			echo '  <td style="border: 1px solid black;"><a href="entry.php?entry_id='.$entry['entry_id'].'">'.
			$entry['entry_name'].'</a></td>'.chr(10);
			
			// Area
			echo '  <td style="border: 1px solid black;">';
			$area = getArea($entry['area_id']);
			if(count($area))
				echo $area['area_name'];
			echo '</td>'.chr(10);
			
			echo ' </tr>'.chr(10);
		}
		echo '</table>';
	}
}

function invoicelist_payed_not ($Q)
{
	//$Q = mysql_query($SQL);
	if(!mysql_num_rows($Q))
	{
		echo '<i>Ingen fakturaer er registert som ikke betalt.</i>';
	}
	else
	{
		echo '<font color="red">'.mysql_num_rows($Q).'</font> fakturaer funnet';
		echo '<br>'.chr(10).chr(10);
		echo '<table style="border-collapse: collapse;">'.chr(10);
		echo ' <tr>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b class="hiddenprint">Vis mer</b><b class="print">Fakturaid</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Fakturadato</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Forfallsdato</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Sum</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Kunde</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;" class="hiddenprint">&nbsp;</td>'.chr(10);
		echo ' </tr>'.chr(10);
		while($R = mysql_fetch_assoc($Q))
		{
			$invoice = new invoice($R['invoice_id']);
			$invoice->get();
			
			echo ' <tr>'.chr(10);
			
			// invoice_id
			echo '  <td style="border: 1px solid black;">';
			echo '<a href="invoice_view.php?invoice_id='.$invoice->invoice_id.'" class="hiddenprint">'.iconHTML('coins').' Faktura '.$invoice->invoice_id.'</a>';
			echo '<b class="print">'.$invoice->invoice_id.'</b>';
			echo '</td>'.chr(10);
			
			// time
			echo '  <td style="border: 1px solid black;">';
			echo $invoice->invoice_time2['year'].'-'.
			$invoice->invoice_time2['month'].'-'.
			$invoice->invoice_time2['day'];
			echo '</td>'.chr(10);
			
			// time_due
			echo '  <td style="border: 1px solid black;">';
			if($invoice->invoice_time_due < date('Ymd'))
				echo '<font color="red">';
			echo $invoice->invoice_time_due2['year'].'-'.
			$invoice->invoice_time_due2['month'].'-'.
			$invoice->invoice_time_due2['day'];
			if($invoice->invoice_time_due < date('Ymd'))
				echo '</font>';
			echo '</td>'.chr(10);
			
			// sum
			echo '  <td style="border: 1px solid black;">kr&nbsp;';
			echo smarty_modifier_commify($invoice->invoice_payment_left,2,","," ");
			if($invoice->invoice_payed_amount > 0)
				echo ' (av '.smarty_modifier_commify($invoice->invoice_topay_total,2,","," ").')';
			echo '</td>'.chr(10);
			
			// Customer
			echo '  <td style="border: 1px solid black;">';
			if($invoice->invoice_to_customer_name != '')
				echo '<a href="customer.php?customer_id='.$invoice->invoice_to_customer_id.'">'.
				$invoice->invoice_to_customer_name.'</a>';
			else
				echo $invoice->invoice_to_line1;
			echo '</td>'.chr(10);
			
			// Options
			echo '  <td style="border: 1px solid black;" class="hiddenprint">';
			echo '<a href="invoice_payment.php?invoice_id='.$invoice->invoice_id.'">'.iconHTML('coins_add').' Registrer betaling</a>';
			echo '</td>'.chr(10);
			
			echo ' </tr>'.chr(10);
		}
		echo '</table>';
	}
}

function invoicelist_payed ($Q)
{
	//$Q = mysql_query($SQL);
	if(!mysql_num_rows($Q))
	{
		echo '<i>Ingen fakturaer er registert som betalt.</i>';
	}
	else
	{
		echo '<font color="red">'.mysql_num_rows($Q).'</font> fakturaer funnet';
		echo '<br>'.chr(10).chr(10);
		echo '<table style="border-collapse: collapse;">'.chr(10);
		echo ' <tr>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b class="hiddenprint">Vis mer</b><b class="print">Fakturaid</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Fakturadato</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Betalt</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Beløp</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Kunde</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;">&nbsp;</td>'.chr(10);
		echo ' </tr>'.chr(10);
		while($R = mysql_fetch_assoc($Q))
		{
			$invoice = new invoice($R['invoice_id']);
			$invoice->get();
			
			echo ' <tr>'.chr(10);
			
			// invoice_id
			echo '  <td style="border: 1px solid black;">';
			echo '<a href="invoice_view.php?invoice_id='.$invoice->invoice_id.'" class="hiddenprint">'.iconHTML('coins').' Faktura '.$invoice->invoice_id.'</a>';
			echo '<b class="print">'.$invoice->invoice_id.'</b>';
			echo '</td>'.chr(10);
			
			// time
			echo '  <td style="border: 1px solid black;">';
			echo $invoice->invoice_time2['year'].'-'.
			$invoice->invoice_time2['month'].'-'.
			$invoice->invoice_time2['day'];
			echo '</td>'.chr(10);
			
			// time_payed
			echo '  <td style="border: 1px solid black;">';
			echo nl2br($invoice->invoice_payment_time);
			echo '</td>'.chr(10);
			
			// sum
			echo '  <td style="border: 1px solid black;">kr&nbsp;'.
			smarty_modifier_commify($invoice->invoice_payed_amount,2,","," ").'</td>'.chr(10);
			
			// Customer
			echo '  <td style="border: 1px solid black;">';
			if($invoice->invoice_to_customer_name != '')
				echo '<a href="customer.php?customer_id='.$invoice->invoice_to_customer_id.'">'.
				$invoice->invoice_to_customer_name.'</a>';
			else
				echo $invoice->invoice_to_line1;
			echo '</td>'.chr(10);
			
			// Options
			echo '  <td style="border: 1px solid black;">';
			echo nl2br($invoice->invoice_payment_comment);
			echo '</td>'.chr(10);
			
			echo ' </tr>'.chr(10);
		}
		echo '</table>';
	}
}
