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

function send_iCal($entry_id)
{
	global $login;
	
	// ## Getting entry
	$entry = getEntry($entry_id);
	if(!count($entry))
		return FALSE;
	
	$description = template_ical($entry);
	
	// ## Making iCal-element
	$c = new vcalendar ();
	$e = new vevent();
	$e->setProperty( 'dtstart',
		date('Y', $entry['time_start']),
		date('m', $entry['time_start']),
		date('d', $entry['time_start']),
		date('H', $entry['time_start']),
		date('i', $entry['time_start']),
		date('s', $entry['time_start']));
	$e->setProperty( 'dtend',
		date('Y', $entry['time_end']),
		date('m', $entry['time_end']),
		date('d', $entry['time_end']),
		date('H', $entry['time_end']),
		date('i', $entry['time_end']),
		date('s', $entry['time_end']));
	$e->setProperty( 'summary', $entry['entry_name'] );
	$e->setProperty( 'class', 'PUBLIC' );
	$e->setProperty( 'description', $description);
	$e->setProperty( 'UID', 'entry'.$entry['entry_id'].'@booking.jaermuseet.no');
	$c->setProperty('method', 'REQUEST');
	$c->setComponent( $e );
	$ical = str_replace('DTSTART', 'X-GWITEM-TYPE:APPOINTMENT'.chr(10).'DTSTART', $c->createCalendar());
	
	// ## Sending mail
	// Need to find who we are sending it to
	$users = array();
	foreach($entry['user_assigned'] as $user_id)
	{
		$user = getUser($user_id);
		if(count($user))
			$users[$user_id] = $user['user_email'];
	}
	
	// Sending to the users
	foreach ($users as $user_mail)
	{
		
		$rand = md5(time());
		$mime_boundary = "==Multipart_Boundary_x{$rand}x";
		$subject = 'Booking - '.date('d-m-Y', $entry['time_start']).': '.$entry['entry_name'];
		if(isset($login['user_email']) && $login['user_email'] != '')
			$headers = 'From: '.$login['user_email'];
		else
			$headers = 'From: '.constant('EMAIL_FROM');
		
		// Add the headers for a file attachment
		$headers .= "\nMIME-Version: 1.0\n" .
			"Content-Type: multipart/mixed;\n" .
			" boundary=\"{$mime_boundary}\"";
		
		// Add a multipart boundary above the plain message
		$message = "This is a multi-part message in MIME format.\n\n" .
			"--{$mime_boundary}\n" .
			"Content-Type: text/plain; charset=\"iso-8859-1\"\n" .
			"Content-Transfer-Encoding: 7bit\n\n" .
		  	"$description\n\n";
		
		// iCal
		$message .= "--{$mime_boundary}\n".
			"Content-class: urn:content-classes:calendarmessage\n".
			"Content-type: text/calendar; method=REQUEST; name=meeting.ics; charset=\"iso-8859-1\"\n".
			"Content-Transfer-Encoding: 7bit\n\n";
		$message .= $ical;
		$message .= "\n--{$mime_boundary}--";
		
		$ok = mail ($user_mail, $subject, $message, $headers);
		$ok = true;
		if(!$ok)
		{
			// TODO: Error handling for mail not sent
			echo 'Faild sending mail. Please contact somebody!!';
			exit();
		}
		else
		{
			// It's okey...
		}
	}
	
	// ## Loging iCal sending
	// TODO: Log iCal sending
	return TRUE;
}

