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

if($area_spesific)
	$redirect = 'invoice_tobemade_ready.php?area_id='.$area_invoice['area_id'];
else
	$redirect = 'invoice_tobemade_ready.php';

/*
 * Mark entries as invoiced
 * 
 * Input: entry_id[], entry_id[], etc
 */

if(!isset($invoice_sendto) || !is_array($invoice_sendto))
{
	echo '$invoice_sendto not set in config or is not array. Please see default config for example.';
	exit;
}
if(!isset($invoice_location))
{
	echo '$invoice_location not set in config. Please see default config for example.';
	exit;
}

if(!isset($_GET['entry_id']) || !is_array($_GET['entry_id']))
{
	header('Location: '.$redirect); // Just redirect back
	exit;
}

$entry_errors = false;
$entries = array();
foreach($_GET['entry_id'] as $id)
{
	$tmp_entry = getEntry($id);
	$id = $tmp_entry['entry_id'];
	$entries[$id] = $tmp_entry;
	
	
	$checkInvoice = checkInvoicedata($tmp_entry);
	
	if(count($checkInvoice[0]))
	{
		if(!$entry_errors)
		{
			$section = 'tobemade_ready';
			include "include/invoice_menu.php";
			$entry_errors = true;
			
			echo '<span class="hiddenprint">';
			$Q_area = mysql_query("select id as area_id, area_name from mrbs_area order by area_name");
			$num_area = mysql_num_rows($Q_area);
			
			$counter_area = 0;
			echo '<span style="font-size: 0.8em;">Filtrer p&aring; anlegg: ';
			while($R = mysql_fetch_assoc($Q_area))
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
		}
		
		echo '<h1>Booking '.
		'<a href="entry.php?entry_id='.$id.'">'.$id.'</a>'.
		' har feil med fakturagrunnlaget</h1>';
		echo '<div class="error" style="width: 500px;"><ul style="padding-left: 20px; margin: 0px;">';
		foreach($checkInvoice[0] as $error)
		{
			echo '<li>'.$error.'</li>';
		}
		echo '</ul></div>';
	}
}

if(!$entry_errors)
{
	if(count($entries))
	{
		mysql_query("
			INSERT INTO `invoiced` (
					`invoiced_id` ,
					`created` ,
					`emailed` ,
					`emailed_time` ,
					`pdf_name`
				)
				VALUES (
					NULL , 
					'".time()."', 
					'0', 
					'0', 
					''
				);");
		$invoiced_id = mysql_insert_id();
		
		if($invoiced_id <= 0)
		{
			echo '$invoiced_id not correct. File: '.__FILE__.' Line: '.__LINE__;
			exit;
		}
	}
	
	$pdf_invoicedata = array();
	$from = null; $to = null;
	foreach($entries as $entry)
	{
		/*
		 * Set new status
		 * Set new rev_num, time of edit, etc
		 */
		$rev_num = $entry['rev_num']+1;
		
		// Add to entry_invoiced
		mysql_query("INSERT INTO `entry_invoiced` (`entry_id` , `invoiced_id`) VALUES ('".$entry['entry_id']."', '".$invoiced_id."');");
		
		// Updating invoice status
		mysql_query(
			"UPDATE `entry` ".
				"SET ".
					"`invoice_status` = '3', ".
					"`user_last_edit` = '".$login['user_id']."', ".
					"`time_last_edit` = '".time()."', ".
					"`rev_num` = '$rev_num', ".
					"`invoice_exported_time` = '".time()."' ".
				" WHERE `entry_id` = '".$entry['entry_id']."' LIMIT 1 ;");
		
		$log_data = array();
		if(!newEntryLog($entry['entry_id'], 'edit', 'invoice_exported', $rev_num, $log_data))
		{
		}
		
		if(is_null($from) || $from > $entry['time_start'])
			$from = $entry['time_start'];
		if(is_null($to) || $to < $entry['time_end'])
			$to = $entry['time_end'];
		
		// Generate PDF data
		$smarty = new Smarty;
		templateAssignEntry('smarty', $entry);
		templateAssignEntryChanges('smarty', $entry, $entry['rev_num']);
		templateAssignSystemvars('smarty');
		$invoicedata[] = $smarty->fetch('file:fakturagrunnlag.tpl');
	}
	
	$pdffile = 'fakturagrunnlag-'.date('Ymd-His').'-'.count($entries).'_bookinger.pdf';
	require_once("libs/dompdf/dompdf_config.inc.php");
	
	$dompdf = new DOMPDF();
	$dompdf->set_paper('A4');
	$dompdf->load_html(implode($invoicedata));
	$dompdf->render();
	file_put_contents($invoice_location.$pdffile, $dompdf->output());
	
	// Update invoiced
	mysql_query("UPDATE `invoiced` SET `pdf_name` = '".$pdffile."' WHERE `invoiced_id` = '".$invoiced_id."' LIMIT 1 ;");
	
	
	// Send PDF in emails
	$subject = 'Fakturagrunnlag - '.date('d.m.Y', $from).' til '.date('d.m.Y', $to);
	
	if(count($entries) != 1)
		$er = 'er';
	else
		$er = '';
	$message_plain = 'Følgende '.count($entries).' booking'.$er.' (FROM_AREA) er med i vedlagt PDF-fil med fakturagrunnlag:'.chr(10).chr(10);
	$areas_inpdf = array();
	foreach($entries as $entry)
	{
		
		$message_plain .= '- ('.$entry['entry_id'].') '.html_entity_decode($entry['entry_title']).chr(10);
		
		$area = getArea ($entry['area_id']);
		if(count($area))
		{
			$message_plain .= '   '.$area['area_name'].chr(10);
			$areas_inpdf[$area['area_id']] = $area['area_name'];
		}
		
		$message_plain .= '   Sum eks mva: kr '.smarty_modifier_commify($entry['eks_mva_tot'], 2,',',' ').' + MVA kr '.smarty_modifier_commify($entry['faktura_belop_sum_mva'], 2,',',' ').chr(10);
		$message_plain .= '   Sum ink. mva: kr '.smarty_modifier_commify($entry['faktura_belop_sum'], 2,',',' ').chr(10).chr(10);
	}
	$message_plain .= chr(10).'Mvh. Bookingsystemet';
	if(count($areas_inpdf)) {
		$message_plain = str_replace('FROM_AREA', 'fra '.implode(', ', $areas_inpdf), $message_plain);
	} else {
		$message_plain = str_replace(' (FROM_AREA)', '', $message_plain);
	}
	
	
	foreach($invoice_sendto as $email)
	{
		emailSendInvoicePDF ($email, $invoice_location.$pdffile, $message_plain, $subject);
		mysql_query("INSERT INTO `invoiced_emails` (`email_addr` , `invoiced_id`) VALUES ('".$email."', '".$invoiced_id."');");
	}
	mysql_query("UPDATE `invoiced` SET `emailed_time` = '".time()."', `emailed` = '1' WHERE `invoiced_id` = '".$invoiced_id."' LIMIT 1 ;");
	
	
	// Redirect back
	//header('Location: '.$redirect);
	echo 'Redirect!';
	exit;
}