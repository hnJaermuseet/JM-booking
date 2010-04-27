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
	JM-booking, made by Hallvard Nygard
	- Started 14.02.2008
	
	View an entry
*/

include_once("glob_inc.inc.php");


if(isset($_POST['entry_id']) && is_numeric($_POST['entry_id']))
	$entry_id = (int)$_POST['entry_id'];
elseif(isset($_GET['entry_id']) && is_numeric($_GET['entry_id']))
	$entry_id = (int)$_GET['entry_id'];
else
{
	print_header($day, $month, $year, $area);
	echo _('Error: No entry spesified.');
	exit();
}

$entry = getEntry ($entry_id);
if (!count($entry))
{
	echo _('Can\'t find entry');
	exit();
}


$day	= date('d', $entry['time_start']);
$month	= date('m', $entry['time_start']);
$year	= date('Y', $entry['time_start']);
$area	= $entry['area_id'];


print_header($day, $month, $year, $area);

readEntry ($entry['entry_id'], $entry['rev_num']);

echo '<span class="hiddenprint">';
echo '<h1>'._('Viewing entry').'</h1>'.chr(10).chr(10);
echo '- <a href="edit_entry2.php?entry_id='.$entry['entry_id'].'">'.
iconHTML('page_white_edit').' '.
_('Edit this entry').'</a><br><br></span>';

echo '<span class="print"><h2>'.date('Y-m-d', $entry['time_start']).': '.$entry['entry_name'].'</h2></span>'.chr(10);
echo '<table>';

echo '<tr>
	<td align="right"><b>'._('Entry name').': </td>
	<td>'.$entry['entry_name'].'</td>
</tr>'.chr(10);

echo '<tr>
	<td align="right">'.iconHTML('page_white_star').' '.'<b>'._('Entry ID').':</b> </td>
	<td>'.$entry['entry_id'].'</td>
</tr>'.chr(10);

echo '<tr>
	<td align="right"><b>'._('Entry title').':</b> </td>
	<td>';
	if($entry['entry_title'] == '')
		echo '<i>'._('Non').'</i>';
	else
		echo $entry['entry_title'];
	echo '</td>
</tr>'.chr(10);

echo '<tr>
	<td align="right">'.iconHTML('page_white_stack').' '.'<b>'._('Entry type').':</b> </td>
	<td>';
	if($entry['entry_type_id'] == '0')
		echo '<i>'._('Non').'</i>';
	else
	{
		$entry_type = getEntryType($entry['entry_type_id']);
		if(count($entry_type))
			echo $entry_type['entry_type_name'];
	}
	echo '</td>
</tr>'.chr(10);

echo '<tr>
	<td align="right">'.iconHTML('date_previous').' '.'<b>'._('Starts').':</b> </td>
	<td>';
	echo date('H:i ', $entry['time_start']).' '.
	strtolower(_(date('l', $entry['time_start']))).' ';
	echo '<a href="day.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'.
	date('j',$entry['time_start']).'</a>. ';
	echo '<a href="month.php?year='.date('Y', $entry['time_start']).'&amp;month='.date('m', $entry['time_start']).'&amp;day='.date('d', $entry['time_start']).'&amp;area='.$entry['area_id'].'">'._(date('M',$entry['time_start'])).'</a> ';
	echo date('Y', $entry['time_start']);
	echo '</td>
</tr>'.chr(10);

echo '<tr><td align="right">'.
	iconHTML('date_next').' '.
	'<b>'._('Finished').':</b> </td><td>';
	echo date('H:i ', $entry['time_end']).' '.
	strtolower(_(date('l', $entry['time_end']))).' ';
	echo '<a href="day.php?year='.date('Y', $entry['time_end']).'&amp;month='.date('m', $entry['time_end']).'&amp;day='.date('d', $entry['time_end']).'&amp;area='.$entry['area_id'].'">'.date('j',$entry['time_end']).'</a>. ';
	echo '<a href="month.php?year='.date('Y', $entry['time_end']).'&amp;month='.date('m', $entry['time_end']).'&amp;day='.date('d', $entry['time_end']).'&amp;area='.$entry['area_id'].'">'._(date('M',$entry['time_end'])).'</a> ';
	echo date('Y', $entry['time_end']);
echo '</td></tr>'.chr(10);

