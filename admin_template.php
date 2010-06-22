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
	Editor for templates
	- Started 11.07.2008
*/

$section = 'template';

include "include/admin_top.php";
require "libs/invoice.class.php";

$template_types = array(
	'confirm' => _('E-mail confirmation')
);

$vars_entry = array(
		'entry_id'					=> _('Entry ID'),
		'entry_name'				=> _('Entry name'),
		'entry_title'				=> _('Entry title'),
		'confirm_email2'			=> _('E-mail confirmed').' '._('(no/yes)'),
		'confirm_email'				=> _('E-mail confirmed').' '._('(0/1)'),
		'entry_type_id'				=> _('Entry type ID'),
		'entry_type'				=> _('Entry type'),
		'num_person_child'			=> _('Number of children'),
		'num_person_adult'			=> _('Number of adults'),
		'num_person_count'			=> _('Count in booking').' '._('(0/1)'),
		'num_person_count2'			=> _('Count in booking').' '._('(no/yes)'),
		'program_id'				=> 'Fast program - id',
		'program_id_name'			=> 'Fast program - navn',
		'program_id_desc'			=> 'Fast program - beskrivelse',
		'program_description'		=> _('Program description'),
		'service_alco'				=> _('Serve alcohol?'),
		'service_description'		=> _('Service description'),
		'comment'					=> _('Comment'),
		'infoscreen_txt'			=> _('Text on infoscreen'),
		'time_start'				=> _('Starts').' '._('Unixtime*'),
		'time_end'					=> _('Finished').' '._('Unixtime*'),
		'room_id'					=> _('Room ID'),
		'room'						=> _('Room'),
		'rooms'						=> _('Room').' ('._('array with the names').')',
		'area_id'					=> _('Area ID'),
		'area'						=> _('Area'),
		'user_assigned'				=> _('User(s) assigned'),
		'user_assigned2'			=> _('Manuel user assigned'),
		'user_assigned_names'		=> _('User(s) and manual user assigned'),
		'user_assigned_names2'		=> _('User(s) and manual user assigned with links'),
		'user_assigned_any'			=> _('Any users assigned?').' ('._('true/false').')',
		'customer_id'				=> _('Customer ID'),
		'customer_name'				=> _('Customer'),
		'contact_person_name'		=> _('Contact person'),
		'contact_person_phone'		=> _('Contact telephone'),
		'contact_person_email'		=> _('Contact email'),
		'customer_municipal_num'	=> _('Municipal number'),
		'customer_municipal'		=> _('Municipal'),
		'created_by'				=> _('Created by ID'),
		'created_by_name'			=> _('Created by'),
		'time_created'				=> _('Time created').' '._('Unixtime*'),
		'edit_by'					=> _('Edited by IDs'),
		'edit_by_names'				=> _('Edited by'),
		'user_last_edit'			=> _('Last edited by'),
		'time_last_edit'			=> _('Time of last edit').' '._('Unixtime*'),
		'rev_num'					=> _('Number of changes'),
		'invoice'					=> _('Invoice').' '._('(0/1)'),
		'invoice2'					=> _('Invoice').' '._('(no/yes)'),
		'invoice_status'			=> _('Status of invoice').' (0 - 4)',
		'invoice_status2'			=> _('Status in text'),
		'invoice_electronic'		=> _('Electronic invoice').' '._('(0/1)'),
		'invoice_electronic2'		=> _('Electronic invoice').' '._('(no/yes)'),
		'invoice_email'				=> _('E-mail for electronic invoice'),
		'invoice_comment'			=> _('Invoice comment - to customer'),
		'invoice_internal_comment'	=> _('Invoice comment - internal'),
		'invoice_ref_your'			=> _('Your referance'),
		'invoice_address_id'		=> _('Invoice address ID'),
		'invoice_address'			=> _('Invoice address'),
		'invoice_content'			=> _('Product lines').' '._('array'),
		'mva'						=> 'MVA-tabell, vise? '._('(no/yes)'),
		'mva'						=> 'MVA-tabell',
		'mva_grunnlag'				=> 'MVA-tabell, grunnlag/eks mva',
		'mva_grunnlag_sum'			=> 'MVA-grunnlag (sum)',
		'faktura_belop_sum'			=> 'Sum å betale',
		'faktura_belop_sum_mva'		=> 'Sum MVA',
		'eks_mva_tot'				=> 'Sum eks mva',
		'grunnlag_mva_tot'			=> 'MVA-grunnlag mva'
	);
