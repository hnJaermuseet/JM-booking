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

function filterAddAlternative ($var, $type, $name)
{
	global $alternatives;
	
	$alternatives[$var]['var']		= $var;
	$alternatives[$var]['type']		= $type;
	$alternatives[$var]['name']		= $name;
	
	$alternatives[$var]['choice']		= array();
	
	if($type == 'id')
		$alternatives[$var]['table']	= array();
}

function filterAssignTable ($var, $table, $id_field, $value_field) {
	global $alternatives;
	$alternatives[$var]['table'] = array('table' => $table, 'id_field' => $id_field, 'value_field' => $value_field);
}

function filterMakeAlternatives () {
	global $alternatives;
	$alternatives = array();
	
	/*
	 * Filtertypes:
	 * - text
	 * - select
	 * - date
	 * - id			field = 'VALUE'
	 * - id2		field LIKE = '%;VALUE;%'
	 * - num
	 * - bool
	 */
	
	filterAddAlternative ('entry_id',					'id',			__('Entry id'));
	filterAddAlternative ('entry_name',					'text',			__('Entry name'));
	filterAddAlternative ('entry_title',				'text',			__('Entry title'));
	filterAddAlternative ('entry_type_id',				'id',			__('Entry type')); filterAssignTable ('entry_type_id', 'entry_type', 'entry_type_id', 'entry_type_name');
	filterAddAlternative ('time_start',					'date',			__('Start time'));
	filterAddAlternative ('time_end',					'date',			__('End time'));
	filterAddAlternative ('area_id',					'select',		__('Area'));
	filterAddAlternative ('room_id',					'select',		__('Room'));
	filterAddAlternative ('user_assigned',				'id2',			__('Users assigned')); // filterAssignTable ('user_assigned', 'users', 'user_id', 'user_name');
	filterAddAlternative ('user_assigned2',				'text',			__('Manuel user assigned'));
	filterAddAlternative ('customer_name',				'text',			__('Customer'));
	filterAddAlternative ('customer_id',				'id',			__('Customer ID')); filterAssignTable ('customer_id', 'customer', 'customer_id', 'customer_name');
	filterAddAlternative ('customer_municipal',			'text',			__('Municipal'));
	filterAddAlternative ('customer_municipal_num',		'num',			__('Municipal number'));
	filterAddAlternative ('contact_person_name',		'text',			__('Contact person'));
	filterAddAlternative ('contact_person_phone',		'text',			__('Contact telephone'));
	filterAddAlternative ('contact_person_email',		'text',			__('Contact email'));
	filterAddAlternative ('num_person_child',			'num',			__('Number of children'));
	filterAddAlternative ('num_person_adult',			'num',			__('Number of adults'));
	filterAddAlternative ('num_person_count',			'bool',			__('Count these numbers'));
	filterAddAlternative ('service_description',		'text',			__('Service description'));
	filterAddAlternative ('service_alco',				'bool',			__('Serve alcohol?'));
	filterAddAlternative ('program_id',					'id',			__('Fixed programs')); filterAssignTable ('program_id', 'programs', 'program_id', 'program_name');
	filterAddAlternative ('program_description',		'text',			__('Program description'));
	filterAddAlternative ('comment',					'text',			__('Comment'));
	filterAddAlternative ('infoscreen_txt',				'text',			_h('Text on infoscreen'));
	filterAddAlternative ('invoice',					'bool',			__('Invoice'));
	filterAddAlternative ('invoice_ref_your',			'text',			__('Invoice - Your referance'));
	filterAddAlternative ('invoice_comment',			'text',			__('Invoice comment - to customer'));
	filterAddAlternative ('invoice_internal_comment',	'text',			__('Invoice comment - internal'));
	filterAddAlternative ('invoice_address_id',			'text',			__('Invoice address ID'));
	filterAddAlternative ('invoice_electronic',			'bool',			__('Electronic invoice'));
	filterAddAlternative ('invoice_email',				'text',			__('E-mail for electronic invoice'));
	filterAddAlternative ('invoice_status',				'select',		__('Status of invoice'));
	filterAddAlternative ('confirm_email',				'bool',			__('Confirmation sent'));
	
	// Datanova
	filterAddAlternative ('tamed_booking',              'bool',        'Ta med fra booking');
	filterAddAlternative ('tamed_datanova',             'bool',        'Ta med fra Datanova');
	filterAddAlternative ('dn_kategori_id',             'id',          'Datanova-kategorier'); filterAssignTable ('dn_kategori_id', 'import_dn_kategori', 'kat_id', 'kat_navn');
	
	// Deleted entries
	filterAddAlternative ('deleted',                    'bool',        'Slettede bookinger');
	
	// TODO: Add status of invoice choices
	
	// Area_id
	$alternatives['area_id']['choice'] = array();
	$alternatives['area_id']['choice'][0] = __('Select one');
	$Q = mysql_query("select id as area_id, area_name from `mrbs_area` order by 'area_name'");
	while( $r_choice = mysql_fetch_assoc($Q))
		$alternatives['area_id']['choice'][$r_choice['area_id']]	= $r_choice['area_name'];
	
	// Room_id
	$alternatives['room_id']['choice'] = array();
	$alternatives['room_id']['choice'][0] = __('Whole area');
	$Q = mysql_query("select id as room_id, room_name, area_id from `mrbs_room` order by area_id, room_name");
	while( $r_choice = mysql_fetch_assoc($Q))
		$alternatives['room_id']['choice'][$r_choice['room_id']]
		 = 
			$alternatives['area_id']['choice'][$r_choice['area_id']].
			' - '.$r_choice['room_name'];
	
	// User_id
	$alternatives['user_assigned']['choice'] = array();
	$alternatives['user_assigned']['choice'][0] = __('Nobody');
	$Q = mysql_query("select user_id, user_name from `users` order by 'user_name'");
	while( $r_choice = mysql_fetch_assoc($Q))
		$alternatives['user_assigned']['choice'][$r_choice['user_id']]	= $r_choice['user_name'];
	
	
	$alternatives['invoice_status']['choice'] = array();
	$alternatives['invoice_status']['choice'][0] = __('Not to be made');
	$alternatives['invoice_status']['choice'][1] = 'skal lages, ikke klargjort';
	$alternatives['invoice_status']['choice'][2] = 'skal lages, klar til fakturering';
	$alternatives['invoice_status']['choice'][3] = 'sendt regnskap';
	
}