echo '<tr>
	<td align="right">'.iconHTML('user').' <b>'._('User(s) assigned to').':</b> </td>
	<td>';
	$users_array = array();
	foreach ($entry['user_assigned'] as $user_id)
	{
		$user = getUser($user_id);
		if(count($user))
			$users_array[] = '<a href="user.php?user_id='.$user['user_id'].'">'.$user['user_name'].'</a>';
	}
	if($entry['user_assigned2'] != '') {
		$users_array[] = $entry['user_assigned2'];
	}
	if(!count($users_array)) {
		$users_array[] = '<i>'.iconHTML('user_delete').' '._('Nobody').'</i>';
	}
	echo implode(', ', $users_array);
	echo '</td>
</tr>'.chr(10);

echo '<tr>
	<td align="right">'.iconHTML('house').' <b>'._('Area').':</b> </td>';
	$area = getArea($entry['area_id']);
	echo "\t".'<td>'.$area['area_name'].'</td>
</tr>'.chr(10);

echo '<tr><td align="right">'.
'<img src="./img/icons/shape_square.png" style="border: 0px solid black; vertical-align: middle;"> '.
'<b>'._('Room').':</b> </td><td>';
if(!count($entry['room_id']))
	echo '<i>'._('Whole area').'</i>';
elseif(count($entry['room_id']) == '1')
{
	// Single room
	foreach ($entry['room_id'] as $rid)
	{
		if ($rid == '0')
			echo '<i>'._('Whole area').'</i>';
		else
		{
			$room = getRoom($rid);
			if(count($room))
				echo $room['room_name'];
			else
				echo _('Can\'t find room');
		}
	}
}
else
{
	echo '<ul>'.chr(10);
	$rooms = false;
	foreach ($entry['room_id'] as $rid)
	{
		if($rid != '0')
		{
			$rooms = true;
			$room = getRoom($rid);
			if(count($room))
				echo '<li>'.$room['room_name'].'</li>'.chr(10);
		}
	}
	if(!$rooms)
		echo '<li>'._('Whole area').'</li>'.chr(10);
	echo '</ul>'.chr(10);
}
echo '</td></tr>'.chr(10);

echo '<tr><td align="right">'.
'<img src="./img/icons/email.png" style="border: 0px solid black; vertical-align: middle;"> '.
'<b>'._('Confirmation email sent?').'</b> </td><td>';
if($entry['confirm_email'])
	echo _('yes');
else
{
	echo _('no');
}
echo ' (<a href="entry_confirm.php?entry_id='.$entry['entry_id'].'">'._('Send confirmation').'</a>)';

echo '</td></tr>'.chr(10);
echo '<tr><td align="right">'.
'<img src="./img/icons/group.png" style="border: 0px solid black; vertical-align: middle;"> '.
'<b>'._('Customer').':</b> </td><td>';
if($entry['customer_id'] == '0')
	echo '<i>'._('Non selected').'</i>';
else
	echo '<a href="customer.php?customer_id='.$entry['customer_id'].'">'.$entry['customer_name'].'</a> ('._('Customer ID').' '.$entry['customer_id'].')';
echo '</td></tr>'.chr(10);

echo '<tr><td align="right"><b>'._('Contact person').':</b> </td><td>'.$entry['contact_person_name'].'</td></tr>'.chr(10);
echo '<tr><td align="right"><b>'._('Contact telephone').':</b> </td><td>'.$entry['contact_person_phone'].'</td></tr>'.chr(10);
echo '<tr><td align="right"><b>'._('Contact persons email').':</b> </td><td>'.$entry['contact_person_email'].'</td></tr>'.chr(10);

echo '<tr><td align="right">'.
'<img src="./img/icons/map.png" style="border: 0px solid black; vertical-align: middle;"> '.
'<b>'._('Municipal').':</b> </td><td>'.$entry['customer_municipal'].'</td></tr>'.chr(10);

$user_created = getUser($entry['created_by']);
if(count($user_created))
	echo '<tr><td align="right"><b>'._('Booking created by').':</b> </td><td>'.$user_created['user_name'].'</td></tr>'.chr(10);

echo '<br>';

echo '<tr>
	<td align="right">'.iconHTML('user_small').' <b>'._('Number of children').':</b> </td>
	<td>'.$entry['num_person_child'].'</td>
</tr>'.chr(10);

echo '<tr>
	<td align="right">'.iconHTML('user_suit').' <b>'._('Number of adults').':</b> </td>
	<td>'.$entry['num_person_adult'].'</td>
</tr>'.chr(10);

echo '<tr>
	<td align="right"><b>'._('Count in booking').':</b> </td>
	<td>';
	if($entry['num_person_count'] == '1') echo _('Yes');
	else echo _('No');
	echo '</td>
</tr>'.chr(10);