$vars_system = array (
		'systemurl'					=> _('Address for the system'),
		'user_name'					=> _('Username') .' '. _('For logged in user'),
		'user_name_short'			=> _('Short username'),
		'user_email'				=> _('Users email') .' '. _('For logged in user')
	);
$vars_entrychanges = array(
		'log_time'					=> _('Time of change').' '._('Unixtime*'),
		'log_action_real'			=> _('Name of the action'),
		'log_user_id'				=> _('User that changed ID'),
		'log_user'					=> _('User that changed'),
		'log_changes'				=> _('Changes').' '._('Table*')
	);
$invoice = new invoice ();
$vars_invoice = array();
foreach ($invoice->variables_to_template as $thisvar) {
	$vars_invoice[$thisvar] = '...';
}
	$vars_invoice['invoice_id'] = 'Fakturaid';
	$vars_invoice['invoice_time_created'] = 'Opprettelsestid'.' '._('Unixtime*');
	$vars_invoice['invoice_time'] = 'Fakturadato'.' '._('YYYYDDMM*');
	$vars_invoice['invoice_time2'] = 'Fakturadato'.' '._('array-time*');
	$vars_invoice['invoice_time_due'] = 'Forfallsdato'.' '._('YYYYDDMM*');
	$vars_invoice['invoice_time_due2'] = 'Forfallsdato'.' '._('array-time*');
	$vars_invoice['invoice_time_payed'] = 'Tidspunkt betalt'.' '._('Unixtime*');
	$vars_invoice['invoice_payment_info'] = '(Ikke i bruk)';
	$vars_invoice['invoice_content'] = 'Produktinnhold'.' '._('array');
	$vars_invoice['invoice_topay_total'] = 'Å betalte';
	$vars_invoice['invoice_topay_total_net'] = 'Netto-beløp';
	$vars_invoice['invoice_topay_total_tax'] = 'MVA-beløp';
	$vars_invoice['invoice_payed'] = 'Betalt'.' '._('(0/1)');
	$vars_invoice['invoice_payed2'] = 'Betalt'.' '._('(no/yes)');
	$vars_invoice['invoice_to_line1'] = 'Adresselinje 1';
	$vars_invoice['invoice_to_line2'] = 'Adresselinje 2';
	$vars_invoice['invoice_to_line3'] = 'Adresselinje 3';
	$vars_invoice['invoice_to_line4'] = 'Adresselinje 4';
	$vars_invoice['invoice_to_line5'] = 'Adresselinje 5';
	$vars_invoice['invoice_to_line6'] = 'Adresselinje 6';
	$vars_invoice['invoice_to_line7'] = 'Adresselinje 7';
	$vars_invoice['invoice_to_lines'] = 'Adresselinjer (hele adresse)';
	$vars_invoice['invoice_to_email'] = 'E-postadresse';

$Q_template = mysql_query("select * from `template` order by `template_name`");
$temp = array();
while($R_tpl = mysql_fetch_assoc($Q_template))
{
	$a = '';
	switch ($R_tpl['template_type'])
	{
		case 'confirm':	$a = _('Confirmation');	break;
		default:		$a = _('Unknown');		break;
	}
	$temp[$R_tpl['template_type']]['db:'.$R_tpl['template_id']] = array(
		$a.' - '.$R_tpl['template_name'], 
		'db:'.$R_tpl['template_id'],
		array_merge (
			$vars_entry,
			$vars_system),
		$R_tpl['template_type'],
		'layout.png',
		true,
		''
	);
}

$allowed_templatefiles = array();
$allowed_templatefiles['db:new'] = array(
		_('New template'), 
		'db:new',
		array_merge (
			$vars_entry,
			$vars_system),
		'',
		'layout_add.png',
		false,
		''
	);

foreach ($temp as $a)
{
	foreach ($a as $b => $c)
		$allowed_templatefiles[$b] = $c;
}

$allowed_templatefiles['mail-entry-new.tpl'] = array(
		_('Mail - New entry'), 
		'mail-entry-new.tpl',
		array_merge (
			$vars_entry,
			$vars_system),
		'sys',
		'layout.png',
		false,
		'user_access_templateadmin'
	);