function filterGetFromSerialized ($serialized) {
	return unserialize(htmlspecialchars_decode($serialized,ENT_QUOTES));
}

function readFiltersFromGet ()
{
	global $alternatives;
	
	$filters = array();
	if(isset($_GET['rows']) && is_array($_GET['rows']) &&
	   isset($_GET['filter']) && is_array($_GET['filter']))
	{
		foreach ($_GET['rows'] as $id)
		{
			if(isset($_GET['filter'][$id]) && isset($_GET['filtervalue1_'.$id]))
			{
				// Verifing that the type of filter exists:
				if(isset($alternatives[$_GET['filter'][$id]]))
				{
					$filter	= $_GET['filter'][$id];
					$value	= $_GET['filtervalue1_'.$id];
					$value2	= '';
					$dont_set = false;
					if(isset($_GET['filtervalue2_'.$id]))
						$value2	= $_GET['filtervalue2_'.$id];
					
					switch ($alternatives[$filter]['type'])
					{
						case 'date':
							if($value != 'current')
							{
								$value	= getTime($value, array('y', 'm', 'd', 'h', 'i'));
								if($value == 0)
									$value = 'current';
							}
							break;
						
						case 'bool':
							// Must be true or false
							if($value == 0)
								$value = false;
							elseif($value == 1)
								$value = true;
							else
								$dont_set = true;
							break;
						
						case 'select':
							// Need to be one of the alternatives
							if(!isset($alternatives[$filter]['choice'][$value]))
								$dont_set = true; // Invalid
							break;
							
						case 'id':
						case 'id2':
							$value	= (int)$value;
							//if($value == 0)
							//	$dont_set = true;
							
							// TODO: Make something that checkes against DB
							break;
						
						case 'text':
							$value = slashes(htmlspecialchars($value,ENT_QUOTES));
							break;
						
						case 'num':
							if(!is_numeric($value)) {
								$value = 0;
							}
							break;
					}
					
					if(!$dont_set)
						$filters[] = array(
							$filter,
							$value,
							$value2);
				}
			}
		}
	}
	return $filters;
}

