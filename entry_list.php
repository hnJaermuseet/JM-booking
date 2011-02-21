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

include_once("glob_inc.inc.php");

filterMakeAlternatives();

print_header($day, $month, $year, $area);

if(!isset($_GET['listtype']))
	$listtype = '';
else
	$listtype = $_GET['listtype'];
$addAdfterSQL = '';
$return_to = 'entry_list';
switch($listtype)
{
	case 'not_confirmed':
		echo '<h1>'._('Entries without confirmation sent').'</h1>';
		echo _('Entries in the past is not shown.').'<br><br>'.chr(10).chr(10);
		$filters = array();
		$filters = addFilter($filters, 'confirm_email', '0');
		$filters = addFilter($filters, 'time_start', 'current', '>');
		if($area != '')
			$filters = addFilter($filters, 'area_id', $area);
		$SQL = "select entry_id from `entry` where confirm_email = '0' and time_start > '".time()."' order by `time_start`";
		//$SQL = "select entry_id from `entry` where confirm_email = '0' order by `time_start`";
		break;
	
	case 'no_user_assigned':
		echo '<h1>'._('Entries without any assigned user').'</h1>';
		echo _('Entries in the past is not shown.').'<br><br>'.chr(10).chr(10);
		$filters = array();
		$filters = addFilter($filters, 'user_assigned', '0');
		$filters = addFilter($filters, 'user_assigned2', '');
		$filters = addFilter($filters, 'time_start', 'current', '>');
		if($area != '')
			$filters = addFilter($filters, 'area_id', $area);
		$SQL = "select entry_id from `entry` where user_assigned = ';0;' and user_assigned2 = '' and time_start > '".time()."' order by `time_start`";
		//$SQL = "select entry_id from `entry` where user_assigned = ';0;' and user_assigned2 = '' order by `time_start`";
		break;
	
	case 'next_100':
		echo '<h1>'._('Next 100 entries').'</h1>';
		echo _('Entries in the past is not shown.').'<br><br>'.chr(10).chr(10);
		$filters = array();
		$filters = addFilter($filters, 'time_start', 'current', '>');
		if($area != '')
			$filters = addFilter($filters, 'area_id', $area);
		$addAdfterSQL = ' limit 100';
		$SQL = "select entry_id from `entry` where time_start > '".time()."' order by `time_start` limit 100";
		break;
	
	case 'servering':
		echo '<h1>Bookinger med servering fremover</h1>';
		echo _('Entries in the past is not shown.').'<br><br>'.chr(10).chr(10);
		$filters = array();
		$filters = addFilter($filters, 'time_start', 'current', '>');
		$filters = addFilter($filters, 'service_description', '_%');
		if($area != '')
			$filters = addFilter($filters, 'area_id', $area);
		$SQL = "select entry_id from `entry` where time_start > '".time()."' order by `time_start` limit 100";
		break;
	
	case 'customer_list':
		echo '<h1>Kundeliste</h1>';
		if(!isset($_GET['filters']))
			$_GET['filters'] = '';
		
		$filters = filterGetFromSerialized($_GET['filters']);
		if(!$filters)
			$filters = array();
		
		$return_to = 'customer_list';
		break;
		
	default:
		echo '<h1>'._('Entry list').'</h1>';
		if(!isset($_GET['filters']))
			$_GET['filters'] = '';
		
		$filters = filterGetFromSerialized($_GET['filters']);
		if(!$filters)
			$filters = array();
		
		$return_to = 'entry_list';
		break;
}

$SQL = genSQLFromFilters($filters, 'entry_id');
$SQL .= " order by `time_start`".$addAdfterSQL;

echo '<div class="hiddenprint">';
filterLink($filters, $return_to);	echo '</div>'.chr(10);
filterPrint($filters);				echo '<br>'.chr(10);
echo '<br>'.chr(10).chr(10);

$emaillist_entry = '';

$tamed_booking = true;
foreach($filters as $filter) {
	if($filter[0] == 'tamed_booking')
	{
		$tamed_booking  = $filter[1];
	}
}