echo '<tr>
	<td align="right">'.iconHTML('package').' <b>'._('Fixed program').':</b> </td>
	<td>';
	if($entry['program_id'] == '0')
		echo _('Non selected');
	else
	{
		$program = getProgram($entry['program_id']);
		if(count($program))
			echo $program['program_name'];
	}
	echo '</td>
</tr>'.chr(10);

echo '<tr>
	<td align="right">'.iconHTML('script').' <b>'._('Program description').':</b> </td>
	<td>'.nl2br($entry['program_description']).'</td>
</tr>'.chr(10);

echo '<tr>
	<td align="right">'.iconHTML('drink').' <b>'._('Service description').':</b> </td>
	<td>'.$entry['service_description'].'</td>
</tr>'.chr(10);

echo '<tr>
	<td align="right">'.iconHTML('comment').' <b>'._('Comment').':</b> </td>
	<td>';
	if($entry['comment'] == '')
		echo '<i>Ingen kommentar</i>';
	else
		echo nl2br($entry['comment']);
	echo '</td>
</tr>'.chr(10);

echo '<tr>
	<td align="right"><b>'._('Serve alcohol?').':</b> </td>
	<td>';
	if($entry['service_alco'])
		echo _('Yes');
	else
		echo _('No');
	echo '</td>
</tr>'.chr(10);

echo '<tr>
	<td align="right">'.iconHTML('monitor').' <b>'._('Text on infoscreen').':</b> </td>
	<td>'.$entry['infoscreen_txt'].'</td>
</tr>'.chr(10);


echo '<tr>
	<td align="right">'.iconHTML('monitor').' <b>'._('Preview infoscreen').':</b> </td>
	<td><a href="infoskjerm.php?area='.$entry['area_id'].'&amp;date='.date('d.m.Y', $entry['time_start']).'">'._('Preview this evening for the current area').' *</td>
</tr>'.chr(10);

echo '<tr>
	<td colspan="2">* '._('Will only show if the event is after 16.00.').'</td>
</tr>'.chr(10);

echo '<tr><td colspan="2"><br>'.chr(10);
echo '<font size="3"><b>'._('Invoice').'</b></font>'.chr(10);
echo '</td></tr>'.chr(10).chr(10);

echo '<tr>
	<td align="right">'.iconHTML('coins').' <b>'._('Invoice').'?</b> </td>
	<td>';
	if($entry['invoice'])
		echo _('yes');
	else
		echo _('no');
	echo '</td>
</tr>'.chr(10);

echo '<tr>
	<td align="right"><b>'._('Invoice status').':</b> </td>
	<td>';
	switch ($entry['invoice_status'])
	{
		case '0':	echo _('not to be made');	break;
		case '1':
			echo 'skal lages, ikke klar';
			echo ' (<a href="invoice_setready.php?entry_id='.$entry['entry_id'].'">set til faktureringsklar</a>)';
		break;
		case '2':	echo 'skal lages, klar til fakturering';	break;
		case '3':	echo 'faktura laget og sendt';	break;
		case '4':	echo 'betalt';	break;
			
	}
		
	echo '</td>
</tr>'.chr(10);

echo '<tr>
	<td align="right"><b>'._('Comment').':</b> </td>
	<td>';
	echo nl2br($entry['invoice_comment']);
echo '</td>
</tr>'.chr(10);

echo '<tr>
	<td align="right"><b>'._('Internal comment').':</b> </td>
	<td>';
	echo nl2br($entry['invoice_internal_comment']);
echo '</td>
</tr>'.chr(10);

echo '<tr>
	<td align="right"><b>'._('E-delivery').':</b> </td>
	<td>';
	if($entry['invoice_electronic'] == '1')
		echo _('Yes');
	else
		echo _('No');
echo '</td>
</tr>'.chr(10);

echo '<tr>
	<td align="right"><b>'._('E-mail').':</b> </td>
	<td>';
	echo $entry['invoice_email'];
echo '</td>
</tr>'.chr(10);

echo '<tr>
	<td align="right"><b>'._('Address').':</b> </td>
	<td>';
	if($entry['invoice_address_id'] == 0)
		echo '<i>'._('Non selected').'</i>';
	else
	{
		$address = getAddress ($entry['invoice_address_id']);
		if(count($address))
		{
			echo nl2br($address['address_full']);
		}
		else
			echo _('Address not found');
	}
	echo '</td>
</tr>'.chr(10);

echo '<tr>
	<td align="right"><b>'._('Your referance').':</b> </td>
	<td>';
	echo $entry['invoice_ref_your'];
	echo '</td>
</tr>'.chr(10);