function addFilter ($filtertable, $filtertype, $value1, $value2 = '') {
	if(!is_array($filtertable))
		return array();
	else
	{
		$filtertable[] = array ($filtertype, $value1, $value2);
		return $filtertable;
	}
}

function genSQLFromFilters ($filters, $FieldsToSelect = '*') {
	global $alternatives;
	
	$SQL = "select $FieldsToSelect from `GET_FROM_TABLE` where 1";
	$used_filtertypes = array();
	$get_deleted = false;
	foreach ($filters as $filter)
	{
		if(
			$filter[0] == 'tamed_booking' ||
			$filter[0] == 'tamed_datanova' ||
			$filter[0] == 'dn_kategori_id'
		)
		{
			continue;
		}
		
		if($filter[0] == 'deleted')
		{
			if($filter[1] == '1')
				$get_deleted = true;
			continue;
		}
		
		$thisFilter = "`".$filter[0]."` ";
		switch ($alternatives[$filter[0]]['type']) {
			
			case 'select':
				if($filter[0] == 'room_id') {
					$thisFilter .= 'LIKE'; break;
				}
			case 'bool':
			case 'id':
				$thisFilter .= '='; break;
			
			case 'id2':
				$thisFilter .= 'LIKE'; break;
			
			case 'text':
				switch($filter[2])
				{
					case 'is':
						$thisFilter .= '='; // Is
						break;
					
					default:
						$thisFilter .= 'LIKE'; // Match
						break;
				}
				break;
			
			case 'date':
				if($filter[1] == 'current')
					$filter[1] = time();
			case 'num':
				$thisFilter .= $filter[2]; break; // Value2
		}
		$thisFilter .= ' ';
		switch ($alternatives[$filter[0]]['type']) {
			case 'id2':
				$thisFilter .= "'%;".$filter[1].";%'"; break; // Value1
			case 'text':
				switch($filter[2])
				{
					case 'is':
						$thisFilter .= "'".$filter[1]."'"; // Is
						break;
					
					default:
						$thisFilter .= "'%".$filter[1]."%'"; // Value1, matcher
						break;
				}
				break;
			
			case 'select':
				if($filter[0] == 'room_id') {
					$thisFilter .= "'%;".$filter[1].";%'"; break;
				}
			default:
				$thisFilter .= "'".$filter[1]."'"; break; // Value1
		}
		
		if(isset($used_filtertypes[$filter[0]]))
		{
			// Already in use, we must make a or-statment
			$SQL = str_replace(" AND (`".$filter[0]."`", " AND (".$thisFilter." OR `".$filter[0]."`", $SQL);
		}
		else
		{
			$SQL .= " AND (".$thisFilter.")";
		}
		$used_filtertypes[$filter[0]] = true;
	}
	
	// Adding the room_id=0, all rooms
	//$SQL = str_replace(" AND (`room_id`", " AND (`room_id` LIKE '%;0;%' OR `".$filter[0]."`", $SQL);
	// => disabled, gives some undesirable effects
	
	
	if(!$get_deleted)
		$SQL = str_replace('`GET_FROM_TABLE`', '`entry`', $SQL);
	else
		$SQL = str_replace('`GET_FROM_TABLE`', '`entry_deleted`', $SQL);
	
	return $SQL;
}

