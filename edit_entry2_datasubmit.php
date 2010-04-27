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


require "libs/municipals_norway.php";

$area_rooms = array();

foreach ($entry_fields as $field)
{
	switch ($field['var'])
	{
		case 'entry_id':
			// Already checked
			break;
		
		case 'entry_type_id':
			if(!isset($_POST[$field['var']]) || !is_numeric($_POST[$field['var']]))
			{
				$form_errors[] = _('Entrytype is invalid.');
				$entry_type_id = 0;
			}
			else
			{
				$entry_type_id = (int)$_POST[$field['var']];
				if($entry_type_id == '0')
					$entry_type_id = 0; // Zero is allowed (non selected)
				else
				{
					// Checking against DB
					$Q = mysql_query("select entry_type_id from `entry_type` where entry_type_id = '$entry_type_id'");
					if(!mysql_num_rows($Q))
						$form_errors[] = _('Can\'t find entrytype in database');
				}
			}
			addValue($field['var'], $$field['var']);
			break;
		
		case 'time_start':
		case 'time_end':
			if(isset($_POST[$field['var']]))
			{
				$$field['var'] = getDateFromPost($_POST[$field['var']]);
				if($invalid_date)
				{
					$form_errors[] = _('Invalid date. Please check the format.');
				}
			}
			addValue($field['var'], $$field['var']);
			break;
		
		case 'area_id':
			if(!isset($_POST[$field['var']]) || !is_numeric($_POST[$field['var']]))
			{
				$form_errors[] = _('Area is invalid.');
				$area_id = 0;
			}
			else
			{
				// Checking against DB
				$area_id = (int)$_POST[$field['var']];
				
				if($area_id == '0')
				{
					$form_errors[] = _('Area must be selected.');
				}
				else
				{
					$area_rooms[$area_id] = true;
					$Q = mysql_query("select id as area_id from `mrbs_area` where id = '$area_id'");
					if(!mysql_num_rows($Q))
						$form_errors[] = _('Can\'t find area in database');
				}
			}
			addValue($field['var'], $$field['var']);
			break;
		
		case 'room_id':
			$room_id = array();
			if(!isset($_POST[$field['var']]))
			{
				// No errors, no room is allowed
				$room_id[0] = 0;
			}
			else
			{
				$room_id_array = $_POST[$field['var']];
				
				// Checking whole array
				foreach ($room_id_array as $rid)
				{
					if(!is_numeric($rid))
						$form_errors[] = _('One of the rooms is not found.');
					elseif($rid != '0') // Ignore zero
					{
						$rid = (int)$rid;
						$thisroom = getRoom($rid);
						if(!count($thisroom))
							$form_errors[] = _('One of the rooms is not found.')." ($rid)";
						else
						{
							$room_id[$rid] = $rid;
							$area_rooms[$thisroom['area_id']] = true;
						}
					}
				}
			}
			if(!count($room_id))
				$room_id = array(0 => 0);
			
			addValueArray($field['var'], $$field['var']);
			
			break;
		
		case 'user_assigned':
			$user_assigned = array();
			if(!isset($_POST[$field['var']]))
			{
				// No errors, no room is allowed
				$user_assigned[0] = 0;
			}
			else
			{
				$user_assigned_array = $_POST[$field['var']];
				
				// Checking whole array
				foreach ($user_assigned_array as $uid)
				{
					if(!is_numeric($uid))
						$form_errors[] = _('One of the users you assigned is not found.');
					else
					{
						$uid = (int)$uid;
						$Q = mysql_query("select user_id from `users` where user_id = '$uid'");
						if(!mysql_num_rows($Q))
							$form_errors[] = _('One of user you assigned is not found.')." ($uid)";
						else
							$user_assigned[$uid] = $uid;
					}
				}
			}
			
			addValueArray($field['var'], $$field['var']);
			break;
		
		case 'customer_id':
			if(!isset($_POST[$field['var']]))
				$$field['var'] = 0;
			else
				$$field['var'] = (int)$_POST[$field['var']];
			
			if($$field['var'] != 0)
			{
				$customer = getCustomer($$field['var']);
				if(!count($customer))
				{
					$form_errors[] = _("Can't find the customer you tried to select").' (id '.$$field['var'].')';
					$$field['var'] = '';
					$customer_name_ok = True; // Don't trigger the customer_name error
				}
				else
				{
					$customer_name = $customer['customer_name'];
					$customer_name_ok = True; // Don't trigger the customer_name error
				}
			}
			else
			{
				// Maybe just the customer_name is inserted?
				if(isset($_POST['customer_name']) && $_POST['customer_name'] != '')
				{
					// Trigger error...
					$customer_name_ok = False;
				}
				else
					$customer_name_ok = True; // Don't trigger the customer_name error
				$customer_name = '';
				$customer_id = 0;
			}
			addValue($field['var'], $$field['var']);
			addValue('customer_name', $customer_name);
			break;
		
		case 'customer_name': // Ignore...
			break;
		
		case 'customer_municipal':
		case 'invoice_address':
			if(!isset($$field['var']))
				$$field['var'] = ''; // Ignore if not set, municipal is recived from municipals array
				// Invoice_address is never to be set
			break;
		
		case 'customer_municipal_num':
			if (is_numeric($_POST[$field['var']]) && isset($municipals[$_POST[$field['var']]]))
			{
				$customer_municipal_num	= $_POST[$field['var']];
				$customer_municipal		= $municipals[$_POST[$field['var']]];
			}
			else
			{
				$customer_municipal_num	= '';
				$customer_municipal		= '';
			}
			
			addValue($field['var'], $$field['var']);
			addValue('customer_municipal', $customer_municipal);
			break;
		
		case 'program_id':
			if(!isset($_POST[$field['var']]) || !is_numeric($_POST[$field['var']]))
			{
				$form_errors[] = _('Fixed program is invalid.');
				$$field['var'] = 0;
			}
			elseif($_POST[$field['var']] == '0')
				$$field['var'] = 0;
			else
			{
				// Checking against DB
				$$field['var'] = (int)$_POST[$field['var']];
				
				$Q = mysql_query("select program_id,program_name from `programs` where program_id = '$program_id'");
				if(!mysql_num_rows($Q))
					$form_errors[] = _('Can\'t find the fixed program in database');
				else
					$program_name = mysql_result($Q,0,'program_name');
			}
			addValue($field['var'], $$field['var']);
			break;
		
		case 'invoice_address_id':
			if(!isset($_POST[$field['var']]) || 
				$_POST[$field['var']] == '0' || 
				$_POST[$field['var']] == '' ||
				$_POST[$field['var']] < 0)
			{
				$$field['var'] = 0;
			}
			elseif(!is_numeric($_POST[$field['var']]))
			{
				$form_errors[] = _('Invoice address is invalid.');
				$$field['var'] = 0;
			}
			else
			{
				// Checking against DB
				$$field['var'] = (int)$_POST[$field['var']];
				
				$Q = mysql_query("select address_id from `customer_address` where address_id = '$invoice_address_id'");
				if(!mysql_num_rows($Q))
					$form_errors[] = _('Can\'t find the invoice address in database');
			}
			addValue($field['var'], $$field['var']);
			break;
		
		case 'invoice':
		case 'service_alco':
		case 'num_person_count':
		case 'invoice_electronic':
			// True or false
			if(isset($_POST[$field['var']]) && $_POST[$field['var']] == '1')
			{
				$$field['var'] = TRUE;
				addValue($field['var'], '1');
			}
			else
			{
				$$field['var'] = FALSE;
				addValue($field['var'], '0');
			}
			
			if($field['var'] == 'invoice' && $$field['var'])
				$invoice_status = '1';
			elseif($field['var'] == 'invoice')
				$invoice_status = '0';
			
			break;
		
		case 'invoice_comment':
		case 'comment':
		case 'infoscreen_txt':
		case 'entry_title':
		case 'user_assigned2':
		case 'contact_person_name':
		case 'contact_person_phone':
		case 'contact_person_email':
		case 'program_description':
		case 'service_description':
		case 'invoice_ref_your':
		case 'invoice_internal_comment':
		case 'invoice_email':
			// Text data is input. (can contain a lot of shit)
			if(!isset($_POST[$field['var']]))
				$$field['var'] = '';
			else
				$$field['var'] = slashes(htmlspecialchars($_POST[$field['var']],ENT_QUOTES));
			
			addValue($field['var'], $$field['var']);
			break;
		
		case 'num_person_child':
		case 'num_person_adult':
			if(!isset($_POST[$field['var']]))
			{
				$$field['var'] = '';
			}
			elseif($_POST[$field['var']] != '' && !is_numeric($_POST[$field['var']]))
			{
				$$field['var'] = '';
				if($field['var'] == 'num_person_child')
					$form_errors[] = _('Number of children must be a number, if anything.');
				elseif($field['var'] == 'num_person_adult')
					$form_errors[] = _('Number of adults must be a number, if anything.');
			}
			else
				$$field['var'] = $_POST[$field['var']];
			
			addValue($field['var'], $$field['var']);
			break;
		
		case 'invoice_content':
			// Getting invoice_content
			
			$$field['var'] = array();
			$i = 0;
			if(isset($_POST['rows']) && is_array($_POST['rows']))
			{
				foreach ($_POST['rows'] as $id)
				{
					$i++;
					$thisone = array();
					$thisone['type']		= 'belop';
					$thisone['belop_hver']	= 0;
					$thisone['antall']		= 1;
					$thisone['mva']			= 0;
					$thisone['name']		= '';
					//$thisone['id_type']		= 0;
					//$thisone['id_ekstra']	= 0;
					$thisone['mva_eks']		= true;
					
					if(isset($_POST['type'.$id]) && is_numeric($_POST['type'.$id]))
						$thisone['type']		= $_POST['type'.$id];
					if(isset($_POST['belop_hver_real'.$id]) && is_numeric($_POST['belop_hver_real'.$id]))
						$thisone['belop_hver']	= $_POST['belop_hver_real'.$id];
					if(isset($_POST['antall'.$id]) && is_numeric($_POST['antall'.$id]))
						$thisone['antall']		= $_POST['antall'.$id];
					if(isset($_POST['mva'.$id]) && is_numeric($_POST['mva'.$id]))
						$thisone['mva']			= $_POST['mva'.$id];
					if(isset($_POST['name'.$id]))
						$thisone['name']		= $_POST['name'.$id];
					
					$thisone['mva'] = ((float)$thisone['mva'])/100;
					
					${$field['var']}[$i] = $thisone;
				}
			}
			$$field['var'] = invoiceContentNumbers($$field['var']);
			addValueArray($field['var'], $$field['var']);
			break;
		
		case 'empty':
		case 'submit1':
		case 'submit2':
		case 'submit3':
			// Ignore
			break;
		
		default:
			echo 'System error! New input field is detected, <b>'.$field['var'].'</b>. This must be made in <b>'.__FILE__.'</b>. System have halted.';
			//echo '<br><br>';
			//print_r($_POST);
			exit();
	}
}

