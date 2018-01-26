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

if(!$login['user_invoice'] && !$login['user_invoice_setready'])
{
	print_header($day, $month, $year, $area);
	echo '<h1>'.__('Invoice').'</h1>'.chr(10).chr(10);
	echo __('No access');
	
	exit();
}

$area_failed = false;
if(isset($_GET['area_id']) && $_GET['area_id'] != '')
{
	$area_invoice = getArea($_GET['area_id']);
	if(count($area_invoice))
	{
		$area_spesific = true;
	}
	else
	{
		$area_spesific  = false;
		$area_failed    = true;
		unset($_GET['area_id']);
		unset($area_invoice);
	}
}
else
{
	$area_spesific = false;
}


/* Functions for printing of bookinglists */

function entrylist_invoice_soon ($SQL, $tamed_booking)
{
	$Q = mysql_query($SQL.' order by `time_start`');
	if(!$tamed_booking || !mysql_num_rows($Q))
	{
		echo __('No entries found.');
	}
	else
	{
		echo '<font color="red">'.mysql_num_rows($Q).'</font> '.__('entries found.');
		echo '<br>'.chr(10).chr(10);
		echo '<table style="border-collapse: collapse;">'.chr(10);
		echo ' <tr>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Arrangementsdato</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>'.__('Name').'</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>'.__('Area').'</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black; text-align: right;"><b>Sum</b></td>'.chr(10);
		echo ' </tr>'.chr(10);
		$entry_ids = array();
		while($R = mysql_fetch_assoc($Q))
		{
			$entry = getEntry($R['entry_id']);
			$entry_ids[] = $entry['entry_id'];
			
			echo ' <tr>'.chr(10);
			
			// Starts
			echo '  <td style="border: 1px solid black;">';
			echo '<a href="day.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'.date('d',$entry['time_start']).'</a>-';
			echo '<a href="month.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'.__(date('m',$entry['time_start'])).'</a>-';
			echo date('Y', $entry['time_start']);
			echo '</td>'.chr(10);
			
			// Name
			echo '  <td style="border: 1px solid black;"><a href="entry.php?entry_id='.$entry['entry_id'].'">'.
			$entry['entry_name'].'</a>';
			
			$checkInvoice = checkInvoicedata($entry);
			if(count($checkInvoice[0]))
			{
				echo '<br /><br /><b>Feil med fakturagrunnlag:</b><br />';
				echo '<div class="error"><ul style="padding-left: 20px; margin: 0px;">';
				foreach($checkInvoice[0] as $error)
				{
					echo '<li>'.$error.'</li>';
				}
				echo '</ul></div>';
			}
			if(count($checkInvoice[1]))
			{
				if(!count($checkInvoice[0]))
					echo '<br /><br />';
				echo '<b>Advarsler p&aring; fakturagrunnlag:</b><br />';
				echo '<div class="notice"><ul style="padding-left: 20px; margin: 0px;">';
				foreach($checkInvoice[1] as $warnings)
				{
					echo '<li>'.$warnings.'</li>';
				}
				echo '</ul></div>';
			}
			
			echo '</td>'.chr(10);
			
			// Area
			echo '  <td style="border: 1px solid black;">';
			$area = getArea($entry['area_id']);
			if(count($area))
				echo $area['area_name'];
			echo '</td>'.chr(10);
			
			// Invoice
			echo '  <td style="border: 1px solid black;">kr&nbsp;'.smarty_modifier_commify($entry['faktura_belop_sum'],2,",","&nbsp;").'</td>'.chr(10);
			
			echo ' </tr>'.chr(10);
		}
		echo '</table>';
	}
}

