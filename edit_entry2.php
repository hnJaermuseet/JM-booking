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
 * JM-booking - Edit_entry
 * - Add or edit an entry/booking
 */

include_once("glob_inc.inc.php");



// Define fields in an entry and how to use em
$entry_fields = array ();
function addField ($var, $type, $name, $description = '')
{
	global $entry_fields;
	
	$add = TRUE;
	
	if ($var == 'empty')
	{
		$entry_fields[] = array(
			'var'	=> 'empty',
			'add'	=> $add,
			'type'	=> 'empty'
		);
		
	}
	else
	{
		$entry_fields[$var]['var']		= $var;
		$entry_fields[$var]['add']		= $add;
		$entry_fields[$var]['type']		= $type;
		$entry_fields[$var]['name']		= $name;
		$entry_fields[$var]['desc']		= $description;
		$entry_fields[$var]['id']		= ''; // Default = no ID
		
		$entry_fields[$var]['value']		= ''; // The value/whats selected
		$entry_fields[$var]['value_array']	= array(); // The value/whats selected (if type is radio, select, etc)
		$entry_fields[$var]['choice']		= array();
	}
}
addField ('entry_id', 				'hidden',		_('Entry ID')); $entry_fields['entry_id']['add'] = FALSE;
addField ('submit1',				'submit',		_('Save entry'));
addField ('entry_title',			'text',			_('Entry title'),			_('What this is. Can be empty. Entryname is generated from the title and some other values.'));
addField ('entry_type_id',			'select',		_('Entry type'),			_('What type of entry this is.'));
addField ('empty',					'',				'');
addField ('time_start',				'date',			_('Start time'),			_('When does this event start. Should be set in the calendar.'));
	addID ('time_start', 'time_start');
addField ('time_end',				'date',			_('End time'),				_('When is the event finished.'));
	addID ('time_end', 'time_end');
addField ('empty',					'',				'');
addField ('area_id',				'select',		_('Area'));
addField ('room_id',				'checkbox',		_('Room'));
addField ('empty',					'',				'');
addField ('user_assigned',			'checkbox',		_('Users assigned'),		_('Which users are assigned to this event. Event will appear in Groupwise.'));
addField ('user_assigned2',			'text',			_('Manuel user assigned'),	_('If someone other than the normal users are assigned. Use this field to add his or her name.'));
addField ('empty',					'',				'');

addField ('customer_name',		'text',			_('Customer'),				_('Type in parts for the customers name to search.'));
	addID ('customer_name', 'customer_name');
	addAfterField ('customer_name', '&nbsp;'.
	'<input size="1" type="text" name="customer_id2" disabled="disabled" id="customer_id2">'.
	'&nbsp;<input type="button" value="+" onclick="new_customer(); return false;">');
addField ('customer_id',		'hidden',		'');
	addID ('customer_id',	'customer_id');
addField ('customer_municipal',	'text',			_('Municipal'),				_('The municipal which the customer belong to.'));
	addID ('customer_municipal', 'customer_municipal');
	disableField ('customer_municipal');
	addAfterField ('customer_municipal', '&nbsp;'.
	'<input size="1" type="text" name="customer_municipal_num2" disabled="disabled" id="customer_municipal_num2">'.
	'<input type="button" value="'._('Choose').'" onclick="chooseMunicipal(\'customer_municipal_num\', \'customer_municipal\'); return false;">');
addField ('customer_municipal_num',		'hidden',		'');
	addID ('customer_municipal_num',	'customer_municipal_num');
addField ('contact_person_name',	'text',			_('Contact person'),		_('The contact person of the customer.'));
	addID ('contact_person_name', 'contact_person_name');
addField ('contact_person_phone',	'text',			_('Contact telephone'),		_('Telephone number where the contact person can be reached. Preferably a mobile number.'));
addField ('contact_person_email',	'text',			_('Contact email'),			_('The contact persons email address. For more than one, please seperate by a space, comma or semicolon.'));	