$allowed_templatefiles['mail-entry-newuser.tpl'] = array(
		_('Mail - New user assigned'), 
		'mail-entry-newuser.tpl',
		array_merge (
			$vars_entrychanges,
			$vars_entry,
			$vars_system),
		'sys',
		'layout.png',
		false,
		'user_access_templateadmin'
	);
$allowed_templatefiles['mail-entry-userdeleted.tpl'] = array(
		_('Mail - User not assigned anymore'), 
		'mail-entry-userdeleted.tpl',
		array_merge (
			$vars_entrychanges,
			$vars_entry,
			$vars_system),
		'sys',
		'layout.png',
		false,
		'user_access_templateadmin'
	);
$allowed_templatefiles['mail-entry-changes.tpl'] = array(
		_('Mail - Changes of entry'), 
		'mail-entry-changes.tpl',
			array_merge (
				$vars_entrychanges,
				$vars_entry,
				$vars_system),
		'sys',
		'layout.png',
		false,
		'user_access_templateadmin'
	);
$allowed_templatefiles['mail-entry-summary.tpl'] = array(
		_('Mail - Summary of entry'), 
		'mail-entry-summary.tpl',
		array_merge (
			$vars_entry,
			$vars_system),
		'sys',
		'layout.png',
		false,
		'user_access_templateadmin'
	);
$allowed_templatefiles['invoice.tpl'] = array(
		_('Invoice'), 
		'invoice.tpl',
		array_merge (
			$vars_invoice,
			$vars_system),
		'sys',
		'layout.png',
		false,
		'user_access_templateadmin'
	);
$allowed_templatefiles['fakturagrunnlag.tpl'] = array(
		'Fakturagrunnlag', 
		'fakturagrunnlag.tpl',
		array_merge (
			$vars_entry,
			$vars_entrychanges,
			$vars_system),
		'sys',
		'layout.png',
		false,
		'user_access_templateadmin'
	);

if(!isset($_GET['template_file']))
{
	// List of files
	include "include/admin_middel.php";
	
	echo '<h1>'._('Templates').'</h1>';
	echo '<table class="prettytable">'.chr(10);
	echo '	<tr>'.chr(10);
	echo '		<th>Mal</th>'.chr(10);
	echo '		<th>Valg</th>'.chr(10);
	echo '	</tr>'.chr(10);
	foreach ($allowed_templatefiles as $template)
	{
		echo '	<tr>'.chr(10);
		if($template[1] == 'db:new')
			$namelink = true;
		else
			$namelink = false;
		
		if($namelink && ($template[6] != '' && $login[$template[6]]))
			echo '<td>'.
				'<img src="./img/icons/'.$template[4].'" style="border: 0px solid black; vertical-align: middle;"> '.
				'<a href="'.$_SERVER['PHP_SELF'].'?template_file='.$template[1].'">'.$template[0].'</a>'.
				'</td>';
		else
		{
			echo '		<td>'.
			'<img src="./img/icons/'.$template[4].'" style="border: 0px solid black; vertical-align: middle;"> ';
			if($template[6] != '' && $login[$template[6]])
				echo '<a href="'.$_SERVER['PHP_SELF'].'?template_file='.$template[1].'&amp;view=1">'.
					$template[0].'</a></td>'.chr(10);
			else
				echo $template[0].'</td>'.chr(10);
		}
		echo '		<td>';
		if(!$namelink && ($template[6] != '' && $login[$template[6]]))
			echo '<a href="'.$_SERVER['PHP_SELF'].'?template_file='.$template[1].'">'.
				iconHTML('layout_edit').' '._('Edit').'</a>';
		else
			echo '&nbsp;';
		if($template[5] && ($template[6] != '' && $login[$template[6]]))
		{
			echo ' -:- <a href="'.$_SERVER['PHP_SELF'].'?template_delete=1&amp;template_file='.$template[1].'">'.
			' <img src="./img/icons/layout_delete.png" style="border: 0px solid black; vertical-align: middle;"> '.
			_('Delete').'</a>';
		}
		else
			echo '&nbsp;';
		echo '</td>'.chr(10);
		echo '	</tr>'.chr(10).chr(10);
	}
	exit();
}

// Checking filename
if(!array_key_exists($_GET['template_file'], $allowed_templatefiles))
{
	include "include/admin_middel.php";
	echo '<h1>'._('Templates').'</h1>';
	
	echo _('Error. Template not found.');
	exit();
}