function entrylist_invoice_tobemade_ready ($SQL, $tamed_booking)
{
	global $area_spesific, $area_invoice;
	global $invoice_sendto;
	
	$Q = mysql_query($SQL.' order by `time_start`');
	if(!$tamed_booking || !mysql_num_rows($Q))
	{
		echo __('No entries found.');
	}
	else
	{
		echo '<font color="red">'.mysql_num_rows($Q).'</font> '.__('entries found.');
		echo '<br>'.chr(10).chr(10);
		
		echo '<form action="invoice_export.php" method="get" id="invoice_export">'.chr(10).chr(10);
		if($area_spesific)
			echo '<input '.
						'type="hidden" '.
						'checked="checked" '.
						'value="'.$area_invoice['area_id'].'" '.
						'name="area_id"'.
					'>';
		
		echo '<table style="border-collapse: collapse;">'.chr(10);
		echo ' <tr>'.chr(10);
		echo '  <td style="border: 1px solid black;">&nbsp;</td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Arrangementsdato</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>'.__('Name').'</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>'.__('Area').'</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Sum</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>&nbsp;</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Satt faktureringsklar av</b></td>'.chr(10);
		echo ' </tr>'.chr(10);
		$entry_ids = array();
		while($R = mysql_fetch_assoc($Q))
		{
			$entry = getEntry($R['entry_id']);
			$entry_ids[] = $entry['entry_id'];
			
			echo ' <tr>'.chr(10);
			
			// Checkbox
			echo '  <td style="border: 1px solid black;">'.
				'<input '.
					'type="checkbox" '.
					'checked="checked" '.
					'value="'.$entry['entry_id'].'" '.
					'name="entry_id[]"'.
				'>'.
			'</td>';
			
			// Starts
			echo '  <td style="border: 1px solid black;">';
			echo '<a href="day.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'.date('d',$entry['time_start']).'</a>-';
			echo '<a href="month.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'.__(date('m',$entry['time_start'])).'</a>-';
			echo date('Y', $entry['time_start']);
			echo '</td>'.chr(10);
			
			// Name
			echo '  <td style="border: 1px solid black;"><a href="entry.php?entry_id='.$entry['entry_id'].'">'.
			$entry['entry_name'].'</a>';
			
			$checkInvoice = checkInvoicedata($entry);
			if(count($checkInvoice[0]))
			{
				echo '<br /><br /><b>Feil med fakturagrunnlag:</b><br />';
				echo '<div class="error"><ul style="padding-left: 20px; margin: 0px;">';
				foreach($checkInvoice[0] as $error)
				{
					echo '<li>'.$error.'</li>';
				}
				echo '</ul></div>';
			}
			if(count($checkInvoice[1]))
			{
				if(!count($checkInvoice[0]))
					echo '<br /><br />';
				echo '<b>Advarsler p&aring; fakturagrunnlag:</b><br />';
				echo '<div class="notice"><ul style="padding-left: 20px; margin: 0px;">';
				foreach($checkInvoice[1] as $warnings)
				{
					echo '<li>'.$warnings.'</li>';
				}
				echo '</ul></div>';
			}
			
			echo '</td>'.chr(10);
			
			// Area
			echo '  <td style="border: 1px solid black;">';
			$area = getArea($entry['area_id']);
			if(count($area))
				echo $area['area_name'];
			echo '</td>'.chr(10);
			
			// Invoice
			echo '  <td style="border: 1px solid black; text-align: right;">kr&nbsp;'.smarty_modifier_commify($entry['faktura_belop_sum'],2,",","&nbsp;").'</td>'.chr(10);
			
			echo '  <td style="border: 1px solid black;">';
			echo '<a href="entry_invoice.php?entry_id='.$entry['entry_id'].'">';
			echo iconHTML('coins').'&nbsp;';
			echo 'Vis&nbsp;fakturagrunnlag</a>';
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
			if(mysql_num_rows($Q_user))
				echo '<br />'.date('d.m.Y \k\l H:i', mysql_result($Q_user, '0', 'log_time'));
			echo '</td>'.chr(10); 
			
			echo ' </tr>'.chr(10);
		}
		echo '</table>';
		
		echo '<div style="font-size: 1.6em; margin-top: 20px; margin-left: 10px;">'.
		'<a href="#" id="invoice_export_submit">'.
		'<img src="img/Crystal_Clear_action_db_comit.png" style="border: 0px solid black;" height="32"> '.
		'Merker som sendt til regnskap og send fakturagrunnlag p&aring; e-post'.
		'</a>';
		if(isset($invoice_sendto) && is_array($invoice_sendto))
			echo '<br /><span style="display: inline-block; width: 32px">&nbsp;</span> (sendes til '.implode($invoice_sendto, ', ').')';
		echo '</div>';
		
		echo '</form>';
		echo '<script type="text/javascript">'.
		'$("#invoice_export_submit").click(function () { $("#invoice_export").submit(); });'.chr(10);
		echo '</script>';
	}
}