addField ('empty',					'',				'');
addField ('num_person_child',		'text',			_('Number of children'));
addField ('num_person_adult',		'text',			_('Number of adults'));
addField ('num_person_count',		'radio',		_('Count these numbers'),	_('If yes the number of children and adults will be counted from the booking system and not the counter.'));
addField ('empty',					'',				'');
addField ('service_description',	'textarea',		_('Service description'),	_('Description of the services we should provide. Food can be one.'));
addField ('service_alco',			'radio',		_('Serve alcohol?'),		_('Will there be served alcohol?'));
//addField ('empty',					'',				'');
addField ('program_id',				'radio',		_('Fixed programs'));
	fieldColspanDesc('program_id');
addField ('program_description',	'textarea',		_('Program description'));
//addField ('empty',					'',				'');
addField ('comment',				'textarea',		_('Comment'));
addField ('infoscreen_txt',			'text',			_('Text on infoscreen'),	_('This text will be on the infoscreens in the reception if the booking is between 16:00 and 06:00. Please check that the playlist on the infosystem is running.'));

addField ('empty',					'',				'');
addField ('submit2',				'submit',		_('Save entry'));
addField ('empty',					'',				'');

addField ('invoice',					'radio',				_('Invoice'),				_('Choose yes if invoice should be made from this entry.'));
addField ('invoice_ref_your',			'text',					_('Your reference'),		_('The customers internal reference.'));
addField ('invoice_comment', 			'textarea',				'Fakturakommentar',				_('Comment that is displayed in the top of the invoice.'));
addField ('invoice_internal_comment',	'textarea',				'Intern fakturakommentar',		_('Internal comment on the invoice (not shown to the customer).'));
addField ('invoice_electronic',			'radio',				_('E-delivery'),			_('Choose yes if the customer want to have the invoice delivery by e-mail.'));
// Address
//addField ('invoice_address_id',			'select',				_('Address'),				_('Choose a address from the customer. This will be the address that the invoice will be sent to.'));
//addID ('invoice_address_id', 'invoice_address_id');
addField ('invoice_address',	'textarea',			_('Address'),				_('Choose a address from the customer. This will be the address that the invoice will be sent to. To change the addresses please press the edit button for the customer.'));
addID ('invoice_address', 'invoice_address');
disableField ('invoice_address');
addAfterField ('invoice_address', '<br>'.
'<input size="1" type="text" name="invoice_address_id2" disabled="disabled" id="invoice_address_id2">'.
'<input type="button" value="Velg/endre adresse" onclick="chooseAddress(\'invoice_address_id\', \'invoice_address\'); return false;">'.
//'<input type="button" value="Ny adresse" onclick="new_address(); return false;">'.
'');
addField ('invoice_address_id',		'hidden',		'');
addID ('invoice_address_id',	'invoice_address_id');

addField ('invoice_email',				'text',					_('E-mail'),				_('E-mail that the customer wants the invoice delivery to.'));
addField ('invoice_content',			'invoice_content',		_('Content'));

addField ('empty',					'',				'');
addField ('submit3',				'submit',		_('Save entry'));

