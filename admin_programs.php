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
$section = 'programs';

if(isset($_GET['editor']))
{
	if(!$login['user_access_programadmin'])
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
		$editor = new editor('programs', $_SERVER['PHP_SELF'].'?editor=1');
		$editor->setHeading(_('New fixed program'));
		$editor->setSubmitTxt(_('Add'));
	}
	else
	{
		$editor = new editor('programs', $_SERVER['PHP_SELF'].'?editor=1', $id);
		$editor->setHeading(_('Change fixed program'));
		$editor->setSubmitTxt(_('Change'));
	}
	
	$editor->setDBFieldID('program_id');
	$editor->showID (TRUE);
	
	$editor->makeNewField('program_name', _('Program name'), 'text');
	$editor->makeNewField('program_desc', 'Beskrivelse', 'textarea');
	$editor->makeNewField('area_id', _('Area belonging'), 'select',
		array('defaultValue' => $area));
	$Q_area = mysql_query("select id as area_id, area_name from `mrbs_area` order by `area_name`");
	while($R_area = mysql_fetch_assoc($Q_area))
		$editor->addChoice('area_id', $R_area['area_id'], $R_area['area_name']);
	$editor->makeNewField('program_inactive', _l('Inactive'), 'select');
		$editor->addChoice('program_inactive', 0, _l('No'));
		$editor->addChoice('program_inactive', 1, _l('Yes'));
	
	$editor->getDB();
	
	if(isset($_POST['editor_submit']))
	{
		if($editor->input($_POST))
		{
			if($editor->performDBquery())
			{
				// Redirect
				header('Location: admin_programs.php');
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
elseif(isset($_GET['program_id']))
{
	$program = getProgram($_GET['program_id']);
	if(!count($program))
	{
		echo 'Finner ikke programmet.';
		exit();
	}
	
	$program_defaultattachment = getProgramDefaultAttachment($_GET['program_id']);
	
	$saved = false;
	if(isset($_POST['attSave']))
	{
		if(!isset($_POST['attachment']) || !is_array($_POST['attachment']))
			$_POST['attachment'] = array();
		// Accept the changes in attachments
		$att_new = array();
		$att_deleted = $program_defaultattachment;
		foreach($_POST['attachment'] as $att_id)
		{
			$att = getAttachment($att_id);
			if(count($att))
			{
				if(!isset($program_defaultattachment[$att['att_id']]))
					$att_new[$att['att_id']] = $att['att_id'];
				else
					unset($att_deleted[$att['att_id']]);
			}
		}
		
		foreach($att_deleted as $att)
		{
			mysql_query("
			DELETE
			FROM `programs_defaultattachment`
			WHERE
				program_id = '".$program['program_id']."' AND 
				att_id = '".$att['att_id']."';
			");
			echo mysql_error();
		}
		foreach($att_new as $att)
		{
			mysql_query("
			INSERT
			INTO `programs_defaultattachment`
			(
				`program_id` ,
				`att_id`
			)
			VALUES (
				'".$program['program_id']."',
				'".$att['att_id']."'
			);
			");
		}
		$saved = true;
		
		$program_defaultattachment = getProgramDefaultAttachment($_GET['program_id']);
	}
	include "include/admin_middel.php";
	
	echo '<h2>Endre vedlegg for '.$program['program_name'].'</h2>';
	
	echo '<form action="'.$_SERVER['PHP_SELF'].'?program_id='.$program['program_id'].'" method="POST">'.chr(10);
	echo '<input type="hidden" name="attSave" value="1">'.chr(10);
	echo 'Filene må lastes opp fra egen side under <i>Administrasjon</i><br><br>';
	
	echo '<h2>Vedlegg valgt:</h2>';
	if($saved)
		echo '<div class="notice">Vedleggene er nå lagret og vil automatisk valgt ved bookinger med dette programmet.</div>'.chr(10);
	echo '<input type="button" id="velgVedlegg" class="ui-button ui-state-default ui-corner-all" value="Velg fil(er)">';
	echo '<div style="border:2px solid #DDDDDD; margin-bottom:1em; padding:0.8em; margin-top:1em;">';
	echo '<div id="noAttachmentsSelected" style="display: none; padding: 5px;"><i>Ingen vedlegg valgt</i></div>';
	echo '<ul id="vedlegg">';
	foreach($program_defaultattachment as $att)
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
	
	echo '<h2>'._('Fixed programs').'</h2>'.chr(10).chr(10);
	$Q_programs = mysql_query("select * from `programs` order by program_name");
	
	if($login['user_access_programadmin'])
		echo '<a href="'.$_SERVER['PHP_SELF'].'?editor=1">'.
		iconHTML('package_add').' '._('New fixed program').'</a><br><br>'.chr(10);
	
	echo '<table class="prettytable">'.chr(10).chr(10);
	echo '	<tr>'.chr(10);
	echo '		<th>'._('ID').'</th>'.chr(10);
	echo '		<th>'._('Program name').'</th>'.chr(10);
	echo '		<th>Beskrivelse</th>'.chr(10);
	echo '		<th>'._('Area').'</th>'.chr(10);
	echo '		<th>Vedlegg</th>'.chr(10);
	echo '		<th>'._('Options').'</th>'.chr(10);
	echo '	</tr>'.chr(10).chr(10);
	while($R_program = mysql_fetch_assoc($Q_programs))
	{
		echo '	<tr'.($R_program['program_inactive']?' class="strike graytext"':'').'>'.chr(10);
		echo '		<td><b>'.$R_program['program_id'].'</b></td>'.chr(10);
		echo '		<td>'.
			iconHTML('package').' '.
			$R_program['program_name'].
		'</td>'.chr(10);
		echo '		<td>'.nl2br($R_program['program_desc']).'</td>'.chr(10);
		echo '		<td style="white-space: nowrap;">';
		$Q_area = mysql_query("select * from `mrbs_area` where id = '".$R_program['area_id']."'");
		if(!mysql_num_rows($Q_area))
			echo '<i>'._('Not found').'</i>';
		else
			echo iconHTML('house').' '.mysql_result($Q_area, 0, 'area_name');
		echo '</td>'.chr(10);
		
		// Attachments
		echo '		<td>';
		$Q_att = mysql_query("
			SELECT
				a.att_filetype, a.att_filename_orig, a.att_filesize, a.att_id
			FROM `programs_defaultattachment` p LEFT JOIN `entry_confirm_attachment` a 
				ON p.att_id = a.att_id
			WHERE
				p.program_id = '".$R_program['program_id']."'
			ORDER BY a.att_filename_orig");
		while($att = mysql_fetch_assoc($Q_att)) {
			echo '<a href="admin_attachment.php?att_id='.$att['att_id'].'">'.
				iconFiletype($att['att_filetype']).' '.$att['att_filename_orig'].
				'</a>'.
				' ('.smarty_modifier_file_size($att['att_filesize']).')<br>';
		}
		echo '</td>'.chr(10);
		
		
		// Options
		echo '		<td style="white-space: nowrap;">';
		if($login['user_access_programadmin'])
		{
			echo '<a href="'.$_SERVER['PHP_SELF'].'?editor=1&amp;id='.$R_program['program_id'].'">'.
				iconHTML('package_go').' '.
				_('Edit').'</a> -:- ';
		}
		
		echo '<a href="'.$_SERVER['PHP_SELF'].'?program_id='.$R_program['program_id'].'">'.
				iconHTML('package_link').' '.
				'Endre vedlegg</a>';
		
		echo '</td>'.chr(10);
		
		
		echo '	</tr>'.chr(10).chr(10);
	}
}

?>