echo '<tr>
	<td align="right"><b>'._('Product lines').':</b> </td>
	<td>';
	$mva			= array();
	$mva_grunnlag	= array();
	$faktura_belop_sum = 0;
	$faktura_belop_sum_mva = 0;
	if(!count($entry['invoice_content']))
		echo '<i>'._('Non').'</i>';
	else
	{
		echo '<table width="700" style="border-collapse: collapse;">'.chr(10);
		echo ' <tr>'.chr(10);
		echo '  <td class="border"><b>Linjenr</b></td>'.chr(10);
		echo '  <td class="border"><b>Beskrivelse</b></td>'.chr(10);
		echo '  <td class="border"><b>Stk.pris</b></td>'.chr(10);
		echo '  <td class="border"><b>Antall</b></td>'.chr(10);
		echo '  <td class="border"><b>Sum&nbsp;eks.mva</b></td>'.chr(10);
		echo '  <td class="border"><b>MVA-sats</b></td>'.chr(10);
		echo '  <td class="border"><b>Sum&nbsp;ink.mva</b></td>'.chr(10);
		echo ' </tr>'.chr(10);
		
		foreach ($entry['invoice_content'] as $linjenr => $vars)
		{
			//$vars['belop_sum_netto']	= $vars['belop_hver'] * $vars['antall'];
			//$vars['belop_sum']			= $vars['belop_sum_netto'] * (1 + $vars['mva']);
			//$vars['mva_sum']			= $vars['belop_sum'] - $vars['belop_sum_netto'];
			
			$faktura_belop_sum_mva	+= $vars['mva_sum'];
			$faktura_belop_sum		+= $vars['belop_sum'];
			
			$vars['mva'] *= 100;
			echo ' <tr>'.chr(10);
			echo '  <td class="border" align="center">'.$linjenr.'</td>'.chr(10);
			echo '  <td class="border" align="center">'.nl2br($vars['name']).'</td>'.chr(10);
			echo '  <td class="border" align="right">kr&nbsp;'.$vars['belop_hver'].'</td>'.chr(10);
			echo '  <td class="border" align="center">'.$vars['antall'].'</td>'.chr(10);
			echo '  <td class="border" align="right">kr&nbsp;'.$vars['belop_sum_netto'].'</td>'.chr(10);
			echo '  <td class="border" align="center">'.$vars['mva'].'&nbsp;%</td>'.chr(10);
			echo '  <td class="border" align="right"><b>kr&nbsp;'.$vars['belop_sum'].'</b></td>'.chr(10);
			echo ' </tr>'.chr(10);
			
			if($vars['mva'] > 0)
			{
				if(isset($mva[$vars['mva']]))
					$mva[$vars['mva']] += $vars['mva_sum'];
				else
					$mva[$vars['mva']] = $vars['mva_sum'];
				
				if(isset($mva_grunnlag[$vars['mva']]))
					$mva_grunnlag[$vars['mva']] += $vars['belop_sum_netto'];
				else
					$mva_grunnlag[$vars['mva']] = $vars['belop_sum_netto'];
			}
		}
		
		echo '</table>'.chr(10);
	}
	echo '</td>
</tr>'.chr(10);

if(count($mva))
{
	echo '<tr>
<td align="right"><b>'._('Tax basis').':</b> </td>
<td>';
	echo '<table style="border-collapse: collapse;">'.chr(10);
	echo ' <tr>'.chr(10);
	echo '  <td class="border">MVA-%</td>'.chr(10);
	echo '  <td class="border">Grunnlag</td>'.chr(10);
	echo '  <td class="border">MVA</td>'.chr(10);
	echo ' </tr>'.chr(10);
	$eks_mva_tot = 0;
	foreach ($mva as $mvaen => $mva_delsum)
	{
		$eks_mva = $mva_grunnlag[$mvaen];
		$eks_mva_tot += $eks_mva;
		echo ' <tr>'.chr(10);
		echo '  <td class="border" align="right">'.$mvaen.'&nbsp;%</td>'.chr(10);
		echo '  <td class="border" align="right">kr&nbsp;'.$eks_mva.'</td>'.chr(10);
		echo '  <td class="border" align="right">kr&nbsp;'.$mva_delsum.'</td>'.chr(10);
		echo ' </tr>'.chr(10);
	}
	echo ' <tr>'.chr(10);
	echo '  <td class="border" align="right">SUM&nbsp;MVA</td>'.chr(10);
	echo '  <td class="border" align="right">kr&nbsp;'.$eks_mva_tot.'</td>'.chr(10);
	echo '  <td class="border" align="right">kr&nbsp;'.$faktura_belop_sum_mva.'</td>'.chr(10);
	echo ' </tr>'.chr(10);
	echo '</table>'.chr(10);
		
	echo '</td>
</tr>'.chr(10);
}