function addOnchange ($var, $value)
{
	global $entry_fields;
	$entry_fields[$var]['onchange']	= $value;
}
function addValue ($var, $value)
{
	global $entry_fields;
	$entry_fields[$var]['value']	= $value;
}
function addValueArray ($var, $value_array)
{
	global $entry_fields;
	$entry_fields[$var]['value_array']	= $value_array;
}
function addChoice ($var, $choice)
{
	global $entry_fields;
	$entry_fields[$var]['choice']	= $choice;
}
function addChoiceBeforeAndAfter ($var, $before, $after)
{
	global $entry_fields;
	$entry_fields[$var]['choice_before']	= $before;
	$entry_fields[$var]['choice_after']		= $after;
}
function addID ($var, $id)
{
	global $entry_fields;
	$entry_fields[$var]['id']				= $id;
}
function addBeforeField ($var, $before)
{
	global $entry_fields;
	$entry_fields[$var]['before']			= $before;
}
function addAfterField ($var, $after)
{
	global $entry_fields;
	$entry_fields[$var]['after']			= $after;
}
function addBeforeChoices ($var, $before)
{
	global $entry_fields;
	$entry_fields[$var]['beforeChoices'] = $before;
}
function disableField ($var)
{
	global $entry_fields;
	$entry_fields[$var]['disabled']			= true;
}
function fieldColspanDesc ($var)
{
	global $entry_fields;
	$entry_fields[$var]['colspanDesc']		= true;
}

// Add og edit
if(isset($_POST['entry_id']) && is_numeric($_POST['entry_id']))
	$entry_id = (int)$_POST['entry_id'];
elseif(isset($_GET['entry_id']) && is_numeric($_GET['entry_id']))
	$entry_id = (int)$_GET['entry_id'];
else
	$entry_id = (int)0;
addValue('entry_id', $entry_id);

/* Onchange */
addOnchange ('area_id', 'choose_area(this.options[this.selectedIndex].value);');

/* Making choices */

// Entry_type_id
$Q_entry_type = mysql_query("select entry_type_id, entry_type_name from `entry_type` order by entry_type_name");
$choices = array('0' => _('Non selected'));
while( $r_choice = mysql_fetch_assoc($Q_entry_type))
	$choices[$r_choice['entry_type_id']] = $r_choice['entry_type_name'];
addChoice ('entry_type_id', $choices);

// Area_id
$Q_area = mysql_query("select id as area_id, area_name, area_group from `mrbs_area` order by area_name");
$choices = array('0' => _('Select one'));
$area2 = array();
$area_group = array();
while( $r_choice = mysql_fetch_assoc($Q_area))
{
	$choices[$r_choice['area_id']]		= $r_choice['area_name'];
	$area2[$r_choice['area_id']]		= $r_choice['area_name'];
	if($r_choice['area_group'] != 0) {
		$group = getGroup($r_choice['area_group']);
		if(count($group)) {
			$area_group[$r_choice['area_id']] = $group['users'];
		}
	}
}
addChoice ('area_id', $choices);

// Room_id
// -> Making some special stuff
$Q_room = mysql_query("select id as room_id, room_name, area_id from `mrbs_room` order by area_id, room_name");
$choices = array('0' => _('Whole area'));
$area_id = 0;		$last_id = 0;
$before = array();	$after = array();
if(mysql_num_rows($Q_room))
{
	while( $r_choice = mysql_fetch_assoc($Q_room))
	{
		if($r_choice['area_id'] != $area_id)
		{
			// Starts new area
			$area_id = $r_choice['area_id'];
			if($last_id != 0)
				$after[$last_id] = '</span>';
			$before[$r_choice['room_id']] = '<span id="area_id'.$area_id.'" style="display: none;">';
		}
		$choices[$r_choice['room_id']] = $r_choice['room_name'];
		$last_id = $r_choice['room_id'];
	}
	$after[$last_id] = '</span>';
}
addChoice ('room_id', $choices);
addChoiceBeforeAndAfter ('room_id', $before, $after);

// User_assigned
$Q_users = mysql_query("select user_id, user_name from `users` order by user_name");
$choices = array();
$before = array();	$after = array();
while( $r_choice = mysql_fetch_assoc($Q_users))
{
	$before[$r_choice['user_id']] = '<span id="user_id'.$r_choice['user_id'].'" style="display: inline;">';
	$choices[$r_choice['user_id']] = $r_choice['user_name'];
	$after[$r_choice['user_id']] = '</span>';
}
addChoice ('user_assigned', $choices);
addChoiceBeforeAndAfter ('user_assigned', $before, $after);
addBeforeChoices ('user_assigned', '<span id="user_id0" style="display: none;"><a href="javascript:show_all_users();">Vis alle</a><br></span>');