function entrylist_invoice_tobemade ($SQL, $tamed_booking, $area_spesific = false)
{
	$Q = mysql_query($SQL.' order by `time_start`');
	
	if(!$tamed_booking || !mysql_num_rows($Q))
	{
		echo __('No entries found.');
	}
	else
	{
		echo '<font color="red">'.mysql_num_rows($Q).'</font> '.__('entries found.');
		echo '<br>'.chr(10).chr(10);
		echo '<table style="border-collapse: collapse;">'.chr(10);
		echo ' <tr>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Arrangementsdato</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>'.__('Name').'</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>'.__('Area').'</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Sum</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>&nbsp;</b></td>'.chr(10);
		echo ' </tr>'.chr(10);
		while($R = mysql_fetch_assoc($Q))
		{
			$entry = getEntry($R['entry_id']);
			
			echo ' <tr>'.chr(10);
			
			// Starts
			echo '  <td style="border: 1px solid black;">';
			echo '<a href="day.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'.date('d',$entry['time_start']).'</a>-';
			echo '<a href="month.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'.__(date('m',$entry['time_start'])).'</a>-';
			echo date('Y', $entry['time_start']);
			echo '</td>'.chr(10);
			
			// Name
			echo '  <td style="border: 1px solid black;"><a href="entry.php?entry_id='.$entry['entry_id'].'">'.
			$entry['entry_name'].'</a>';
			
			$checkInvoice = checkInvoicedata($entry);
			if(count($checkInvoice[0]))
			{
				echo '<br /><br /><b>Feil med fakturagrunnlag:</b><br />';
				echo '<div class="error"><ul style="padding-left: 20px; margin: 0px;">';
				foreach($checkInvoice[0] as $error)
				{
					echo '<li>'.$error.'</li>';
				}
				echo '</ul></div>';
			}
			if(count($checkInvoice[1]))
			{
				if(!count($checkInvoice[0]))
					echo '<br /><br />';
				echo '<b>Advarsler p&aring; fakturagrunnlag:</b><br />';
				echo '<div class="notice"><ul style="padding-left: 20px; margin: 0px;">';
				foreach($checkInvoice[1] as $warnings)
				{
					echo '<li>'.$warnings.'</li>';
				}
				echo '</ul></div>';
			}
			
			echo '</td>'.chr(10);
			
			// Area
			echo '  <td style="border: 1px solid black;">';
			$area = getArea($entry['area_id']);
			if(count($area))
				echo $area['area_name'];
			echo '</td>'.chr(10);
			
			// Invoice
			echo '  <td style="border: 1px solid black; text-align: right;">kr&nbsp;'.smarty_modifier_commify($entry['faktura_belop_sum'],2,",","&nbsp;").'</td>'.chr(10);
			
			// Set ready
			echo '  <td style="border: 1px solid black;">';
			echo '<a href="invoice_setready.php?entry_id='.$entry['entry_id'];
			if($area_spesific)
				echo '&amp;return=invoice_tobemade_area';
			else
				echo '&amp;return=invoice_tobemade';
			echo '">'.
			'Sett&nbsp;faktureringsklar&nbsp;';
			
			if (count($checkInvoice[0])) // Errors
				echo iconHTML ('arrow_right_red');
			elseif (count($checkInvoice[1])) // Warnings
				echo iconHTML ('arrow_right_yellow');
			else // None
				echo iconHTML ('arrow_right');
			
			echo '</a>';
			echo '</td>'.chr(10);
			
			echo ' </tr>'.chr(10);
		}
		echo '</table>';
	}
}

function entrylist_invoice_exported ($SQL, $tamed_booking)
{
	$Q = mysql_query($SQL.' order by `invoice_exported_time`');
	if(!$tamed_booking || !mysql_num_rows($Q))
	{
		echo __('No entries found.');
	}
	else
	{
		echo '<font color="red">'.mysql_num_rows($Q).'</font> '.__('entries found.');
		echo '<br>'.chr(10).chr(10);
		echo '<table style="border-collapse: collapse;">'.chr(10);
		echo ' <tr>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Sendt til regnskap</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>Arrangementsdato</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>'.__('Name').'</b></td>'.chr(10);
		echo '  <td style="border: 1px solid black;"><b>'.__('Area').'</b></td>'.chr(10);
		//echo '  <td style="border: 1px solid black;"><b>&nbsp;</b></td>'.chr(10);
		echo ' </tr>'.chr(10);
		while($R = mysql_fetch_assoc($Q))
		{
			$entry = getEntry($R['entry_id']);
			
			echo ' <tr>'.chr(10);
			
			// invoice_exported_time
			echo '  <td style="border: 1px solid black;">'.date('H:i d.m.Y', $entry['invoice_exported_time']).'</td>'.chr(10);
			
			// Starts
			echo '  <td style="border: 1px solid black;">';
			echo '<a href="day.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'.date('d',$entry['time_start']).'</a>-';
			echo '<a href="month.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'.__(date('m',$entry['time_start'])).'</a>-';
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
			
			// Set ready
			/*echo '  <td style="border: 1px solid black;">';
			echo '<a href="invoice_setready.php?entry_id='.$entry['entry_id'].'">'.
			' Sett faktureringsklar '.iconHTML ('arrow_right').'</a>';
			echo '</td>'.chr(10);
			*/
			
			echo ' </tr>'.chr(10);
		}
		echo '</table>';
	}
}