function genSQLFromFiltersDatanova ($filters, $FieldsToSelect = '*') {
	global $alternatives;
	
	$SQL = "select $FieldsToSelect from `import_dn_tall` where 1";
	$used_filtertypes = array();
	foreach ($filters as $filter)
	{
		if(
			$filter[0] != 'dn_kategori_id' &&
			$filter[0] != 'time_start' &&
			$filter[0] != 'time_end' &&
			$filter[0] != 'area_id'
		)
		{
			continue;
		}
		
		if($filter[0] == 'dn_kategori_id')
			$col_name = 'kat_id';
		else
			$col_name = $filter[0];
		
		$thisFilter = "`".$col_name."` ";
		switch ($alternatives[$filter[0]]['type']) {
			
			case 'select':
				if($filter[0] == 'room_id') {
					$thisFilter .= 'LIKE'; break;
				}
			case 'bool':
			case 'id':
				$thisFilter .= '='; break;
			
			case 'id2':
				$thisFilter .= 'LIKE'; break;
			
			case 'text':
				switch($filter[2])
				{
					case 'is':
						$thisFilter .= '='; // Is
						break;
					
					default:
						$thisFilter .= 'LIKE'; // Match
						break;
				}
			
			case 'date':
				if($filter[1] == 'current')
					$filter[1] = time();
			case 'num':
				$thisFilter .= $filter[2]; break; // Value2
		}
		$thisFilter .= ' ';
		switch ($alternatives[$filter[0]]['type']) {
			case 'id2':
				$thisFilter .= "'%;".$filter[1].";%'"; break; // Value1
			case 'text':
				$thisFilter .= "'%".$filter[1]."%'"; break; // Value1, matcher
			default:
				$thisFilter .= "'".$filter[1]."'"; break; // Value1
		}
		
		if($filter[0] == 'time_start')
		{
			$time_start    = $filter[1];
			$time_start_2  = $filter[2];
			continue;
		}
		if($filter[0] == 'time_end')
		{
			$time_end    = $filter[1];
			$time_end_2  = $filter[2];
			continue;
		}
		
		if(isset($used_filtertypes[$filter[0]]))
		{
			// Already in use, we must make a or-statment
			$SQL = str_replace(" AND (`".$col_name."`", " AND (".$thisFilter." OR `".$col_name."`", $SQL);
		}
		else
		{
			$SQL .= " AND (".$thisFilter.")";
		}
		$used_filtertypes[$filter[0]] = true;
	}
	
	// Detect time span
	// time_start, time_start_2, time_end, time_end_2
	$lower = null;
	$upper = null;
	
	if($time_start_2 == '>=')
		$lower = $time_start-1;
	elseif($time_start_2 == '>')
		$lower = $time_start;
	elseif($time_end_2 == '>=')
		$lower = $time_end-1;
	elseif($time_end_2 == '>')
		$lower = $time_end;
	
	if($time_start_2 == '<=')
		$upper = $time_start+1;
	elseif($time_start_2 == '<')
		$upper = $time_start;
	elseif($time_end_2 == '<=')
		$upper = $time_end+1;
	elseif($time_end_2 == '<')
		$upper = $time_end;
	
	if(!is_null($lower))
			$SQL .= " AND (`dag` >= '".$lower."')";
	if(!is_null($lower))
			$SQL .= " AND (`dag` < '".$upper."')";
	
	// Adding the room_id=0, all rooms
	$SQL = str_replace(" AND (`room_id`", " AND (`room_id` LIKE '%;0;%' OR `".$filter[0]."`", $SQL);
	return $SQL;
}

function filterSerialized($filtertable) {
	return htmlspecialchars(serialize($filtertable),ENT_QUOTES);
}