// Program_id
$Q_programs = mysql_query("select program_id, program_name, area_id, program_desc from `programs` order by area_id, program_name");
$choices = array('0' => _('Non selected'));
$area_id = 0;		$last_id = 0;
$before = array();	$after = array();
if(mysql_num_rows($Q_programs))
{
	while( $r_choice = mysql_fetch_assoc($Q_programs))
	{
		if($r_choice['area_id'] != $area_id)
		{
			// Starts new area
			$area_id = $r_choice['area_id'];
			if($last_id != 0) {
				if(isset($after[$last_id]))
					$after[$last_id] = '</span>';
				else
					$after[$last_id] = '</span>';
			}
			$before[$r_choice['program_id']] = '<span id="areaid_'.$area_id.'" style="display: none;">';
		}
		
		if($r_choice['program_desc'] == '') {
			$r_choice['program_desc'] = '<i>Ingen beskrivelse lagt inn...</i>';
		}
		$choices[$r_choice['program_id']] = 
			'<span class="programHover" title="'.nl2br($r_choice['program_desc']).'">'.
			$r_choice['program_name'].'</span>';
		$last_id = $r_choice['program_id'];
	}
	$after[$last_id] = '</span>';
}
addChoice ('program_id', $choices);
addChoiceBeforeAndAfter ('program_id', $before, $after);

// Address_id
addChoice ('invoice_address_id', array('0' => _('No customer set.')));

// Yes/no
addChoice ('invoice', array ('1' => _('Yes'), '0' => _('No')));
addChoice ('invoice_electronic', array ('1' => _('Yes'), '0' => _('No')));
addChoice ('service_alco', array ('1' => _('Yes'), '0' => _('No')));
addChoice ('num_person_count', array ('1' => _('Yes'), '0' => _('No')));

