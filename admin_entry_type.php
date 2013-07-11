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

include "include/admin_top.php";
require "libs/editor.class.php";
$section = 'entry_type';

if(isset($_GET['editor']))
{
	if(!$login['user_access_entrytypeadmin'])
	{
		showAccessDenied($day, $month, $year, $area, true);
		exit ();
	}
	
	$id = 0;
	if(isset($_GET['id']) && is_numeric($_GET['id']))
		$id = (int)$_GET['id'];
	if(isset($_POST['id']) && is_numeric($_POST['id']))
		$id = (int)$_POST['id'];
	
	if($id <= 0)
	{
		$editor = new editor('entry_type', $_SERVER['PHP_SELF'].'?editor=1');
		$editor->setHeading(__('New entrytype'));
		$editor->setSubmitTxt(__('Add'));
	}
	else
	{
		$editor = new editor('entry_type', $_SERVER['PHP_SELF'].'?editor=1', $id);
		$editor->setHeading(__('Change entrytype'));
		$editor->setSubmitTxt(__('Change'));
	}
	
	$editor->setDBFieldID('entry_type_id');
	$editor->showID (TRUE);
	
	$editor->makeNewField('entry_type_name', __('Entrytype name'), 'text');
	$editor->makeNewField('entry_type_name_short', __('Short entrytype name'), 'text');
	$editor->makeNewField('resourcenum_length', _h('Length of resource number').'<br />('._h('If zero, resource number will not be required').')', 'text');
	
	$editor->getDB();
	
	if(isset($_POST['editor_submit']))
	{
		if($editor->input($_POST))
		{
			if($editor->performDBquery())
			{
				// Redirect
				header('Location: admin_entry_type.php');
				exit();
			}
			else
			{
				echo 'Error occured while performing query on database:<br>'.chr(10),
				//echo '<b>Error:</b> '.$editor->error();
				exit();
			}
		}
	}
	
	include "include/admin_middel.php";
	$editor->printEditor();
}
elseif(isset($_GET['entry_type_id']) && isset($_GET['area_id']))
{
	$entry_type = getEntryType($_GET['entry_type_id']);
	if(!count($entry_type))
	{
		echo 'Finner ikke typen.';
		exit();
	}
	$area = getArea($_GET['area_id']);
	if(!count($area))
	{
		echo __('Can\'t find the area.');
		exit();
	}
	
	$entry_type_defaultattachment = getEntryTypeDefaultAttachment($_GET['entry_type_id'], $_GET['area_id']);
	
	$saved = false;
	if(isset($_POST['attSave']))
	{
		if(!isset($_POST['attachment']) || !is_array($_POST['attachment']))
			$_POST['attachment'] = array();
		// Accept the changes in attachments
		$att_new = array();
		$att_deleted = $entry_type_defaultattachment;
		foreach($_POST['attachment'] as $att_id)
		{
			$att = getAttachment($att_id);
			if(count($att))
			{
				if(!isset($entry_type_defaultattachment[$att['att_id']]))
					$att_new[$att['att_id']] = $att;
				else
					unset($att_deleted[$att['att_id']]);
			}
		}
		
		foreach($att_deleted as $att)
		{
			mysql_query("
			DELETE
			FROM `entry_type_defaultattachment`
			WHERE
				entry_type_id = '".$entry_type['entry_type_id']."' AND 
				area_id = '".$area['area_id']."' AND
				att_id = '".$att['att_id']."';
			");
		}
		foreach($att_new as $att)
		{
			mysql_query("
			INSERT
			INTO `entry_type_defaultattachment`
			(
				`entry_type_id` ,
				`att_id` ,
				`area_id`
			)
			VALUES (
				'".$entry_type['entry_type_id']."',
				'".$att['att_id']."',
				'".$area['area_id']."'
			);
			");
		}
		$saved = true;
		
		$entry_type_defaultattachment = getEntryTypeDefaultAttachment($_GET['entry_type_id'], $_GET['area_id']);
	}
	include "include/admin_middel.php";
	
	echo '<h2>Endre vedlegg for '.$area['area_name'].' - '.$entry_type['entry_type_name'].'</h2>';
	
	echo '<form action="'.
	$_SERVER['PHP_SELF'].'?entry_type_id='.$entry_type['entry_type_id'].
	'&amp;area_id='.$area['area_id'].'" method="POST">'.chr(10);
	echo '<input type="hidden" name="attSave" value="1">'.chr(10);
	echo 'Filene m&aring; lastes opp fra egen side under <i>Administrasjon</i><br><br>';
	
	echo '<h2>Vedlegg valgt:</h2>';
	if($saved)
		echo '<div class="notice">Vedleggene er n&aring; lagret og vil automatisk valgt ved bookinger med dette programmet.</div>'.chr(10);
	echo '<input type="button" id="velgVedlegg" class="ui-button ui-state-default ui-corner-all" value="Velg fil(er)">';
	echo '<div style="border:2px solid #DDDDDD; margin-bottom:1em; padding:0.8em; margin-top:1em;">';
	echo '<div id="noAttachmentsSelected" style="display: none; padding: 5px;"><i>Ingen vedlegg valgt</i></div>';
	echo '<ul id="vedlegg">';
	foreach($entry_type_defaultattachment as $att)
	{
		echo '<li id="vedleggValgt'.$att['att_id'].'">'.
			'<input type="hidden" value="'.$att['att_id'].'" name="attachment[]"/>'.
			iconFiletype($att['att_filetype']).' '.$att['att_filename_orig'].' ('.smarty_modifier_file_size($att['att_filesize']).')'.
			'<input type="button" class="attSelected" style="font-size: 10px;" value="Fjern" onclick="removeAttachment('.$att['att_id'].');"/>'.
			'</li>';
	}
	echo '</ul>';
	echo '</div>';
	
	echo '<input type="submit" value="Lagre" class="ui-button ui-state-default ui-corner-all">';
	echo '</form>';
	
	echo '<script src="js/jquery-1.3.2.min.js" type="text/javascript"></script>'.chr(10);
	require "include/attachmentSelector.php";
}
else
{
	// List with programs
	
	include "include/admin_middel.php";
	
	echo '<script src="js/jquery-1.3.2.min.js" type="text/javascript"></script>'.chr(10);
	echo '<script src="js/hide_unhide.js" type="text/javascript"></script>'.chr(10);
	echo '<h2>Bookingtyper</h2>'.chr(10).chr(10);
	$Q_programs = mysql_query("select * from `entry_type` order by entry_type_name");
	
	if($login['user_access_entrytypeadmin'])
		echo '<a href="'.$_SERVER['PHP_SELF'].'?editor=1">'.
		iconHTML('page_white_stack_add').' '.__('New entrytype').'</a><br><br>'.chr(10);
		
	echo '<a href="javascript:void();" class="showAll">Vis vedlegg p&aring; alle / Ikke vis vedlegg p&aring; alle</a>';
	echo '<table class="prettytable">'.chr(10).chr(10);
	echo '	<tr>'.chr(10);
	echo '		<th>'.__('ID').'</th>'.chr(10);
	echo '		<th>'.__('Entrytype').'</th>'.chr(10);
	echo '		<th>'.__('Short name').'</th>'.chr(10);
	echo '		<th>Automatisk vedlegg ved bekreftelse</th>'.chr(10);
	if($login['user_access_entrytypeadmin'])
		echo '		<th>'.__('Options').'</th>'.chr(10);
	echo '	</tr>'.chr(10).chr(10);
	while($ROW = mysql_fetch_assoc($Q_programs))
	{
		echo '	<tr>'.chr(10);
		echo '		<td><b>'.$ROW['entry_type_id'].'</b></td>'.chr(10);
		echo '		<td>'.iconHTML('page_white_stack').' '.$ROW['entry_type_name'].'</td>'.chr(10);
		echo '		<td>'.$ROW['entry_type_name_short'].'</td>'.chr(10);
		
		
		// Attachment
		echo '		<td>';
		
		echo '<div class="showButton" id="buttonId'.$ROW['entry_type_id'].'">';
			//<a href="javascript:switchView('.$ROW['entry_type_id'].');">vis</a>';
		echo '<a href="javascript:void();">Vis / ikke vis</a>'.
			'</div>';
		echo '<div class="showField" id="fieldId'.$ROW['entry_type_id'].'" style="display:none;">';
		$Q_area = mysql_query("select id as area_id, area_name from `mrbs_area` order by area_name");
		while($area = mysql_fetch_assoc($Q_area))
		{
			echo '<ul style="padding-left: 20px; "><li>';
			echo '<b>'.$area['area_name'].'</b>';
			echo ' (<a href="'.
					$_SERVER['PHP_SELF'].'?entry_type_id='.$ROW['entry_type_id'].
					'&amp;area_id='.$area['area_id'].'">'.
				iconHTML('page_white_stack_link','.png','height: 12px;').' '.
				'Endre</a>)';
			echo '<br><ul style="padding-left: 10px; ">';
			$Q_att = mysql_query("
			SELECT
				a.att_filetype, a.att_filename_orig, a.att_filesize, a.att_id, e.area_id
			FROM `entry_type_defaultattachment` e LEFT JOIN `entry_confirm_attachment` a 
				ON e.att_id = a.att_id
			WHERE
				e.entry_type_id = '".$ROW['entry_type_id']."' AND
				e.area_id = '".$area['area_id']."'
			ORDER BY a.att_filename_orig");
			if(!mysql_num_rows($Q_att))
				echo '<li><i>Ingen vedlegg koblet til</i></li>';
			else
			{
				while($att = mysql_fetch_assoc($Q_att)) {
					echo '<li><a href="admin_attachment.php?att_id='.$att['att_id'].'">'.
						iconFiletype($att['att_filetype']).' '.$att['att_filename_orig'].
						'</a>'.
						' ('.smarty_modifier_file_size($att['att_filesize']).')</li>';
				}
			}
			echo '</ul></li></ul>';
		}
		echo '</div>';
		echo '</td>';
		
		
		echo '		<td>';
		if($login['user_access_entrytypeadmin'])
			echo '<a href="'.$_SERVER['PHP_SELF'].'?editor=1&amp;id='.$ROW['entry_type_id'].'">'.
			iconHTML('page_white_edit').' '.__('Edit').' -:- ';
		
		echo '</td>'.chr(10);
		echo '	</tr>'.chr(10).chr(10);
	}
}

?>