/**
 * Checks the invoice data an entry contains
 * 
 * @param array getEntry-array
 * @return array Things that failed, [0] is errors, [1] is warnings
 */
function checkInvoicedata ($entry)
{
	$errors = array();
	$warnings = array();

	// Any invoice content?
	if(!count($entry['invoice_content']))
	{
		$errors[] = 'Ingen fakturalinjer er lagt inn p&aring; bookingen. Det m&aring; v&aelig;re fakturalinjer for at fakturering skal kunne finne sted.';
	}
	else
	{
		$line_num = 0;
		foreach($entry['invoice_content'] as $line)
		{
			$line_num++;
			if($line['name'] == '')
			{
				$warnings[] = 'Fakturalinje nr '.$line_num.' har ikke beskrivelse.';
			}
			else
			{
				//$text_split = explode(chr(10), $line['name']);
				//foreach($text_split as $this_text)
				//{
				//	$this_text = trim($this_text);
				//	if(strlen($this_text) > 50)
				//	{
				//		$warnings[] = 
				//			'Fakturalinje nr '.$line_num.' har beskrivelse som har bredd lenger enn 50 bokstaver. '.
				//			'Ved eksport blir teksten kuttet til 50 bokstaver. '.
				//			'Du kan fikse dette ved &aring; fordele teksten p&aring; to linjer i beskrivelsesfeltet.';
				//	}
				//}
			}
			
			// Checking amount
			if($line['antall'] < 0)
			{
				$warnings[] = 'Fakturalinje nr '.$line_num.' har minus i antall. Pris per stykk kan v&aelig;re minus.';
			}
			elseif($line['antall'] == 0)
			{
				$warnings[] = 'Fakturalinje nr '.$line_num.' har null i antall.';
			}
			
			// Checking a few export limits
			if($line['belop_hver'] > 99999999)
			{
				$errors[] = 'Fakturalinje nr '.$line_num.' har for stort bel&oslash;p per stykk. Maks er 99 millioner. Kan ikke fortsette uten at dette rettes f.eks. ved &aring; dele opp i flere fakturalinjer.';
			}
			if($line['belop_sum_netto'] > 9999999999)
			{
				$errors[] = 'Fakturalinje nr '.$line_num.' har for stort bel&oslash;p totalt. Maks er 9999 millioner. Kan ikke fortsette uten at dette rettes f.eks. ved &aring; dele opp i flere fakturalinjer.';
			}
			if($line['antall'] > 99999999)
			{
				$errors[] = 'Fakturalinje nr '.$line_num.' har for stort antall. Maks er 99 millioner. Kan ikke fortsette uten at dette rettes f.eks. ved &aring; dele opp i flere fakturalinjer.';
			}
		}
	}
	
	// Checking customer
	if($entry['customer_id'] == 0)
	{
		$errors[] = 'Ingen kunde er valgt. Dette m&aring; rettes.';
	}
	else
	{
		$customer = getCustomer($entry['customer_id']);
		if(!count($customer))
		{
			$errors[] = 'Finner ikke kunden som er valgt. Det kan hende den er slettet. Velg en ny kunde. Dette m&aring; rettes.';
		}
		else
		{
			// We have got the customer

			if(strlen($customer['customer_name']) < 1)
				$errors[] = 'Kundens navn er ikke lagt inn p&aring; kunden. Dette g&aring;r ikke og m&aring; rettes. Kunde m&aring; endres.';
			
			// Checking address
			if($entry['invoice_address_id'] == 0)
			{
				$errors[] = 'Ingen fakturaadresse er valgt. Dette m&aring; rettes.';
			}
			else
			{
				$address = getAddress($entry['invoice_address_id']);
				if(!count($address))
				{
					$errors[] = 'Finner ikke addressen som er valgt. Det kan hende den er slettet. Velg en ny adresse. Dette m&aring; rettes.';
				}
				else
				{
					// We got a address
					if($address['address_postalnum'] == '')
					{
						$warnings[] = 'Det er ikke valgt noe postnummer/poststed for adressen.';
					}
				}
			}
		}
	}
	
	return array($errors, $warnings);
}