/* ### GETTING DATA FROM ENTRY OR DEFAULTS ### */
$entry_add = TRUE;
$copy_entry = false;
if ($entry_id != 0 || isset($_GET['copy_entry_id'])) {
	if($entry_id != 0)
	{
		$entry = getEntry ($entry_id);
		$entry_add = FALSE;
	}
	else
	{
		$entry = getEntry ($_GET['copy_entry_id']);
		$copy_entry = true;
	}
	if (!count($entry))
	{
		echo _('Can\'t find entry');
		exit();
	}
	
	// Add the values to $entry_fields
	addValue('entry_id',				$entry['entry_id']);
	addValue('entry_title',				$entry['entry_title']);
	addValue('entry_type_id',			$entry['entry_type_id']);
	addValue('time_start',				$entry['time_start']);
	addValue('time_end',				$entry['time_end']);
	addValue('area_id',					$entry['area_id']);
	addValueArray('room_id',			$entry['room_id']);
	addValueArray('user_assigned',		$entry['user_assigned']);
	addValue('user_assigned2',			$entry['user_assigned2']);
	addValue('customer_id',				$entry['customer_id']);
	addValue('customer_name',			$entry['customer_name']);
	addValue('customer_municipal_num',	$entry['customer_municipal_num']);
	addValue('customer_municipal',		$entry['customer_municipal']);
	addValue('contact_person_name',		$entry['contact_person_name']);
	addValue('contact_person_phone',	$entry['contact_person_phone']);
	addValue('contact_person_email',	$entry['contact_person_email']);
	addValue('num_person_child',		$entry['num_person_child']);
	addValue('num_person_adult',		$entry['num_person_adult']);
	addValue('num_person_count',		$entry['num_person_count']);
	addValue('program_id',				$entry['program_id']);
	addValue('program_description',		$entry['program_description']);
	addValue('service_alco',			$entry['service_alco']);
	addValue('service_description',		$entry['service_description']);
	addValue('comment',					$entry['comment']);
	addValue('infoscreen_txt',			$entry['infoscreen_txt']);
	
	// Disabled of invoice_status is 2, 3 or 4
	if(!$entry_add && 
		(
			$entry['invoice_status'] == 2 || 
			$entry['invoice_status'] == 3 || 
			$entry['invoice_status'] == 4
		))
	{
		disableField('invoice');
		disableField('invoice_electronic');
		disableField('invoice_email');
		disableField('invoice_comment');
		disableField('invoice_internal_comment');
		disableField('invoice_ref_your');
		disableField('invoice_address_id');
		disableField('invoice_content');
		
		addAfterField ('invoice_address', '<br>'.
		'<input size="1" type="text" name="invoice_address_id2" disabled="disabled" id="invoice_address_id2">'.
		'<input type="button" value="'._('Choose address').'" disabled="disabled" onclick="chooseAddress(\'invoice_address_id\', \'invoice_address\'); return false;">'.
		'<input type="button" value="'._('Edit customer/address').'" disabled="disabled" onclick="new_customer(); return false;">');
	}
	addValue('invoice',					$entry['invoice']);
	addValue('invoice_electronic',		$entry['invoice_electronic']);
	addValue('invoice_email',			$entry['invoice_email']);
	addValue('invoice_comment',			$entry['invoice_comment']);
	addValue('invoice_internal_comment', $entry['invoice_internal_comment']);
	addValue('invoice_ref_your',		$entry['invoice_ref_your']);
	addValue('invoice_address_id',		$entry['invoice_address_id']);
	addValueArray('invoice_content',	$entry['invoice_content']);
}
else
{
	// Some default values
	$time_end_after_start = (60*60*2);
	
	addValueArray ('invoice', '0'); // Invoice = no
	addValue ('num_person_count', '1'); // Count = yes
	addValue ('time_start', round_t_up(time(), $resolution));
	addValue ('time_end', round_t_up((time() + $time_end_after_start), $resolution));
	addValue ('area_id', $area);
	addValueArray ('room_id', array(0 => 0));
	
	
	/* ### DATA SENDT VIA GET/POST ### */
	
	/*
		Data that can be sent:
		area_id		= ID
		room_id		= ;ID;;ID;;ID; (or just ;ID;)
		time_start	= unix timestamp
		time_end	= unix timestamp
		user_assigned	= array or ;ID;;ID;;ID; (or just ;ID;)
	*/
	
	if(isset($_GET['area_id']) && is_numeric($_GET['area_id']))
		addValue('area_id', (int)$_GET['area_id']);
	
	if(isset($_GET['room_id']))
		addValueArray('room_id', splittIDs($_GET['room_id']));
	
	if(isset($_GET['year']) && isset($_GET['month']) && isset($_GET['day']))
	{
		if(!(isset($_GET['hour']) && isset($_GET['minute'])))
		{
			//$_GET['hour']	= date('H', round_t_up(time(), $resolution));
			//$_GET['minute']	= date('i', round_t_up(time(), $resolution));
			$_GET['hour'] = 0;
			$_GET['minute'] = 0;
			$time_end_after_start = (60*60*24)-1;
		}
		$time_start = mktime ((int)$_GET['hour'], (int)$_GET['minute'], 0, (int)$_GET['month'], (int)$_GET['day'], (int)$_GET['year']);
		addValue('time_start', $time_start);
		addValue('time_end', ($time_start + $time_end_after_start));
	}
	
	// Special format:
	// year;month;day;hour;minute;room;view
	// 0;   1;    2;  3;   4;    5;    6
	if(isset($_GET['starttime']))
	{
		$abc = explode(';', $_GET['starttime']);
		if(count($abc) >= 7)
		{
			$a_year			= (int)$abc[0];
			$a_month		= (int)$abc[1];
			$a_day			= (int)$abc[2];
			$a_hour			= (int)$abc[3];
			$a_minute		= (int)$abc[4];
			$a_room			= (int)$abc[5];
			$_GET['view']	= $abc[6];
			
			$time_start = mktime ($a_hour, $a_minute, 0, $a_month, $a_day, $a_year);
			addValue('time_start', $time_start);
			addValueArray('room_id', splittIDs($a_room));
			$a_room = getRoom ($a_room);
			if(count($a_room))
			{
				addValue('area_id', $a_room['area_id']);
			}
			
			if(isset($_GET['endtime']))
			{
				$abc = explode(';', $_GET['endtime']);
				if(count($abc) >= 7)
				{
					$a_year			= (int)$abc[0];
					$a_month		= (int)$abc[1];
					$a_day			= (int)$abc[2];
					$a_hour			= (int)$abc[3];
					$a_minute		= (int)$abc[4];
					
					$time_end = mktime ($a_hour, $a_minute, 0, $a_month, $a_day, $a_year);
					addValue('time_end', $time_end);
				}
			}
		}
	}
	elseif(isset($_GET['endtime']))
	{
		$abc = explode(';', $_GET['endtime']);
		if(count($abc) >= 7)
		{
			$a_year			= (int)$abc[0];
			$a_month		= (int)$abc[1];
			$a_day			= (int)$abc[2];
			$a_hour			= (int)$abc[3];
			$a_minute		= (int)$abc[4];
			$a_room			= (int)$abc[5];
			$_GET['view']	= (int)$abc[6];
			
			$time_end = mktime ($a_hour, $a_minute, 0, $a_month, $a_day, $a_year);
			addValue('time_end', $time_end);
			addValueArray('room_id', splittIDs($a_room));
			addValue('time_start', ($time_end - $time_end_after_start));
		}
	}
	
	
	if(isset($_GET['time_start']) && is_numeric($_GET['time_start']))
		addValue('time_start', (int)$_GET['time_start']);
	
	if(isset($_GET['time_end']) && is_numeric($_GET['time_end']))
		addValue('time_end', (int)$_GET['time_end']);
	
	
	if(isset($_GET['user_assigned']))
	{
		if(is_array($_GET['user_assigned']))
			addValueArray('user_assigned', $_GET['user_assigned']);
		else
			addValueArray('user_assigned', splittIDs($_GET['user_assigned']));
	}
	
	if(isset($_GET['room']) && is_numeric($_GET['room']))
	{
		// Single room
		addValueArray('room_id', array((int)$_GET['room'] => (int)$_GET['room']));
	}
}

