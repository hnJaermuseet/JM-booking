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

function entrySetReady ($entry)
{
	global $invoice_sendto, $systemurl, $login;
	
	if(!count($entry))
	{
		echo __('No entry found.');
		return FALSE;
	}
	
	/*
	 * Set new status
	 * Set new rev_num, time of edit, etc
	 */
	$rev_num = $entry['rev_num']+1;
	$UPDATE = db()->prepare("UPDATE `entry` SET `invoice_status` = '2', `user_last_edit` = '".$login['user_id']."', `time_last_edit` = '".time()."', `rev_num` = '$rev_num' WHERE `entry_id` = '".$entry['entry_id']."' LIMIT 1 ;");
    $UPDATE->execute();
	
	$log_data = array();
	if(!newEntryLog($entry['entry_id'], 'edit', 'invoice_readyfor', $rev_num, $log_data))
	{
		echo __('Can\'t log the changes for the entry.');
		echo '<br><br>';
		return FALSE;
	}
	
	if(isset($invoice_sendto) && is_array($invoice_sendto))
	{
		$entry_summary = 'Bookingid: '.$entry['entry_id'].chr(10);
		
		
		$area = getArea($entry['area_id']);
		if(count($area))
		{
			foreach($invoice_sendto as $email)
			{
				emailSendDirect($email,
					'Booking klar til fakturering - '.$area['area_name'].', '.date('d.m.Y', $entry['time_start']).' ('.$entry['entry_id'].')',
					
					'Hei'.chr(10).chr(10).
					
					$login['user_name'].' har satt en ny booking, fra '.$area['area_name'].', klar til fakturering. Bookingen var fra '.date('d.m.Y', $entry['time_start']).'.'.chr(10).chr(10).
					
					'Gå inn på følgende adresse for å få tilsendt fakturagrunnlagene:'.chr(10).
					$systemurl.'/invoice_tobemade_ready.php'.chr(10).
					
					'Hvis det er flere fakturaer som er klar til fakturering, så kan samtlige hentes ut på likt.'.chr(10).chr(10).
					
					'Oppsummert booking:'.chr(10).
					'Bookingid: '.$entry['entry_id'].chr(10).
					'Tittel: '.html_entity_decode($entry['entry_name']).chr(10).
					'Anlegg: '.$area['area_name'].chr(10).
					'Sum eks mva: kr '.smarty_modifier_commify($entry['eks_mva_tot'], 2,',',' ').chr(10).
					' + MVA kr '.smarty_modifier_commify($entry['faktura_belop_sum_mva'], 2,',',' ').chr(10).
					'Sum ink. mva: kr '.smarty_modifier_commify($entry['faktura_belop_sum'], 2,',',' ').chr(10).
					chr(10).
					
					'Mvh. Bookingsystemet');
			}
		}
	}
	
	return TRUE;
}

?>