function template_ical ($entry)
{
	// $entry = getEntry($entry_id) from sending_ical
	if(!count($entry))
		return '';
	
	$return = '';
	$return .= _('Entry name').': '.$entry['entry_name'].chr(10);
	$return .= _('Entry ID').': '.$entry['entry_id'].chr(10);
	$return .= _('Entry type').': ';
	if($entry['entry_type_id'] == '0')
		$return .= _('Non');
	else
	{
		$entry_type = getEntryType($entry['entry_type_id']);
		if(count($entry_type))
			$return .= $entry_type['entry_type_name'];
	}
	$return .= chr(10);
	$return .= _('Starts').': '.date('H:i d-m-Y', $entry['time_start']).chr(10);
	$return .= _('Finished').': '.date('H:i d-m-Y', $entry['time_end']).chr(10);
	if(count($entry['user_assigned']) > 1) $return .= chr(10);
	$return .= _('Assigned to').': ';
	if(!count($entry['user_assigned']))
		$return .= _('Nobody').chr(10);
	elseif(count($entry['user_assigned']) == '1')
	{
		foreach ($entry['user_assigned'] as $user_id)
		{
			if($user_id == '0')
				$return .= _('Nobody').chr(10);
			else
			{
				$user = getUser($user_id);
				if(count($user))
					$return .= $user['user_name'].chr(10);
			}
		}
	}
	else
	{
		foreach ($entry['user_assigned'] as $user_id)
		{
			$user = getUser($user_id);
			if(count($user))
				$return .= ' - '.$user['user_name'].chr(10);
		}
	}
	
	$area = getArea($entry['area_id']);
	$return .= _('Area').': '.$area['area_name'].chr(10);
	$return .= _('Room').': ';
	if(!count($entry['room_id']))
		$return .= _('Whole area').chr(10);
	elseif(count($entry['room_id']) == '1')
	{
		// Single room
		foreach ($entry['room_id'] as $rid)
		{
			if ($rid == '0')
				$return .= _('Whole area').chr(10);
			else
			{
				$room = getRoom($rid);
				if(count($room))
					$return .= $room['room_name'].chr(10);
				else
					$return .= _('Can\'t find room').chr(10);
			}
		}
	}
	else
	{
		$rooms = false;
		foreach ($entry['room_id'] as $rid)
		{
			if($rid != '0')
			{
				$rooms = true;
				$room = getRoom($rid);
				if(count($room))
					$return .= ' - '.$room['room_name'].chr(10);
			}
		}
		if(!$rooms)
			$return .= _('Whole area').chr(10);
	}

	if($entry['customer_id'] == '0')
		$return .= _('Customer').': '._('Non selected').chr(10);
	else
		$return .= _('Customer').': '.$entry['customer_name'].' ('._('Customer ID').' '.$entry['customer_id'].')'.chr(10);
	$return .= _('Customer').': '.$entry['customer_name'].chr(10);
	$return .= _('Contact person').': '.$entry['contact_person_name'].chr(10);
	$return .= _('Contact telephone').': '.$entry['contact_person_phone'].chr(10);
	$return .= _('Contact persons email').': '.$entry['contact_person_email'].chr(10);
	$return .= _('Municipal').': '.$entry['customer_municipal'].chr(10);
	
	$user_created = getUser($entry['created_by']);
	if(count($user_created))
		$return .= _('Booking created by').': '.$user_created['user_name'].chr(10);
	
	$return .= _('Comment').':'.chr(10);
	$return .= nl2br($entry['comment']);
	
	return $return;
}

function emailSend ($user_id, $subject, $message)
{
	global $login;
	
	$user = getUser ($user_id);
	if(count($user))
	{
		if(isset($login['user_email']) && $login['user_email'] != '')
			$headers = 'From: '.$login['user_email'];
		else
			$headers = 'From: '.constant('EMAIL_FROM');
		
		$headers .= "\r\n";
		$headers .= 'Content-type: text/plain; charset=iso-8859-1' . "\r\n";
		mail ($user['user_email'], $subject, $message, $headers);
	}
}

function emailSendAdmin ($subject, $message)
{
	$headers = 'From: '.constant('EMAIL_FROM');
		
	$headers .= "\r\n";
	$headers .= 'Content-type: text/plain; charset=iso-8859-1' . "\r\n";
	mail (constant('EMAIL_FROM'), $subject, $message, $headers);
}

function emailSendEntryChanges ($entry, $rev_num, $user_id)
{
	global $smarty;
	
	if(!isUserDeactivated($user_id))
	{
		$smarty = new Smarty;
		
		templateAssignEntry('smarty', $entry);
		templateAssignSystemvars('smarty');
		templateAssignEntryChanges('smarty', $entry, $rev_num, true);
		$message = $smarty->fetch('file:mail-entry-changes.tpl');
		$subject = "[booking]".$entry['entry_id'].' '.
		date('d.m.Y', $entry['time_start']).' Endring';
		
		emailSend($user_id, $subject, $message);
	}
}