/* Checking the times */
if(isset($time_start) && isset($time_end))
{
	if ($time_start > $time_end)
	{
		$form_errors[] = _('Start time must be before end time for the entry.');
	}
}
else
	$form_errors[] = _('Start and end time must be set for all entries.');


if(!count($form_errors))
{
	$entry_name = genEntryName();
	
	/* ## WARNINGSYSTEM ## */
	if(isset($_POST['warningignore']) && $_POST['warningignore'] == '1')
		$ignoreWarning = TRUE;
	else
		$ignoreWarning = FALSE;
	
	if (!$ignoreWarning)
	{
		/* ## Checking for warnings ## */
		
		// Checking invoice
		//if($invoice && $invoice_info == '')
		//{
		//	$warnings[] = _('You have selected that you want an invoice to be sent, but you havn\'t specified what it should contain.');
		//}
		
		// Checking title
		if(!$entry_name_set)
		{
			$warnings[] = _('No title for the entry was made. Please spesify either a title, a customer or a bookingtype.');
		}
		
		// Trigger customer_name but not customer_id error?
		if(!isset($customer_name_ok) || !$customer_name_ok)
		{
			$warnings[] = _('Please be advised: You have set a customer name but no customer in the database where selected. The result of this is that no customer is selected.');
		}
		
		// Checking room
		$checkroom = checkTime_Room ($time_start, $time_end, $area_id, $room_id); // $array[roomid][entryid] = entryid;
		if(count($checkroom))
		{
			// Removing this event if we are editing
			if(!$entry_add)
			{
				$checkroom2	= $checkroom;
				$checkroom	= array();
				foreach ($checkroom2 as $rid => $entries)
				{
					foreach ($entries as $thisentry)
					{
						if($thisentry != $entry_id)
							$checkroom[$rid][$thisentry] = $thisentry;
					}
				}
			}
			foreach ($checkroom as $rid => $entries)
			{
				if($rid == 0)
				{
					$warning_tmp = '<b>Hele bygningen</b> er booket for: ';
				}
				else
				{
					$thisroom = getRoom ($rid);
					if(!count($thisroom))
						$warning_tmp = _('One of the rooms you have selected is already booked for').': ';
					else
						$warning_tmp = '<b>'.$thisroom['room_name'].'</b>'._(' is already booked at the time you have selected for').': ';
				}
				
				$i = 0;
				$warning_tmp .= '<ul>';
				foreach ($entries as $entryid)
				{
					$i++;
					$entrytmp = getEntry($entryid);
					if(count($entrytmp))
					{
						$warning_tmp .= '<li>'. iconHTML('page_white').' <i>'.$entrytmp['entry_name'].'</i> ('.
							'<a href="entry.php?entry_id='.$entrytmp['entry_id'].'">Vis booking</a>)'.
							'</li>';
						//if($i != count($entries))
						//	$warning_tmp .= ' '._('and for').' ';
					}
				}
				$warning_tmp .= '</ul>';
				$warnings[] = $warning_tmp;
			}
		}
		
		// Checking user
		$checkuser = checkTime_User ($time_start, $time_end, $user_assigned); // $array[userid][entryid] = entryid;
		if(count($checkuser))
		{
			// Removing this event if we are editing
			if(!$entry_add)
			{
				$checkuser2	= $checkuser;
				$checkuser	= array();
				foreach ($checkuser2 as $uid => $entries)
				{
					foreach ($entries as $thisentry)
					{
						if($thisentry != $entry_id)
							$checkroom[$uid][$thisentry] = $thisentry;
					}
				}
			}
			
			foreach ($checkuser as $uid => $entries)
			{
				$thisuser = getUser($uid);
				if(!count($thisuser))
					$warnings[] = _('One of the users you have selected is already booked.');
				else
					$warnings[] = '<b>'.$thisuser['user_name'].'</b>'._(' is already booked at the time you have selected.');
			}
		}
		
		// Checking for room i wrong building
		if(count($area_rooms) > 1)
			$warnings[] = _('You have selected rooms from more than one area.');
		
		
		if($entry_add || $entry['time_start'] != $time_start || $entry['time_end'] != $time_end)
		{
			// Checking for starttime in the past and too long into the future
			if($time_start < time())
				$warnings[] = _('You have selected a starttime in the past.');
			if($time_end > (time() + 364*24*60*60))
				$warnings[] = _('Are you sure you want to let the entry end over a year into the future?');
			if(($time_end - $time_start) > (7*24*60*60))
				$warnings[] = _('The entry goes over more than 7 days. I think this might be wrong.');
		}
	}
	
	if(!count($warnings))
	{
		/* ## DATA IS OK -> SENDING TO DATABASE ## */
		
		// Some preping of the data
		$time_day	= date('d', $time_start);
		$time_month	= date('m', $time_start);
		$time_year	= date('Y', $time_start);
		$time_hour	= date('H', $time_start);
		$time_min	= date('i', $time_start);
		
		// Building query
		if ($entry_add)
		{
			// Adding -> Insert-query
			$rev_num = '1';
			$SQL = "INSERT INTO `entry` (
					`entry_id` ,
					`entry_name` ,
					`entry_title` ,
					`confirm_email` ,
					`entry_type_id` ,
					`num_person_child` ,
					`num_person_adult` ,
					`num_person_count` ,
					`program_id` ,
					`program_description` ,
					`service_alco` ,
					`service_description` ,
					`comment` ,
					`infoscreen_txt` ,
					`rev_num` ,
					`time_start` ,
					`time_end` ,
					`time_day` ,
					`time_month` ,
					`time_year` ,
					`time_hour` ,
					`time_min` ,
					`time_created` ,
					`time_last_edit` ,
					`room_id` ,
					`area_id` ,
					`created_by` ,
					`edit_by` ,
					`user_assigned` ,
					`user_assigned2` ,
					`user_last_edit` ,
					`customer_id` ,
					`customer_name` ,
					`customer_municipal_num` ,
					`customer_municipal` ,
					`contact_person_name` ,
					`contact_person_phone` ,
					`contact_person_email` ,
					`invoice` ,
					`invoice_comment` ,
					`invoice_status` , 
					`invoice_locked` ,
					`invoice_electronic` ,
					`invoice_email` ,
					`invoice_internal_comment` ,
					`invoice_ref_your` ,
					`invoice_address_id` ,
					`invoice_content`
				)
				VALUES (
					NULL,
					'$entry_name',
					'$entry_title',
					'0',
					'$entry_type_id',
					'$num_person_child',
					'$num_person_adult',
					'$num_person_count',
					'$program_id',
					'$program_description',
					'$service_alco',
					'$service_description',
					'$comment',
					'$infoscreen_txt',
					'$rev_num',
					'$time_start',
					'$time_end',
					'$time_day',
					'$time_month',
					'$time_year',
					'$time_hour',
					'$time_min',
					'".time()."',
					'".time()."',
					'".splittalize($room_id)."',
					'$area_id',
					'".$login['user_id']."',
					'".splittalize(array($login['user_id'] => $login['user_id']))."',
					'".splittalize($user_assigned)."',
					'$user_assigned2',
					'".$login['user_id']."',
					'$customer_id',
					'$customer_name',
					'$customer_municipal_num',
					'$customer_municipal',
					'$contact_person_name',
					'$contact_person_phone',
					'$contact_person_email',
					'$invoice',
					'$invoice_comment',
					'$invoice_status',
					'0',
					'$invoice_electronic',
					'$invoice_email',
					'$invoice_internal_comment',
					'$invoice_ref_your',
					'$invoice_address_id',
					'".serialize($invoice_content)."'
				);";
			
			mysql_query($SQL);
			$entry_id = mysql_insert_id();
			
			// # Log
			$log_data = array();
			$log_data['entry_name']				= $entry_name;
			$log_data['entry_type_id']			= $entry_type_id;
			$log_data['num_person_child']		= $num_person_child;
			$log_data['num_person_adult']		= $num_person_adult;
			$log_data['num_person_count']		= $num_person_count;
			$log_data['program_id']				= $program_id;
			$log_data['program_description']	= $program_description;
			$log_data['service_alco']			= $service_alco;
			$log_data['service_description']	= $service_description;
			$log_data['comment']				= $comment;
			$log_data['infoscreen_txt']			= $infoscreen_txt;
			$log_data['rev_num']				= $rev_num;
			$log_data['time_start']				= $time_start;
			$log_data['time_end']				= $time_end;
			$log_data['room_id']				= splittalize($room_id);
			$log_data['area_id']				= $area_id;
			$log_data['user_assigned']			= splittalize($user_assigned);
			$log_data['user_assigned2']			= $user_assigned2;
			$log_data['customer_id']			= $customer_id;
			$log_data['customer_municipal_num']	= $customer_municipal_num;
			$log_data['contact_person_name']	= $contact_person_name;
			$log_data['contact_person_phone']	= $contact_person_phone;
			$log_data['contact_person_email']	= $contact_person_email;
			$log_data['invoice']				= $invoice;
			$log_data['invoice_status']			= $invoice_status;
			$log_data['invoice_comment']		= $invoice_comment;
			$log_data['invoice_electronic']		= $invoice_electronic;
			$log_data['invoice_email']			= $invoice_email;
			$log_data['invoice_internal_comment']		= $invoice_internal_comment;
			$log_data['invoice_ref_your']		= $invoice_ref_your;
			$log_data['invoice_address_id']		= $invoice_address_id;
			$log_data['invoice_content']		= $invoice_content;
			
			if(!newEntryLog($entry_id, 'add', '', $rev_num, $log_data))
			{
				echo _('Can\'t log the new entry.');
				exit();
			}
			
			// Sending iCal
			// TODO: Jobb med ical og gw
			//send_iCal($entry_id);
			//not any more...
			
			// Sending email to other users that are assigned
			$the_entry = getEntry($entry_id);
			if(count($the_entry))
			{ 
				foreach ($user_assigned as $user_id)
				{
					if($user_id != $login['user_id'])
						emailSendEntryNew($the_entry, $user_id);
				}
			}
		}
		else
		{
			// # Editing -> Find changes, than update-query
			
			// # Finding changes
			// $entry contains all original values
			$changed = array();
			if($entry['entry_name'] != $entry_name)
				$changed[] = 'entry_name';
			if($entry['entry_title'] != $entry_title)
				$changed[] = 'entry_title';
			if($entry['entry_type_id'] != $entry_type_id)
				$changed[] = 'entry_type_id';
			if($entry['num_person_child'] != $num_person_child)
				$changed[] = 'num_person_child';
			if($entry['num_person_adult'] != $num_person_adult)
				$changed[] = 'num_person_adult';
			if($entry['num_person_count'] != $num_person_count)
				$changed[] = 'num_person_count';
			if($entry['program_id'] != $program_id)
				$changed[] = 'program_id';
			if($entry['program_description'] != $program_description)
				$changed[] = 'program_description';
			if($entry['service_alco'] != $service_alco)
				$changed[] = 'service_alco';
			if($entry['service_description'] != $service_description)
				$changed[] = 'service_description';
			if($entry['comment'] != $comment)
				$changed[] = 'comment';
			if($entry['infoscreen_txt'] != $infoscreen_txt)
				$changed[] = 'infoscreen_txt';
			if($entry['time_start'] != $time_start)
				$changed[] = 'time_start';
			if($entry['time_end'] != $time_end)
				$changed[] = 'time_end';
			if($entry['room_id'] != $room_id)
				$changed[] = 'room_id';
			if($entry['area_id'] != $area_id)
				$changed[] = 'area_id';
			if($entry['user_assigned'] != $user_assigned)
				$changed[] = 'user_assigned';
			if($entry['user_assigned2'] != $user_assigned2)
				$changed[] = 'user_assigned2';
			if($entry['customer_id'] != $customer_id)
				$changed[] = 'customer_id';
			//if($entry['customer_name'] != $customer_name)
			//	$changed[] = 'customer_name';
			if($entry['customer_municipal_num'] != $customer_municipal_num)
				$changed[] = 'customer_municipal_num';
			if($entry['customer_municipal'] != $customer_municipal)
				$changed[] = 'customer_municipal';
			if($entry['contact_person_name'] != $contact_person_name)
				$changed[] = 'contact_person_name';
			if($entry['contact_person_phone'] != $contact_person_phone)
				$changed[] = 'contact_person_phone';
			if($entry['contact_person_email'] != $contact_person_email)
				$changed[] = 'contact_person_email';
			
			if($entry['invoice_status'] != 2 && $entry['invoice_status'] != 3 && $entry['invoice_status'] != 4)
			{
				// Invoice
				if($entry['invoice'] != $invoice)
					$changed[] = 'invoice';
				if($entry['invoice_status'] != $invoice_status)
					$changed[] = 'invoice_status';
				if($entry['invoice_comment'] != $invoice_comment)
					$changed[] = 'invoice_comment';
				if($entry['invoice_internal_comment'] != $invoice_internal_comment)
					$changed[] = 'invoice_internal_comment';
				if($entry['invoice_electronic'] != $invoice_electronic)
					$changed[] = 'invoice_electronic';
				if($entry['invoice_ref_your'] != $invoice_ref_your)
					$changed[] = 'invoice_ref_your';
				if($entry['invoice_email'] != $invoice_email)
					$changed[] = 'invoice_email';
				if($entry['invoice_address_id'] != $invoice_address_id)
					$changed[] = 'invoice_address_id';
				if(serialize($entry['invoice_content']) != serialize($invoice_content))
					$changed[] = 'invoice_content';
			}
			
			// # Making SQL query
			if (count($changed))
			{
				// Saving $changed for the log
				$changed2 = $changed;
				
				if(in_array('time_start', $changed))
				{
					$changed[] = 'time_day';
					$changed[] = 'time_month';
					$changed[] = 'time_year';
					$changed[] = 'time_hour';
					$changed[] = 'time_min';
				}
				
				if(in_array('customer_id', $changed))
					$changed[] = 'customer_name';
				if(in_array('customer_municipal_num', $changed))
					$changed[] = 'customer_municipal';
				
				// Adding the one who changed something...
				$changed[]	= 'time_last_edit';	$time_last_edit = time();
				$edit_by	= $entry['edit_by'];
				$edit_by[]	= $login['user_id'];
				$changed[]	= 'edit_by';			$edit_by = splittalize(splittIDs(splittalize($edit_by))); // the extra functions = remove duplicates
				$rev_num	= $entry['rev_num'] + 1;
				$changed[]	= 'rev_num';
				$user_last_edit	= $login['user_id'];
				$changed[]	= 'user_last_edit';
				
				$i = 0;
				$SQL = "UPDATE `entry` SET ";
				foreach ($changed as $change)
				{
					$i++;
					
					if($change == 'user_assigned' || $change == 'room_id')
						$SQL .= "`$change` = '".splittalize($$change)."'";
					elseif($change == 'invoice_content')
						$SQL .= "`$change` = '".addslashes(serialize($$change))."'";
					else
						$SQL .= "`$change` = '".$$change."'";
					
					if(count($changed) != $i)
						$SQL .= ', '.chr(10);
				}
				$SQL .= " WHERE `entry`.`entry_id` =$entry_id LIMIT 1 ;";
				
				mysql_query($SQL);
				
				// # Log
				
				// Generating $log_data
				$only_invoice_changed = true;
				$log_data = array();
				foreach ($changed2 as $change)
				{
					if($change == 'user_assigned' || $change == 'room_id')
						$log_data[$change] = splittalize($$change);
					//elseif($change == 'invoice_content')
					//	$log_data[$change] = addslashes(serialize($$change));
					elseif($change == "customer_municipal")
					{
						// Ignore
					}
					else
						$log_data[$change] = $$change;
					
					// Only invoice changes?
					if(substr($change, 0, 7) != 'invoice')
						$only_invoice_changed = false;
				}
				
				if(!newEntryLog($entry_id, 'edit', '', $rev_num, $log_data))
				{
					echo _('Can\'t log the changes for the entry.');
					exit();
				}
				
				// iCal
				// TODO: Jobb med ical og gw
				//send_iCal($entry_id);
				// no more...
				
				if(!$only_invoice_changed)
				{
					// Sending email to other users that are assigned
					// $user_assigned = the new once
					// $entry['user_assigned'] = the old once
					$user_in_both	= array();
					$user_deleted	= array();
					$user_new		= array();
					foreach ($user_assigned as $user_id)
					{
						if($user_id != $login['user_id'])
						{
							if(in_array($user_id, $entry['user_assigned']))
								$user_in_both[$user_id] = $user_id;
							else
								$user_new[$user_id] = $user_id;
						}
					}
					foreach ($entry['user_assigned'] as $user_id)
					{
						if($user_id != $login['user_id'])
						{
							if(in_array($user_id, $user_assigned))
								$user_in_both[$user_id] = $user_id;
							else
								$user_deleted[$user_id] = $user_id;
						}
					}
					
					$the_entry = getEntry($entry_id);
					if(count($the_entry))
					{
						foreach ($user_in_both as $user_id)
							emailSendEntryChanges ($the_entry, $rev_num, $user_id);
						foreach ($user_new as $user_id)
							emailSendEntryNewUser ($the_entry, $rev_num, $user_id);
						foreach ($user_deleted as $user_id)
							emailSendEntryUserDeleted ($the_entry, $rev_num, $user_id);
					}
				}
			}
			else
			{
				// Nothing is changed, just redirecting
			}
		}
		
		// Redirect...
		switch ($view)
		{
			case 'week':
				header('Location: week.php?year='.date('Y', $time_start).'&month='.date('m', $time_start).'&day='.date('d', $time_start).'&area='.$area_id);
				break;
			
			case 'day':
				header('Location: day.php?year='.date('Y', $time_start).'&month='.date('m', $time_start).'&day='.date('d', $time_start).'&area='.$area_id);
				break;
			
			case 'entry':
			default:
				header('Location: entry.php?entry_id='.$entry_id);
				break;
		}
		exit();
	}
}

/*
// Passing back data
addValue		('area_id',			$area_id);
addValueArray	('room_id',			$room_id);
addValue		('time_start',		$time_start);
addValue		('time_end',		$time_end);
addValueArray	('user_assigned',	$user_assigned);
addValue		('user_assigned2',	$user_assigned2);

addValue		('customer_id',		$customer_id);
addValue		('customer_name',	$customer_name);
addValue		('customer_municipal_num',	$customer_municipal_num);
addValue		('customer_municipal',		$customer_municipal);
addValue		('contact_person_name',		$contact_person_name);
addValue		('contact_person_phone',	$contact_person_phone);
addValue		('contact_person_email',	$contact_person_email);

if($invoice) // Must convert...
	addValue	('invoice',			'1');
else
	addValue	('invoice',			'0');

addValue		('invoice_info',	$invoice_info);
*/

?>