echo '<tr>
	<td align="right"><b>Sum å betale:</b> </td>
	<td>NOK '.$faktura_belop_sum.'</td>
</tr>'.chr(10);

echo '<tr>
	<td colspan="2"> - '.
	'<a href="entry_invoice.php?entry_id='.$entry['entry_id'].'">'.
	'Vis som fakturagrunnlag</a></td>
</tr>'.chr(10);
/*
if($login['user_invoice_setready'])
{
	echo '<tr>
	<td colspan="2"> - '.
	'<a href="invoice_setready.php?entry_id='.$entry['entry_id'].'">'.
	'Sett at fakturastatus til "Klar til fakturering"</a></td>'.chr(10).
	'</tr>'.chr(10);
}*/
if($login['user_invoice'])
{
	echo '<tr>
	<td colspan="2"> - '.
	'<a href="invoice_create.php?entry_ids=;'.$entry['entry_id'].';">'.
	'Opprett faktura med utgangspunkt i denne bookingen</a></td>'.chr(10).
	'</tr>'.chr(10);
}
echo '</table>';

echo '<br>';
echo '<table class="hiddenprint"><tr><td>';
echo '<h2>'._('Changelog').'</h2>'.chr(10);
echo '<b>'._('Number of changes').':</b> '.$entry['rev_num'].'<br>'.chr(10);
$user_edit = getUser($entry['user_last_edit']);
if(count($user_edit))
	echo '<b>'._('Last changed by').':</b> '.$user_edit['user_name'].' ('.date('H:i:s d-m-Y', $entry['time_last_edit']).')<br>'.chr(10);


echo '
<script type="text/javascript">

function changeText(el, newText) {
	// Safari work around
	if (el.innerText) {
		el.innerText = newText;
	} else if (el.firstChild && el.firstChild.nodeValue) {
		el.firstChild.nodeValue = newText;
	}
}

function switchView(id) {
	var toc = document.getElementById(\'log\' + id);
	if(toc)
	{
		toc = toc.getElementsByTagName(\'ul\')[0];
		var toggleLink = document.getElementById(\'switchlink\' + id);
	
		if (toc && toggleLink && toc.style.display == \'none\') {
			changeText(toggleLink, tocHideText);
			toc.style.display = \'block\';
		} else {
			changeText(toggleLink, tocShowText);
			toc.style.display = \'none\';
		}
	}
}

var tocShowText = "'._('show').'";
var tocHideText = "'._('hide').'";

</script>';
$entry_log = getEntryLog($entry['entry_id'], true);
echo '<br><br><table>'.chr(10);
echo ' <tr>'.chr(10);
echo '  <td><b>'._('Revision').'</b></td>'.chr(10);
echo '  <td><b>'._('Time').'</b></td>'.chr(10);
echo '  <td><b>'._('Action').'</b></td>'.chr(10);
echo '  <td><b>'._('Who').'</b></td>'.chr(10);
echo '  <td><b>'._('What').'</b></td>'.chr(10);
echo ' </tr>'.chr(10);
foreach($entry_log as $thislog)
{
	echo ' <tr>'.chr(10);
	echo '  <td><b>'.$thislog['rev_num'].'</td>'.chr(10);
	echo '  <td>'.str_replace(" ", "&nbsp;", date('Y-m-d H:i:s', $thislog['log_time'])).'</td>'.chr(10);
	
	// Action
	echo '  <td>'.str_replace(" ", "&nbsp;", printEntryLog($thislog, false, true)).'</td>'.chr(10);
	
	// Who
	$user = getUser($thislog['user_id']);
	if(count($user))
		echo '  <td>'.str_replace(" ", "&nbsp;", $user['user_name']).'</td>'.chr(10);
	else
		echo '  <td>&nbsp;</td>'.chr(10);
	
	// What
	echo '  <td>';
	echo '<a href="javascript:switchView('.$thislog['log_id'].');" id="switchlink'.$thislog['log_id'].'">'._("hide").'</a>'.chr(10);
	echo '<div id="log'.$thislog['log_id'].'">'.chr(10);
	echo ' <ul>'.chr(10);
	echo printEntryLog($thislog, TRUE);
	echo ' </ul>'.chr(10);
	echo '</div>'.chr(10);
	echo '<script type="text/javascript">switchView('.$thislog['log_id'].');</script>'.chr(10);
	echo '</td>'.chr(10);
	
	echo ' </tr>'.chr(10);
}
echo '</table>'.chr(10);

echo '</td></tr></table>';
?>