function emailSendEntryNew ($entry, $user_id)
{
	global $smarty;
	
	if(!isUserDeactivated($user_id))
	{
		$smarty = new Smarty;
		
		templateAssignEntry('smarty', $entry);
		templateAssignSystemvars('smarty');
		$message = $smarty->fetch('file:mail-entry-new.tpl');
		$subject = "[booking]".$entry['entry_id'].' '.
		date('d.m.Y', $entry['time_start']).' Ny booking';
		
		emailSend($user_id, $subject, $message);
	}
}

function emailSendEntryNewUser ($entry, $rev_num, $user_id)
{
	global $smarty;
	
	if(!isUserDeactivated($user_id))
	{
		$smarty = new Smarty;
		
		templateAssignEntry('smarty', $entry);
		templateAssignSystemvars('smarty');
		templateAssignEntryChanges('smarty', $entry, $rev_num);
		$message = $smarty->fetch('file:mail-entry-newuser.tpl');
		$subject = "[booking]".$entry['entry_id'].' '.
		date('d.m.Y', $entry['time_start']).' Ny som vert';
		
		emailSend($user_id, $subject, $message);
	}
}

function emailSendEntryUserDeleted ($entry, $rev_num, $user_id)
{
	global $smarty;
	
	if(!isUserDeactivated($user_id))
	{
		$smarty = new Smarty;
		
		templateAssignEntry('smarty', $entry);
		templateAssignSystemvars('smarty');
		templateAssignEntryChanges('smarty', $entry, $rev_num);
		$message = $smarty->fetch('file:mail-entry-userdeleted.tpl');
		$subject = "[booking]".$entry['entry_id'].' '.
		date('d.m.Y', $entry['time_start']).' Ikke lenger vert';
		
		emailSend($user_id, $subject, $message);
	}
}

function emailSendConfirmation ($entry, $to, $message)
{
	global $login;
	
	if(isset($login['user_email']) && $login['user_email'] != '')
			$headers = 'From: '.$login['user_email'];
	else
		$headers = 'From: '.constant('EMAIL_FROM');
	
	$area = getArea($entry['area_id']);
	if(count($area))
		$area_name = ', '.$area['area_name'];
	else
		$area_name = '';
	
	$subject = 'Bekreftelse på booking - '.date('d.m.Y', $entry['time_start']).$area_name;
	
	
	// TODO: Add HTML
	$headers .= "\r\n";
	$headers .= 'Content-type: text/plain; charset=iso-8859-1' . "\r\n";
	return mail ($to, '=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers);
}

function emailSendConfirmationPDF ($entry, $to, $confirm_pdffile, $attachments, $message_plain)
{
	global $login, $entry_confirm_pdf_path, $entry_confirm_att_path;
	
	require_once 'libs/Mail/Mail.php';
	require_once 'libs/Mail/mime.php';
	
	
	// ## Get the data ##
	
	// From
	if(isset($login['user_email']) && $login['user_email'] != '')
			$from = $login['user_email'];
	else
		$from = constant('EMAIL_FROM');
	
	// Area in subject
	$area = getArea($entry['area_id']);
	if(count($area))
		$area_name = ', '.$area['area_name'];
	else
		$area_name = '';
	
	// Subject
	$subject = 'Bekreftelse på booking - '.date('d.m.Y', $entry['time_start']).$area_name;
	
	
	$crlf = "\n";
	$hdrs = array(
				'From'    => $from,
				'Subject' => '=?UTF-8?B?'.base64_encode($subject).'?='
				);
	$mime = new Mail_mime($crlf);
	
	// Plain
	//$mime->setTextEncoding
	$mime->setTXTBody($message_plain);
	
	// HTML
	//$mime->setHTMLBody($html);
	
	// PDF
	if($confirm_pdffile != '')
		$mime->addAttachment($entry_confirm_pdf_path.'/'.$confirm_pdffile, 'application/pdf');
	
    // Attachments
	foreach($attachments as $att)
	{
		$mime->addAttachment($entry_confirm_att_path.'/'.$att['att_filename'], $att['att_filetype'], $att['att_filename_orig']);
	}
	//do not ever try to call these lines in reverse order
	$body = $mime->get();
	$hdrs = $mime->headers($hdrs);
    
	$mail =& Mail::factory('mail');
	$mail->send($to, $hdrs, $body);
	
	return true;
	//return mail ($to, $subject, "", $headers);
}
?>