/*
if($entry_add)
	addField ('submit',	'submit',	_('Add/Save entry'));
else
	addField ('submit',	'submit',	_('Save entry'));
*/

// Redirect
$view = 'entry';
if(isset($_GET['view']))
{
	switch ($_GET['view'])
	{
		case 'week':
		case 'day':
			$view = $_GET['view'];
			break;
		default:
			break;
	}
}

// New data sent?
$form_errors = array();
$warnings = array();
if(isset($_POST['data_submit']))
{
	/*
	If some is sent:
	- Get data and insert with addValue
	- validate
	-- Add / update DB
	-- exit();
	- else
	-- generate errors and give them to the form (which displays them)
	*/
	
	require( 'edit_entry2_datasubmit.php' );
}

// Get addresses for invoice if customer is set
/*
if($entry_fields['customer_id']['value'] != '')
{
	$customer = getCustomer($entry_fields['customer_id']['value']);
	if(count($customer))
	{
		$address_choice = array();
		foreach ($customer['customer_address'] as $address)
		{
			$address_choice[$address['address_id']] = $address['address_full'];
		}
		if(count($address_choice))
			addChoice ('invoice_address_id', $address_choice);
		else
			addChoice ('invoice_address_id', array('0' => ));
	}
}*/

require ( 'edit_entry2_form.php' );

?>