$Q = mysql_query($SQL);
if(!$tamed_booking || !mysql_num_rows($Q))
{
	echo _('No entries found.');
}
else
{
	echo mysql_num_rows($Q).' '._('entries found.');
	//echo '<br><br>'.chr(10).chr(10);
	echo '<table class="prettytable">'.chr(10);
	echo ' <tr>'.chr(10);
	if($listtype == 'customer_list')
	{
		echo '  <th style="vertical-align: bottom;">'.iconHTML('group').'&nbsp;Kundenavn</th>'.chr(10);
		echo '  <th style="vertical-align: bottom;">'.iconHTML('user_small').'&nbsp;B&nbsp;/&nbsp;'.
		iconHTML('user_suit').'&nbsp;V</th>'.chr(10);
		echo '  <th style="vertical-align: bottom;">'.iconHTML('page_white').'&nbsp;Antall booking</th>'.chr(10);
		
		$customer_list = array();
		$customer_names = array();
	}
	else
	{
		echo '  <th style="vertical-align: bottom;">'._('Starts').'</th>'.chr(10);
		echo '  <th style="vertical-align: bottom;">'._('Ends').'</th>'.chr(10);
		echo '  <th style="vertical-align: bottom;">'._('C/A').'</th>'.chr(10);
		echo '  <th style="vertical-align: bottom;">'._('Entry type').'</th>'.chr(10);
		echo '  <th style="vertical-align: bottom;">'._('Name').'</th>'.chr(10);
		if($listtype == 'servering')
		{
			echo '  <th style="vertical-align: bottom;">Alkohol?</th>'.chr(10);
			echo '  <th style="vertical-align: bottom;">'._('Service description').'</th>'.chr(10);
		}
		echo '  <th style="vertical-align: bottom;">'._('Room').'</th>'.chr(10);
	}
	echo ' </tr>'.chr(10);
	
	while($R = mysql_fetch_assoc($Q))
	{
		$entry = getEntry($R['entry_id']);
		
		if($listtype == 'customer_list')
		{
			if(isset($customer_list[$entry['customer_id']]))
			{
				$customer_list[$entry['customer_id']]['c'] += $entry['num_person_child'];
				$customer_list[$entry['customer_id']]['a'] += $entry['num_person_adult'];
				$customer_list[$entry['customer_id']]['e']++;
			}
			else
			{
				$customer_list[$entry['customer_id']] = getCustomer($entry['customer_id']);
				if(!count($customer_list[$entry['customer_id']]))
				{
					$customer_list[$entry['customer_id']]['customer_id'] = $entry['customer_id'];
					if($entry['customer_id'] == 0)
						$customer_list[$entry['customer_id']]['customer_name'] = 'Ingen kunde valgt';
					else
						$customer_list[$entry['customer_id']]['customer_name'] = 'KUNDE IKKE FUNNET I DATABASE';
					$customer_names[$entry['customer_id']] = ''; // Sort the unknowns to the top
				}
				else
					$customer_names[$entry['customer_id']] = $customer_list[$entry['customer_id']]['customer_name'];
				$customer_list[$entry['customer_id']]['c'] = $entry['num_person_child'];
				$customer_list[$entry['customer_id']]['a'] = $entry['num_person_adult'];
				$customer_list[$entry['customer_id']]['e'] = 1;
				
			}
		}
		else
		{
			echo ' <tr>'.chr(10);
			
			// Starts
			echo '  <td>';
			echo date('H:i', $entry['time_start']).'&nbsp;';
			echo '<a href="day.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'.date('d',$entry['time_start']).'</a>-';
			echo '<a href="month.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'._(date('m',$entry['time_start'])).'</a>-';
			echo date('Y', $entry['time_start']);
			echo '</td>'.chr(10);
			
			// Ends
			echo '  <td>';
			echo date('H:i', $entry['time_end']).'&nbsp;';
			echo '<a href="day.php?year='.date('Y', $entry['time_end']).'&amp;month='.date('m', $entry['time_end']).'&amp;day='.date('d', $entry['time_end']).'&amp;area='.$entry['area_id'].'">'.date('d',$entry['time_end']).'</a>-';
			echo '<a href="month.php?year='.date('Y', $entry['time_end']).'&amp;month='.date('m', $entry['time_end']).'&amp;day='.date('d', $entry['time_end']).'&amp;area='.$entry['area_id'].'">'._(date('m',$entry['time_end'])).'</a>-';
			echo date('Y', $entry['time_end']);
			echo '</td>'.chr(10);
			
			// Child / adult
			echo '  <td>';
			echo '<font size="1">'.$entry['num_person_child'].'&nbsp;/&nbsp;'.$entry['num_person_adult'].'</font>';
			echo '</td>';
	
			// Type
			$entrytype = getEntryType($entry['entry_type_id']);
			echo '  <td>';
			if(count($entrytype))
				echo '<font size="1">'.$entrytype['entry_type_name'].'</font>';
			echo '</td>';
			
			// Name
			echo '  <td><a href="entry.php?entry_id='.$entry['entry_id'].'">'.
			$entry['entry_name'].'</a></td>'.chr(10);
		
			// Serveringsbeskrivelse
			if($listtype == 'servering')
			{
				echo '  <td>';
				if($entry['service_alco'])
					echo 'ja';
				else
					echo 'nei';
				echo '</td>'.chr(10);
				
				echo '  <td>'.nl2br($entry['service_description']).'</td>'.chr(10);
			}
			
			// Room
			echo '  <td>';
			$rooms = array();
			if(!count($entry['room_id']))
				$rooms[] = '<i>'._('Whole area').'</i>';
			elseif(count($entry['room_id']) == '1')
			{
				// Single room
				foreach ($entry['room_id'] as $rid)
				{
					if ($rid == '0')
						$rooms[] = '<i>'._('Whole area').'</i>';
					else
					{
						$room = getRoom($rid);
						if(count($room))
							$rooms[] = $room['room_name'];
						else
							$rooms[] = _('Can\'t find room');
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
						$room = getRoom($rid);
						if(count($room))
							$rooms[] = $room['room_name'];
					}
				}
				if(!$rooms)
					$rooms[] = _('Whole area');
			}
			echo implode(', ', $rooms);
			echo '</td>'.chr(10);
			
			echo ' </tr>'.chr(10);
		}
		
		if(isset($_GET['emaillist']) && $_GET['emaillist'] == 1 && $entry['contact_person_email'] != '')
		{
			if($entry['customer_name'] != '')
				$emaillist_entry[$entry['contact_person_email']] = $entry['contact_person_email'].' <'.trim($entry['contact_person_name']).' - '.trim($entry['customer_name']).'>';
			else
				$emaillist_entry[$entry['contact_person_email']] = $entry['contact_person_email'].' <'.trim($entry['contact_person_name']).'>';
		}
	}
	
	if($listtype == 'customer_list')
	{
		// Sorting
		natcasesort($customer_names);
		foreach($customer_names as $customer_id => $customer_name)
		{
			$c = $customer_list[$customer_id];
			echo '	<tr>'.chr(10);
			echo '		<td>';
				if($c['customer_id'] > 0)
					echo '<a href="customer.php?customer_id='.$c['customer_id'].'">';
				echo $c['customer_name'];
				if($c['customer_id'] > 0)
					echo '</a>';
			echo '</td>';
			echo '		<td style="text-align: center;">'.$c['c'].'&nbsp;/&nbsp;'.$c['a'].'</td>'.chr(10);
			echo '		<td>';
				$tmpfilter = $filters;
				$tmpfilter = addFilter($tmpfilter, 'customer_id', $c['customer_id']);
				echo '<a href="entry_list.php?filters='.filterSerialized($tmpfilter).'">';
				echo $c['e'].' boooking';
				if($c['e'] != '1')
					echo 'er';
				echo '</a>';
			echo '</td>'.chr(10);
			echo '	</tr>'.chr(10);
		}
	}
	echo '</table>';
}


if(isset($_GET['emaillist']) && $_GET['emaillist'] == 1)
{
	echo '<textarea rows="60" cols="150">';
	echo implode("\n", $emaillist_entry);
	echo '</textarea>';
	$emaillist_entry2 = array();
	foreach($emaillist_entry as $email)
		$emaillist_entry2[] = strip_tags($email);
	echo '<textarea rows="60" cols="150">';
	echo implode("\n", $emaillist_entry2);
	echo '</textarea>';
}

?>