function filterLink ($filtertable, $return_to = '') {
	$filtertable_serialized = filterSerialized($filtertable);
	echo '[ <font size="1">';
	
	echo '<img height="12" src="./img/icons/table_multiple.png" style="border: 0px solid black; vertical-align: middle;"> '.
	'<a href="entry_filters.php?filters='.$filtertable_serialized.'&amp;return_to='.$return_to.'">'.__('View / Edit filters').'</a> -:- ';
	
	echo '<img height="12" src="./img/icons/table.png" style="border: 0px solid black; vertical-align: middle;"> '.
	'<a href="entry_list.php?filters='.$filtertable_serialized.'">'.__('Entry list').'</a> -:- ';
	
	echo '<img height="12" src="./img/icons/chart_bar.png" style="border: 0px solid black; vertical-align: middle;"> '.
	'<a href="entry_stat.php?filters='.$filtertable_serialized.'">'.__('Statistics').'</a> -:- ';
	
	echo '<img height="12" src="./img/icons/group.png" style="border: 0px solid black; vertical-align: middle;"> '.
	'<a href="entry_list.php?listtype=customer_list&amp;filters='.$filtertable_serialized.'">'._h('Customer list').'</a> ';

	echo '<img height="12" src="./img/icons/icon-file-excel.gif" style="border: 0px solid black; vertical-align: middle;"> '.
	'<a href="entry_list_export_excel.php?filters='.$filtertable_serialized.'">'._h('Entry list in Excel').'</a> ';
	
	echo '</font> ]';
}

function filterToText ($filtertable) {
	global $alternatives;
	
	$return = __('Getting entries where ');
	$i = 0;
	$last_type = '';
	foreach ($filtertable as $filter)
	{
		$i++;
		
		if($i > 1) // != count($filtertable))
		{
			if($filter[0] == $last_filter)
				$return .= ' eller ';
			else
				$return .= ' '.__('and').' ';
		}
		
		$return .= '<b>'.strtolower($alternatives[$filter[0]]['name']).'</b> ';
		switch ($alternatives[$filter[0]]['type']) {
			case 'bool':
			case 'select':
			case 'id':
				$return .= __('is'); break;
			
			case 'id2':
				$return .= __('contains'); break;
			
			case 'text':
				switch($filter[2])
				{
					case 'is':
						$return .= _h('is');
						break;
					
					default:
						$return .= __('matches');
						break;
				}
				
			case 'date':
			case 'num':
				switch($filter[2]) {
					case '=':	$return .= _h('is');							break;
					case '>';	$return .= _h('is bigger than');				break;
					case '>=':	$return .= _h('is bigger than or same as');	break;
					case '<':	$return .= _h('is less than');					break;
					case '<=':	$return .= _h('is less than or same as');		break;
				}
		}
		$return .= ' <b>';
		if($alternatives[$filter[0]]['type'] == 'date' && $filter[1] == 'current') {
			$return .= _h('current time');
		}
		elseif($alternatives[$filter[0]]['type'] == 'date') {
			$return .= date('H:i d-m-Y', $filter[1]);
		}
		elseif($alternatives[$filter[0]]['type'] == 'bool') {
			if($filter[1])
				$return .= __('true');
			else
				$return .= __('false');
		}
		elseif($alternatives[$filter[0]]['type'] == 'select') {
			$return .= $alternatives[$filter[0]]['choice'][$filter[1]];
		}
		elseif($alternatives[$filter[0]]['type'] == 'text') {
			$return .= '"'.$filter[1].'"';
		}
		elseif($alternatives[$filter[0]]['type'] == 'id') {
			if(isset($alternatives[$filter[0]]['table']) && count($alternatives[$filter[0]]['table']))
			{
				$table = $alternatives[$filter[0]]['table'];
				$Q_id = mysql_query('
					SELECT 
						'.$table['id_field'].' AS id, 
						'.$table['value_field'].' AS value
					FROM '.$table['table'].'
					WHERE '.$table['id_field'].' = "'.$filter[1].'"');
				if(mysql_num_rows($Q_id))
					$return .= mysql_result($Q_id, '0', 'value').' (id '.mysql_result($Q_id, '0', 'id').')';
				else
					$return .= 'id '.$filter[1];
			}
			else
				$return .= 'id '.$filter[1];
		}
		else {
			$return .= $filter[1];
		}
		$return .= '</b>';
		
		$last_filter = $filter[0];
	}
	
	return $return;
}

function filterPrint ($filtertable) {
	echo filterToText($filtertable);
}
