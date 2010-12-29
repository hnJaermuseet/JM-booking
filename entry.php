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

debugAddToLog(__FILE__, __LINE__, 'Start of entry.php');

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

debugAddToLog(__FILE__, __LINE__, 'Getting entry');
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

debugAddToLog(__FILE__, __LINE__, 'Marking entry as read');
//readEntry ($entry['entry_id'], $entry['rev_num']);

$smarty = new Smarty;

templateAssignEntry('smarty', $entry);
templateAssignSystemvars('smarty');

debugAddToLog(__FILE__, __LINE__, 'Displaying template (entry_view.tpl)');
$smarty->display('file:entry_view.tpl');


debugAddToLog(__FILE__, __LINE__, 'Getting entry log');
$entry_log = getEntryLog($entry['entry_id'], true);
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
	if($thislog['log_action2'] != 'invoice_readyfor' && 
	$thislog['log_action2'] != 'invoice_exported' && 
	$thislog['log_action2'] != 'invoice_payed')
	{
		echo '<a href="javascript:switchView('.$thislog['log_id'].');" id="switchlink'.$thislog['log_id'].'">'._("hide").'</a>'.chr(10);
		echo '<div id="log'.$thislog['log_id'].'">'.chr(10);
		echo ' <ul>'.chr(10);
		echo printEntryLog($thislog, TRUE);
		echo ' </ul>'.chr(10);
		echo '</div>'.chr(10);
		echo '<script type="text/javascript">switchView('.$thislog['log_id'].');</script>'.chr(10);
	}
	echo '</td>'.chr(10);
	
	echo ' </tr>'.chr(10);
}
echo '</table>'.chr(10);

echo '</td></tr></table>';

debugAddToLog(__FILE__, __LINE__, 'entry.php finished');
debugPrintLog();
?>