$template = $allowed_templatefiles[$_GET['template_file']];

if(!isset($_GET['view']) && !($template[6] != '' && $login[$template[6]]))
{
	showAccessDenied($day, $month, $year, $area, true);
	exit ();
}
	
if(substr($_GET['template_file'], 0, 3) != 'db:')
	$tpl_db = false;
else
	$tpl_db = true;

// Exists?
if($tpl_db)
{
	$id = substr($_GET['template_file'], 3);
	if($id != 'new')
	{
		if(!is_numeric($id))
		{
			include "include/admin_middel.php";
			echo '<h1>'._('Templates').'</h1>';
			
			echo _('Error: Template ID is not a number.');
			exit();
		}
		
		$id = (int)$id;
		$Q_tpl = mysql_query("select template_id from `template` where template_id = '$id'");
		if(!mysql_num_rows($Q_tpl))
		{
			include "include/admin_middel.php";
			echo '<h1>'._('Templates').'</h1>';
			
			echo _('Error: Template was not found.');
			exit();
		}
	}
}
else
{
	$filename = 'templates/'.$_GET['template_file'];
	if(!file_exists($filename))
	{
		include "include/admin_middel.php";
		echo '<h1>'._('Templates').'</h1>';
		
		echo _('Error: Template file do not exist.');
		exit();
	}
}

if($template[6] != '' && $login[$template[6]])
{
	// Delete
	if($tpl_db && isset($_GET['template_delete']))
	{
		if($_GET['template_delete'] != '2')
		{
			include "include/admin_middel.php";
			echo '<h1>'._('Delete template').' - '.$template[0].'</h1>';
			echo '- <a href="'.$_SERVER['PHP_SELF'].'">'._('Back to template list').'</a><br><br>'.chr(10);
			
			echo '<b>'._('Are you sure you want to delete the template?').'</b><br>'.chr(10);
			echo '<a href="'.$_SERVER['PHP_SELF'].'?template_delete=2&amp;template_file='.$template[1].'">'.
			_('Yes').'</a> - ';
			echo '<a href="'.$_SERVER['PHP_SELF'].'">'.
			_('No').'</a>';
			exit();
		}
		else
		{
			mysql_query("DELETE FROM `template` WHERE `template_id` = $id LIMIT 1");
			header('Location: '.$_SERVER['PHP_SELF']);
			exit();
		}
	}
	
	// Saving file, if we are going to...
	if(isset($_POST['template_txt']))
	{
		// Remove bad stuff
		$txt = slashes(htmlspecialchars($_POST['template_txt'],ENT_QUOTES));
		
		if($tpl_db)
		{
			// Work against DB
			if(!isset($_POST['template_type']) || !array_key_exists($_POST['template_type'], $template_types))
			{
				include "include/admin_middel.php";
				echo '<h1>'._('Templates').'</h1>';
				
				echo _('Error: No template type is defined.');
				exit();
			}
			
			if(!isset($_POST['template_name']) || $_POST['template_name'] == '')
			{
				include "include/admin_middel.php";
				echo '<h1>'._('Templates').'</h1>';
				
				echo _('Error: No template name is made.');
				exit();
			}
			$template_name = slashes(htmlspecialchars(strip_tags($_POST['template_name']),ENT_QUOTES));
			
			if($id == 'new')
			{
				// Insert
				mysql_query("INSERT INTO `template` (
					`template_id` ,
					`template` ,
					`template_name` ,
					`template_type`,
					`template_time_last_edit`
				)
				VALUES (
					NULL , 
					'$txt', 
					'$template_name', 
					'".$_POST['template_type']."',
					'".time()."'
				);");
			}
			else
			{
				// Update
				mysql_query("UPDATE `template` SET 
					`template` = '$txt',
					`template_name` = '$template_name',
					`template_type` = '".$_POST['template_type']."',
					`template_time_last_edit` = '".time()."'
				WHERE `template_id` =$id LIMIT 1 ;");
			}
		}
		else
		{
			$fp = @fopen($filename, "w");
			if ($fp) {
				fwrite($fp, htmlspecialchars_decode($txt, ENT_QUOTES));
				fclose($fp);
			}
			else {
				include "include/admin_middel.php";
				echo '<h1>'._('Templates').'</h1>';
				
				echo _('Error: Could not save template file.');
				exit();
			}
		}
		
		header('Location: '.$_SERVER['PHP_SELF']);
		exit();
	}
}

// Preview
if(isset($_GET['preview']))
{
	$entry = getEntry($_GET['entry_id']);
	if(count($entry))
	{
		$smarty = new Smarty;
		
		templateAssignEntry('smarty', $entry);
		templateAssignEntryChanges('smarty', $entry, $entry['rev_num']);
		templateAssignSystemvars('smarty');
		echo nl2br($smarty->fetch($template[1]));
		exit();
	}
}


// Getting file
if($tpl_db && $id != 'new')
{
	$Q_tpl = mysql_query("select template, template_name from `template` where template_id = '$id'");
	if(mysql_num_rows($Q_tpl))
	{
		$template_txt	= mysql_result($Q_tpl, 0, 'template');
		$template_name	= mysql_result($Q_tpl, 0, 'template_name');
	}
	else
	{
		$template_txt	= _('Error with getting the template. Try again.');
		$template_name	= $template[0];
	}
}
elseif($tpl_db)
{
	// New template
	$template_txt	= '';
	$template_name	= '';
}
else
{
	$template_txt = htmlspecialchars(file_get_contents($filename),ENT_QUOTES);
	//$template_txt = file_get_contents($filename);
	if(!$template_txt && $template_txt != '')
	{
		include "include/admin_middel.php";
		echo '<h1>'._('Templates').'</h1>';
		
		echo _('Error: Could not read template file.');
		exit();
	}
}

include "include/admin_middel.php";
echo '<h1>'._('Templates').' - '.$template[0].'</h1>';
echo '- <a href="'.$_SERVER['PHP_SELF'].'">'._('Back to template list').'</a><br><br>'.chr(10);

if(($template[6] != '' && $login[$template[6]]) || (isset($_GET['view']) && $_GET['view'] == '1'))
{
	echo _('The template in its original form:').'<br>';
	echo '<textarea cols="75" rows="20" name="template_txt">'.$template_txt.'</textarea>'.chr(10);
	echo '<br><br>';
	
	echo '<b>'._('Preview').' / '._('Test').' '._('againts an entry').'</b>'.chr(10);
	echo '<form method="get" action="'.$_SERVER['PHP_SELF'].'">'.chr(10);
	echo '<input type="hidden" name="preview" value="1">'.chr(10);
	echo '<input type="hidden" name="template_file" value="'.$template[1].'">'.chr(10);
	echo '<input type="text" name="entry_id"> - '._('Entry ID').' ('._('Must be valid').')<br>'.chr(10);
	echo '<input type="submit" value="'._('Preview').'"'.chr(10);
	echo '</form>'.chr(10);
}
else
{
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?template_file='.$template[1].'">'.chr(10);
	echo '<table><tr>'.chr(10);
	echo '<td><textarea cols="75" rows="20" name="template_txt">'.$template_txt.'</textarea>'.chr(10);
	echo '<br><br>';
	
	if(!$tpl_db)
	{
		echo '<input type="text" value="'.$template[0].'" disabled="disabled"> - '._('Template name').'<br>'.chr(10).
		'<input type="text" value="'.$template[3].'" disabled="disabled"> - '._('Template type').'<br>'.chr(10);
	}
	else
	{
		echo '<input name="template_name" type="text" value="'.$template_name.'"> - '._('Template name').'<br>'.chr(10);
		echo '<select name="template_type">'.chr(10);
		foreach ($template_types as $ttype => $tname)
		{
			echo '<option value="'.$ttype.'"';
			if($ttype == $template[3])
				echo ' selected="selected"';
			echo '>'.$tname.'</option>'.chr(10);
		}
		echo '</select>';
		echo ' - '._('Template type').'<br>'.chr(10);
	}
	
	echo '<input type="submit" value="'._('Save').'">'.chr(10);
	echo '</form>';
	
	
	echo '<h2>'._('How to make templates').'</h2>';
	echo str_replace('Smarty', '<a href="http://www.smarty.net/">Smarty</a>', 
		_('The template system is using Smarty. Please se Smarty\'s own descriptions on how to make templates. Click on the variables to the right to insert them in the template you are writing on.'));
	echo '<br><br>'.chr(10);
	echo str_replace('Smartys documentation about date_format', 
		'<a href="http://www.smarty.net/manual/en/language.modifier.date.format.php">'.
		'Smartys documentation about date_format</a>',
		_('*Unixtime is a given second of time and can be converted to a readable format (like Monday 15:30 04.12.2000). To do this write |date_format:"%A %H:%M %d.%m.%Y" after the variabel (like {$log_time|date_format:"%M"}). Please see Smartys documentation about date_format for different datevalues.'));
	
	echo '<br><br>'.chr(10);
	echo str_replace('Smartys documentation about foreach', 
		'<a href="http://www.smarty.net/manual/en/language.function.foreach.php">'.
		'Smartys documentation about foreach</a>',
		_('*Table means that you can not just print it. Please see the Smartys documentation about foreach for how to print a table.'));
	
	echo '<br><br>'.chr(10);
	echo _('*Array-time means that the time is returned in an array with the day, month and year. Access by typing in {$something.year}, {$something.month} and {$something.day}.');
	
	echo '<br><br>'.chr(10);
	echo _('*YYYYMMDD means that the time is in the format year month day, without spaces.');
	
	echo '</td>'.chr(10);
	echo '<td><b>'._('Variables:').'</b><br>';
	echo '<table>'.chr(10);
	foreach ($template[2] as $var => $txt) {
		echo '<tr><td><span onclick="insertTags (\'{$'.$var.'}\', \'\', \'\');">{$'.$var.'}</span></td><td>&nbsp;-&nbsp;<i>'.$txt.'</i></td></tr>';
	}
	echo '</table>'.chr(10);
	echo '</td>'.chr(10);
	echo '</tr></table>'.chr(10);
	
	// Function from Wikipedia
	echo chr(10).chr(10).
	'<script type="text/javascript">
	function insertTags(tagOpen, tagClose, sampleText) {
		var txtarea;
		if (document.editform) {
			txtarea = document.editform.wpTextbox1;
		} else {
			// some alternate form? take the first one we can find
			var areas = document.getElementsByTagName(\'textarea\');
			txtarea = areas[0];
		}
		var selText, isSample = false;
	
		if (document.selection  && document.selection.createRange) { // IE/Opera
	
			//save window scroll position
			if (document.documentElement && document.documentElement.scrollTop)
				var winScroll = document.documentElement.scrollTop
			else if (document.body)
				var winScroll = document.body.scrollTop;
			//get current selection  
			txtarea.focus();
			var range = document.selection.createRange();
			selText = range.text;
			//insert tags
			checkSelectedText();
			range.text = tagOpen + selText + tagClose;
			//mark sample text as selected
			if (isSample && range.moveStart) {
				if (window.opera)
					tagClose = tagClose.replace(/\n/g,\'\');
				range.moveStart(\'character\', - tagClose.length - selText.length); 
				range.moveEnd(\'character\', - tagClose.length); 
			}
			range.select();   
			//restore window scroll position
			if (document.documentElement && document.documentElement.scrollTop)
				document.documentElement.scrollTop = winScroll
			else if (document.body)
				document.body.scrollTop = winScroll;
	
		} else if (txtarea.selectionStart || txtarea.selectionStart == \'0\') { // Mozilla
	
			//save textarea scroll position
			var textScroll = txtarea.scrollTop;
			//get current selection
			txtarea.focus();
			var startPos = txtarea.selectionStart;
			var endPos = txtarea.selectionEnd;
			selText = txtarea.value.substring(startPos, endPos);
			//insert tags
			checkSelectedText();
			txtarea.value = txtarea.value.substring(0, startPos)
				+ tagOpen + selText + tagClose
				+ txtarea.value.substring(endPos, txtarea.value.length);
			//set new selection
			if (isSample) {
				txtarea.selectionStart = startPos + tagOpen.length;
				txtarea.selectionEnd = startPos + tagOpen.length + selText.length;
			} else {
				txtarea.selectionStart = startPos + tagOpen.length + selText.length + tagClose.length;
				txtarea.selectionEnd = txtarea.selectionStart;
			}
			//restore textarea scroll position
			txtarea.scrollTop = textScroll;
		} 
	
		function checkSelectedText(){
			if (!selText) {
				selText = sampleText;
				isSample = true;
			} else if (selText.charAt(selText.length - 1) == \' \') { //exclude ending space char
				selText = selText.substring(0, selText.length - 1);
				tagClose += \' \'
			} 
		}
	
	}
	</script>
	';
}

?>